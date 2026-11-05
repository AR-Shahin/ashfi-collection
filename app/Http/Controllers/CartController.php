<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function show()
    {
        return view('pages.cart', ['lines' => $this->cart->lines(), 't' => $this->cart->totals()]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product' => ['required', 'string'],
            'size' => ['nullable', 'string', 'max:20'],
            'color' => ['nullable', 'string', 'max:40'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:'.Cart::maxQty()],
            'buy_now' => ['nullable', 'boolean'],
        ]);
        $product = Product::active()->where('slug', $data['product'])->firstOrFail();
        $sizes = $product->sizeList();
        $colors = array_column($product->colorList(), 'name');

        $size = $data['size'] ?? (count($sizes) === 1 ? $sizes[0] : null);
        $color = $data['color'] ?? ($colors[0] ?? null);

        if ($sizes && ! in_array($size, $sizes, true)) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'needsOptions' => true, 'message' => 'Please choose your size to add this item.'], 422);
            }

            return redirect($product->url())->with('toast', ['message' => 'Please choose your size to add this item.', 'type' => 'error']);
        }
        if ($colors && ! in_array($color, $colors, true)) {
            $color = $colors[0];
        }

        $this->cart->add($product, $size, $color, (int) ($data['qty'] ?? 1));

        if (! empty($data['buy_now'])) {
            return $request->expectsJson()
                ? response()->json(['ok' => true, 'redirect' => route('checkout')])
                : redirect()->route('checkout');
        }

        return $this->done($request, 'Added to your cart', ['href' => route('cart'), 'linkText' => 'View cart']);
    }

    public function update(Request $request, string $key)
    {
        $qty = (int) $request->input('qty', 1);
        $this->cart->setQty($key, $qty);

        return $this->done($request, $qty < 1 ? 'Item removed from cart' : 'Cart updated', $this->fragment($request));
    }

    public function remove(Request $request, string $key)
    {
        $this->cart->remove($key);

        return $this->done($request, 'Item removed from cart', $this->fragment($request));
    }

    public function applyCoupon(Request $request)
    {
        $code = trim((string) $request->input('code'));
        $res = $code === '' ? ['ok' => false, 'message' => 'Enter a coupon code.'] : $this->cart->applyCoupon($code);

        return $this->done($request, $res['message'], $this->fragment($request), $res['ok']);
    }

    public function removeCoupon(Request $request)
    {
        $this->cart->removeCoupon();

        return $this->done($request, 'Coupon removed', $this->fragment($request));
    }

    /** Re-rendered cart/checkout summary so fetch callers can swap it in without a reload. */
    private function fragment(Request $request): array
    {
        if (! $request->expectsJson()) {
            return [];
        }
        $area = $request->input('area');
        $view = $request->input('view') === 'checkout' ? 'partials.checkout-summary' : 'partials.cart-body';
        $lines = $this->cart->lines();

        return ['html' => view($view, [
            'lines' => $lines,
            't' => $this->cart->totals($request->input('view') === 'checkout' ? (in_array($area, ['inside', 'outside'], true) ? $area : 'inside') : null),
        ])->render(), 'empty' => $lines->isEmpty()];
    }
}
