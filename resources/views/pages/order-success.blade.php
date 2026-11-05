@extends('layouts.app')
@section('page', 'order-success')
@section('title', 'Order Confirmed')
@section('robots', 'noindex,nofollow')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Order Confirmed', null]], 'heading' => 'Thank You', 'lead' => 'Here are your order details.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container">
  <div class="confirm"><div class="success"><div class="success__icon"><x-ui.icon name="check" :size="32" /></div>
    <h2>Thank you, {{ str($order->customer_name)->before(' ') }}!</h2>
    <p>Your order number is <strong>{{ $order->number }}</strong>. Keep it to track your delivery. We’ll call you on {{ $order->phone }} to confirm before dispatch.</p>
    <div class="hero__cta"><a class="btn btn--primary" href="{{ route('track', ['order' => $order->number]) }}">Track Order</a><a class="btn btn--outline" href="{{ route('shop') }}">Continue Shopping</a></div></div>
    <div class="confirm__grid"><section class="panel"><h2>Items</h2>@include('partials.mini-lines', ['items' => $order->items])</section>
      <section class="summary" aria-label="Order total"><h2>Summary</h2>@include('partials.order.totals')<p class="sum-note">Payment: {{ $order->paymentLabel() }}</p>
        <h3 class="summary__h">Delivering to</h3>@include('partials.order.address', ['name' => $order->customer_name, 'address' => $order->address, 'district' => $order->district, 'phone' => $order->phone])</section></div>
  </div>
</div></main>
@endsection
