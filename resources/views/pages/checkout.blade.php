@extends('layouts.app')
@section('page', 'checkout')
@section('title', 'Checkout')
@section('robots', 'noindex,nofollow')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Cart', route('cart')], ['Checkout', null]], 'heading' => 'Checkout', 'lead' => 'Almost there – just a few details.'])
@endsection
@section('content')
@php $d = setting('delivery'); @endphp
<main id="main" class="page-body"><div class="container" id="checkoutRoot">
@if ($lines->isEmpty())
  <div class="empty"><x-ui.icon name="bag" :size="48" /><h2>Your cart is empty</h2><p>Add something beautiful to your cart before checking out.</p>
    <a class="btn btn--primary" href="{{ route('shop') }}">Browse the Shop</a></div>
@else
  <div class="checkout">
    <form id="checkoutForm" method="post" action="{{ route('checkout.store') }}" novalidate data-checkout>@csrf
      @guest
        <p class="notice"><x-ui.icon name="user" :size="18" /><span>Returning customer? <a class="text-link" href="{{ route('login', ['next' => '/checkout']) }}">Log in</a> for faster checkout, or continue as a guest.</span></p>
      @endguest
      @if ($errors->any())<p class="form-msg" role="alert">Please check the highlighted fields.</p>@endif
      <section class="panel"><h2>Delivery Details</h2><div class="form-grid">
        @if ($addresses->isNotEmpty())
          <div class="field"><label for="savedAddress">Saved address</label><select id="savedAddress" data-addresses="{{ $addresses->map->only(['id', 'name', 'phone', 'address', 'district'])->values()->toJson() }}">
            @foreach ($addresses as $a)<option value="{{ $a->id }}" @selected($a->id === $prefill['address_id'])>{{ $a->label }} – {{ $a->address }}, {{ $a->district }}</option>@endforeach
            <option value="">Use a new address</option></select></div>
        @endif
        <div class="form-grid form-grid--2">
          @include('partials.field', ['id' => 'name', 'name' => 'name', 'label' => 'Full name', 'value' => $prefill['name'], 'attrs' => 'autocomplete="name" required'])
          @include('partials.field', ['id' => 'phone', 'name' => 'phone', 'label' => 'Phone number', 'type' => 'tel', 'value' => $prefill['phone'], 'attrs' => 'inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" required'])
        </div>
        @include('partials.field', ['id' => 'email', 'name' => 'email', 'label' => 'Email <small>(optional, for order updates)</small>', 'type' => 'email', 'value' => $prefill['email'], 'attrs' => 'inputmode="email" autocomplete="email"'])
        @include('partials.field', ['id' => 'address', 'name' => 'address', 'label' => 'Full address', 'textarea' => true, 'value' => $prefill['address'], 'attrs' => 'autocomplete="street-address" placeholder="House, road, area" required'])
        <div class="form-grid form-grid--2">
          <div class="field"><label for="district">District</label><select id="district" name="district" required @error('district') aria-invalid="true" @enderror><option value="">Select district</option>
            @foreach (config('store.districts') as $dist)<option @selected($prefill['district'] === $dist)>{{ $dist }}</option>@endforeach
          </select><p class="field__error" id="districtErr">@error('district'){{ $message }}@enderror</p></div>
          <div class="field"><span class="label" id="areaLabel">Delivery area</span><div role="radiogroup" aria-labelledby="areaLabel">
            <label class="f-opt"><input type="radio" name="area" value="inside" @checked($area === 'inside')> Inside Dhaka <small>{{ money($d['insideDhaka']) }}</small></label>
            <label class="f-opt"><input type="radio" name="area" value="outside" @checked($area === 'outside')> Outside Dhaka <small>{{ money($d['outsideDhaka']) }}</small></label></div></div>
        </div>
        @include('partials.field', ['id' => 'note', 'name' => 'note', 'label' => 'Order note <small>(optional)</small>', 'textarea' => true, 'attrs' => 'placeholder="Anything we should know?"'])
        @auth
          <label class="f-opt" id="saveAddressRow" @if ($addresses->isNotEmpty()) hidden @endif><input type="checkbox" name="save_address" value="1" id="saveAddress" @checked(old('save_address'))> Save this address to my account</label>
        @endauth
      </div></section>
      <section class="panel"><h2>Payment Method</h2>
        <label class="pay-opt"><input type="radio" name="pay" value="cod" checked><span><strong>Cash on Delivery</strong><span>Pay in cash when your order arrives.</span></span></label>
        <label class="pay-opt pay-opt--off"><input type="radio" name="pay" disabled><span><strong>Online payment</strong><span>bKash, Nagad and cards – coming soon.</span></span></label></section>
      <button type="submit" class="btn btn--primary btn--block" style="min-height:52px">Place Order</button>
      <p class="field__hint" style="text-align:center">By placing your order you agree to our <a class="text-link" href="{{ route('terms') }}">Terms</a>.</p>
    </form>
    <aside class="summary" id="checkoutSummary" aria-label="Order summary" data-summary-root data-view="checkout" data-refresh="{{ route('checkout.summary') }}">
      @include('partials.checkout-summary')
    </aside>
  </div>
@endif
</div></main>
@endsection
