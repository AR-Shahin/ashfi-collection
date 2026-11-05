@extends('layouts.app')
@section('robots', 'noindex,nofollow')
@section('banner')
  @include('partials.page-banner', ['crumbs' => $crumbs ?? [['My Account', null]], 'heading' => $heading ?? 'My Account', 'lead' => $lead ?? 'Your orders, addresses and details in one place.'])
@endsection
@section('content')
@php
    $items = [
        ['account.dashboard', 'grid', 'Dashboard', route('account.dashboard')],
        ['account.order*', 'box', 'My Orders', route('account.orders')],
        ['account.addresses*', 'pin', 'Addresses', route('account.addresses')],
        ['account.profile', 'user', 'Profile & Password', route('account.profile')],
        ['wishlist', 'heart', 'Wishlist', route('wishlist')],
    ];
@endphp
<main id="main" class="page-body"><div class="container"><div class="account">
  <nav class="account__nav" aria-label="My account">
    <p class="account__hello">Hello, <strong>{{ str(auth()->user()->name)->before(' ') }}</strong></p>
    <ul>
      @foreach ($items as [$pattern, $icon, $label, $href])
        <li><a href="{{ $href }}" @if (request()->routeIs($pattern)) aria-current="page" @endif><x-ui.icon :name="$icon" />{{ $label }}</a></li>
      @endforeach
      <li><form method="post" action="{{ route('logout') }}">@csrf<button type="submit"><x-ui.icon name="logout" />Log Out</button></form></li>
    </ul>
  </nav>
  <div class="account__main" id="accountRoot">@yield('account')</div>
</div></div></main>
@endsection
