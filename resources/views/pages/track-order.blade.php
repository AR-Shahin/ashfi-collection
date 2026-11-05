@extends('layouts.app')
@section('page', 'track-order')
@section('title', 'Track Your Order')
@section('description', 'Track your Ashfi Collection order with your order number and mobile number.')
@if ($order)@section('robots', 'noindex,nofollow')@endif
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Track Order', null]], 'heading' => 'Track Your Order', 'lead' => 'Enter your details to see where your order is.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container"><div class="track">
  <form id="trackForm" class="panel form-grid" method="post" action="{{ route('track.lookup') }}" novalidate>@csrf
    <h2>Find your order</h2>
    @if (session('trackError'))<p class="form-msg" role="alert">{{ session('trackError') }}</p>@endif
    @include('partials.field', ['id' => 'trackNumber', 'name' => 'number', 'label' => 'Order number', 'value' => $order?->number ?? request('order'), 'attrs' => 'autocomplete="off" required placeholder="AC-XXXXXXXX" spellcheck="false" autocapitalize="characters"'])
    @include('partials.field', ['id' => 'trackPhone', 'name' => 'phone', 'label' => 'Mobile number used for the order', 'type' => 'tel', 'value' => $order ? request('phone') : '', 'attrs' => 'autocomplete="tel" required inputmode="tel" placeholder="01XXXXXXXXX"'])
    <button type="submit" class="btn btn--primary btn--block">Track Order</button>
    <p class="field__hint">Your order number is on your confirmation page. @auth You can also see every order under <a class="text-link" href="{{ route('account.orders') }}">My Orders</a>.@endauth</p>
  </form>
  <div id="trackResult" tabindex="-1">
    @if ($order)
      <section class="panel"><div class="order-card__head"><div><h2>{{ $order->number }}</h2><small>Placed {{ $order->created_at->format('j M Y') }}</small></div>@include('partials.order.pill')</div>
        @include('partials.order.timeline')<h3>Items</h3>@include('partials.mini-lines', ['items' => $order->items])@include('partials.order.totals')</section>
    @endif
  </div>
</div></div></main>
@endsection
