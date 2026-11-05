<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function show(Request $request)
    {
        $user = $request->user();
        $addresses = $user ? $user->addresses : collect();
        $default = $addresses->firstWhere('is_default', true) ?? $addresses->first();
        $district = old('district', $default?->district);
        $area = old('area', $district ? Cart::areaFor($district) : 'inside');

        return view('pages.checkout', [
            'lines' => $this->cart->lines(),
            't' => $this->cart->totals($area),
            'area' => $area,
            'addresses' => $addresses,
            'prefill' => [
                'name' => old('name', $default?->name ?? $user?->name),
                'phone' => old('phone', $default?->phone ?? $user?->phone),
                'email' => old('email', $user?->email),
                'address' => old('address', $default?->address),
                'district' => $district,
                'address_id' => $default?->id,
            ],
        ]);
    }

    /** Re-priced summary when the delivery area changes (app.js). */
    public function summary(Request $request)
    {
        $area = $request->query('area') === 'outside' ? 'outside' : 'inside';

        return response()->json(['html' => view('partials.checkout-summary', [
            'lines' => $this->cart->lines(), 't' => $this->cart->totals($area),
        ])->render()]);
    }

    public function store(CheckoutRequest $request)
    {
        $lines = $this->cart->lines();
        if ($lines->isEmpty()) {
            return redirect()->route('cart')->with('toast', ['message' => 'Your cart is empty.', 'type' => 'error']);
        }
        $data = $request->validated();
        // Prices are always recalculated here; nothing about money is trusted from the browser.
        $t = $this->cart->totals($data['area']);
        $user = $request->user();

        $order = DB::transaction(function () use ($data, $t, $lines, $user) {
            $order = Order::create([
                'user_id' => $user?->id,
                'customer_name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? $user?->email,
                'address' => $data['address'],
                'district' => $data['district'],
                'note' => $data['note'] ?? null,
                'delivery_area' => $data['area'],
                'payment_method' => 'cod',
                'subtotal' => $t->subtotal,
                'discount' => $t->discount,
                'delivery_charge' => $t->delivery,
                'total' => $t->total,
                'coupon_code' => $t->coupon && ! $t->couponMessage ? $t->coupon->code : null,
                'status' => 'placed',
            ]);
            foreach ($lines as $l) {
                $order->items()->create([
                    'product_id' => $l->product->id,
                    'name' => $l->product->name,
                    'image' => $l->product->image(),
                    'size' => $l->size,
                    'color' => $l->color,
                    'quantity' => $l->qty,
                    'unit_price' => $l->unit,
                ]);
            }
            if ($user && ! empty($data['save_address'])) {
                $user->addresses()->create([
                    'label' => 'Home', 'name' => $data['name'], 'phone' => $data['phone'],
                    'address' => $data['address'], 'district' => $data['district'],
                    'is_default' => ! $user->addresses()->exists(),
                ]);
            }

            return $order;
        });

        $this->cart->clear();
        // Lets a guest see their own confirmation page, and nobody else's.
        $request->session()->push('orders.placed', $order->number);

        return redirect()->route('order.success', $order);
    }

    public function success(Request $request, Order $order)
    {
        // Account orders: only their owner. Guest orders: only the browser session that placed them.
        $mine = $order->user_id
            ? $order->user_id === $request->user()?->id
            : in_array($order->number, $request->session()->get('orders.placed', []), true);
        abort_unless($mine, 404);

        return view('pages.order-success', ['order' => $order->load('items')]);
    }
}
