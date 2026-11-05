<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function show()
    {
        return view('pages.track-order', ['order' => null]);
    }

    /** Tracking needs the phone number too, so an order number alone reveals nothing. */
    public function track(Request $request)
    {
        $request->merge(['number' => strtoupper(trim((string) $request->input('number'))), 'phone' => normalize_phone($request->input('phone'))]);
        $request->validate([
            'number' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'regex:/^01[3-9]\d{8}$/'],
        ], [
            'number.required' => 'Please enter your order number.',
            'phone.*' => 'Enter the mobile number used for the order.',
        ]);

        $order = Order::with('items')->where('number', $request->input('number'))->where('phone', $request->input('phone'))->first();
        if (! $order) {
            return back()->withInput()->with('trackError', 'We couldn’t find an order with that number and mobile number. Please check both and try again.');
        }

        return view('pages.track-order', ['order' => $order]);
    }
}
