@extends('layouts.account')
@section('page', 'account')
@section('title', 'My Account')
@section('account')
<div class="stats account__stats">
  <a class="stat" href="{{ route('account.orders') }}"><strong>{{ $orderCount }}</strong><span>Total orders</span></a>
  <a class="stat" href="{{ route('account.orders', ['status' => 'active']) }}"><strong>{{ $activeCount }}</strong><span>In progress</span></a>
  <a class="stat" href="{{ route('wishlist') }}"><strong>{{ $wishCount }}</strong><span>Wishlist</span></a>
  <a class="stat" href="{{ route('account.addresses') }}"><strong>{{ $addressCount }}</strong><span>Addresses</span></a>
</div>
<section class="panel"><div class="panel__head"><h2>Recent Orders</h2>@if ($recent->isNotEmpty())<a class="link-arrow" href="{{ route('account.orders') }}">View all <x-ui.icon name="right" :size="16" /></a>@endif</div>
  @if ($recent->isNotEmpty())
    <div class="order-list">@foreach ($recent as $order)@include('partials.order.card')@endforeach</div>
  @else
    <div class="empty empty--sm"><x-ui.icon name="box" :size="40" /><p>You haven’t placed an order yet.</p><a class="btn btn--primary btn--sm" href="{{ route('shop') }}">Start Shopping</a></div>
  @endif
</section>
<div class="account__cols">
  <section class="panel"><div class="panel__head"><h2>Default Address</h2><a class="link-arrow" href="{{ route('account.addresses') }}">Manage <x-ui.icon name="right" :size="16" /></a></div>
    @if ($address)@include('partials.order.address', $address->only(['name', 'address', 'district', 'phone']))@else<p>No address saved yet.</p>@endif</section>
  <section class="panel"><div class="panel__head"><h2>Your Details</h2><a class="link-arrow" href="{{ route('account.profile') }}">Edit <x-ui.icon name="right" :size="16" /></a></div>
    <address class="addr">{{ $user->name }}<br>{{ $user->email }}<br>{{ $user->phone }}</address></section>
</div>
@endsection
