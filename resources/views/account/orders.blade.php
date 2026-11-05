@extends('layouts.account', ['crumbs' => [['My Account', route('account.dashboard')], ['My Orders', null]], 'heading' => 'My Orders', 'lead' => 'Track and manage your orders.'])
@section('page', 'account-orders')
@section('title', 'My Orders')
@section('account')
<div class="tabs" role="group" aria-label="Filter orders">
  @foreach (\App\Http\Controllers\AccountController::ORDER_GROUPS as $k => [$label])
    <a class="tab" href="{{ route('account.orders', $k === 'all' ? [] : ['status' => $k]) }}" aria-pressed="{{ $tab === $k ? 'true' : 'false' }}" @if ($tab === $k) aria-current="page" @endif>{{ $label }}</a>
  @endforeach
</div>
@if ($orders->isNotEmpty())
  <div class="order-list">@foreach ($orders as $order)@include('partials.order.card')@endforeach</div>
  {{ $orders->links() }}
@else
  <div class="empty"><x-ui.icon name="box" :size="44" /><h2>No orders here</h2>
    @if ($hasAny)<p>Try a different filter.</p>@else<p>When you place an order it will appear here.</p><a class="btn btn--primary" href="{{ route('shop') }}">Start Shopping</a>@endif</div>
@endif
@endsection
