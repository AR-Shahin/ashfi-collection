@extends('layouts.account', ['crumbs' => [['My Account', route('account.dashboard')], ['My Orders', route('account.orders')], [$order->number, null]], 'heading' => 'Order '.$order->number, 'lead' => 'Placed '.$order->created_at->format('j M Y')])
@section('page', 'account-order')
@section('title', 'Order '.$order->number)
@section('account')
<section class="panel"><div class="order-card__head"><div><h2>{{ $order->number }}</h2><small>Placed {{ $order->created_at->format('j M Y, g:i a') }}</small></div>@include('partials.order.pill')</div>@include('partials.order.timeline')</section>
<section class="panel"><h2>Items</h2>@include('partials.mini-lines', ['items' => $order->items])</section>
<div class="account__cols">
  <section class="panel"><h2>Delivery Address</h2>@include('partials.order.address', ['name' => $order->customer_name, 'address' => $order->address, 'district' => $order->district, 'phone' => $order->phone])
    @if ($order->note)<p><strong>Note:</strong> {{ $order->note }}</p>@endif</section>
  <section class="panel"><h2>Payment</h2>@include('partials.order.totals')<p class="sum-note">{{ $order->paymentLabel() }}</p></section>
</div>
<div class="account__actions">
  <form method="post" action="{{ route('account.order.reorder', $order) }}" data-ajax="toast">@csrf<button type="submit" class="btn btn--primary">Order Again</button></form>
  @if ($order->canCancel())
    <form method="post" action="{{ route('account.order.cancel', $order) }}" data-confirm="Cancel order {{ $order->number }}? This can’t be undone.">@csrf<button type="submit" class="btn btn--outline">Cancel Order</button></form>
  @endif
  <a class="btn btn--outline" href="{{ route('account.orders') }}">Back to My Orders</a>
</div>
@endsection
