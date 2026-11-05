<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public const ORDER_GROUPS = [
        'all' => ['All', null],
        'active' => ['In Progress', ['placed', 'confirmed', 'shipped']],
        'delivered' => ['Delivered', ['delivered']],
        'cancelled' => ['Cancelled', ['cancelled']],
    ];

    public function dashboard(Request $request)
    {
        $user = $request->user();

        return view('account.dashboard', [
            'user' => $user,
            'orderCount' => $user->orders()->count(),
            'activeCount' => $user->orders()->whereIn('status', ['placed', 'confirmed', 'shipped'])->count(),
            'wishCount' => $user->wishlist()->count(),
            'addressCount' => $user->addresses()->count(),
            'recent' => $user->orders()->with('items')->take(3)->get(),
            'address' => $user->addresses()->first(),
        ]);
    }

    public function orders(Request $request)
    {
        $tab = array_key_exists($request->query('status'), self::ORDER_GROUPS) ? $request->query('status') : 'all';
        $statuses = self::ORDER_GROUPS[$tab][1];

        return view('account.orders', [
            'tab' => $tab,
            'hasAny' => $request->user()->orders()->exists(),
            'orders' => $request->user()->orders()->with('items')
                ->when($statuses, fn ($q) => $q->whereIn('status', $statuses))->paginate(10)->withQueryString(),
        ]);
    }

    public function order(Request $request, Order $order)
    {
        $this->own($request, $order);

        return view('account.order', ['order' => $order->load('items.product')]);
    }

    public function cancel(Request $request, Order $order)
    {
        $this->own($request, $order);
        if (! $order->canCancel()) {
            return $this->done($request, 'This order can no longer be cancelled. Please contact us.', ok: false);
        }
        $order->update(['status' => 'cancelled']);

        return $this->done($request, 'Order cancelled');
    }

    public function reorder(Request $request, Order $order, Cart $cart)
    {
        $this->own($request, $order);
        $added = 0;
        foreach ($order->items as $item) {
            $product = $item->product_id ? Product::active()->find($item->product_id) : null;
            if (! $product) {
                continue;
            }
            $size = in_array($item->size, $product->sizeList(), true) ? $item->size : null;
            if ($product->sizeList() && ! $size) {
                continue;
            }
            $cart->add($product, $size, $item->color, $item->quantity);
            $added++;
        }

        return $added
            ? $this->done($request, 'Items added to your cart', ['href' => route('cart'), 'linkText' => 'View cart'])
            : $this->done($request, 'These items are no longer available', ok: false);
    }

    /* ------------------------------------------------------- addresses */

    public function addresses(Request $request)
    {
        $user = $request->user();
        $editing = null;
        if ($request->filled('edit')) {
            $editing = $user->addresses()->findOrFail($request->query('edit'));
        } elseif ($request->boolean('new') || $request->session()->hasOldInput('address')) {
            $editing = new Address(['name' => $user->name, 'phone' => $user->phone]);
        }

        return view('account.addresses', ['addresses' => $user->addresses, 'editing' => $editing]);
    }

    public function storeAddress(Request $request)
    {
        $data = $this->validateAddress($request);
        $user = $request->user();
        $makeDefault = $request->boolean('is_default') || ! $user->addresses()->exists();
        $address = $user->addresses()->create($data + ['is_default' => false]);
        if ($makeDefault) {
            $this->setDefault($address);
        }

        return redirect()->route('account.addresses')->with('toast', ['message' => 'Address saved', 'type' => 'success']);
    }

    public function updateAddress(Request $request, Address $address)
    {
        $this->ownAddress($request, $address);
        $address->update($this->validateAddress($request));
        if ($request->boolean('is_default')) {
            $this->setDefault($address);
        }

        return redirect()->route('account.addresses')->with('toast', ['message' => 'Address saved', 'type' => 'success']);
    }

    public function defaultAddress(Request $request, Address $address)
    {
        $this->ownAddress($request, $address);
        $this->setDefault($address);

        return $this->done($request, 'Default address updated');
    }

    public function deleteAddress(Request $request, Address $address)
    {
        $this->ownAddress($request, $address);
        $wasDefault = $address->is_default;
        $address->delete();
        if ($wasDefault && ($next = $request->user()->addresses()->first())) {
            $this->setDefault($next);
        }

        return $this->done($request, 'Address deleted');
    }

    /* --------------------------------------------------------- profile */

    public function profile(Request $request)
    {
        return view('account.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->merge(['email' => strtolower(trim((string) $request->input('email'))), 'phone' => normalize_phone($request->input('phone'))]);
        $data = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'regex:/^01[3-9]\d{8}$/'],
        ], [
            'name.*' => 'Please enter your full name.',
            'email.unique' => 'Another account already uses this email.',
            'email.*' => 'Enter a valid email address.',
            'phone.*' => 'Enter a valid Bangladeshi mobile number.',
        ]);
        $user->update($data);

        return back()->with('toast', ['message' => 'Details saved', 'type' => 'success']);
    }

    public function updatePassword(Request $request)
    {
        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Enter your current password.',
            'current_password.current_password' => 'That isn’t your current password.',
            'password.confirmed' => 'Passwords don’t match.',
            'password.*' => 'Use at least 8 characters.',
        ]);
        $request->user()->update(['password' => Hash::make($request->input('password'))]);

        return back()->with('toast', ['message' => 'Password updated', 'type' => 'success']);
    }

    /* --------------------------------------------------------- helpers */

    private function own(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 404);
    }

    private function ownAddress(Request $request, Address $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 404);
    }

    private function setDefault(Address $address): void
    {
        Address::where('user_id', $address->user_id)->update(['is_default' => false]);
        $address->update(['is_default' => true]);
    }

    private function validateAddress(Request $request): array
    {
        $request->merge(['phone' => normalize_phone($request->input('phone'))]);

        return $request->validate([
            'label' => ['required', 'string', 'max:30'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'district' => ['required', Rule::in(config('store.districts'))],
            'address' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'label.*' => 'Add a label, such as Home.',
            'name.*' => 'Please enter a full name.',
            'phone.*' => 'Enter a valid Bangladeshi mobile number.',
            'district.*' => 'Please select a district.',
            'address.*' => 'Please enter the full address (house, road, area).',
        ]);
    }
}
