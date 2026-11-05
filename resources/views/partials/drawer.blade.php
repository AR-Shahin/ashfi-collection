@php $phoneHref = preg_replace('/[^+\d]/', '', $brand['phone'] ?? ''); @endphp
<div class="overlay" data-close-drawer hidden></div>
<aside class="drawer" id="mobileDrawer" aria-label="Menu" aria-hidden="true" inert>
  <div class="drawer__head"><a class="logo" href="{{ route('home') }}"><img class="logo__img" src="{{ asset('images/brand/logo-horizontal.svg') }}" alt="Ashfi Collection" width="562" height="176"></a>
    <button type="button" class="icon-btn" data-close-drawer aria-label="Close menu"><x-ui.icon name="x" :size="22" /></button></div>
  <nav aria-label="Mobile"><ul class="drawer__list">
    <li><a class="nav-link" href="{{ route('home') }}">Home</a></li>
    <li><a class="nav-link" href="{{ route('shop') }}">Shop</a></li>
    <li><a class="nav-link" href="{{ route('shop', ['new' => 1]) }}">New Arrivals</a></li>
    <li><a class="nav-link" href="{{ route('shop', ['best' => 1]) }}">Best Sellers</a></li>
  </ul>
  <p class="drawer__label" id="drawerCats">Categories</p><ul class="drawer__list drawer__list--sub" aria-labelledby="drawerCats">
    @foreach ($navCategories as $c)<li><a href="{{ route('shop', ['category' => $c->slug]) }}">{{ $c->name }}</a></li>@endforeach
  </ul>
  <ul class="drawer__list">
    @auth<li><a class="nav-link" href="{{ route('account.dashboard') }}">My Account</a></li>@else<li><a class="nav-link" href="{{ route('login') }}">Log In / Register</a></li>@endauth
    <li><a class="nav-link" href="{{ route('wishlist') }}">Wishlist</a></li>
    <li><a class="nav-link" href="{{ route('track') }}">Track Order</a></li>
    <li><a class="nav-link" href="{{ route('offers') }}">Offers</a></li>
    <li><a class="nav-link" href="{{ route('about') }}">About Us</a></li>
    <li><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
  </ul></nav>
  <div class="drawer__foot"><a href="tel:{{ $phoneHref }}"><x-ui.icon name="phone" :size="18" /> {{ $brand['phone'] ?? '' }}</a>
    <div class="social"><a href="{{ $brand['facebook'] ?? '#' }}" target="_blank" rel="noopener" aria-label="Facebook"><x-ui.icon name="facebook" /></a>
    <a href="{{ $brand['instagram'] ?? '#' }}" target="_blank" rel="noopener" aria-label="Instagram"><x-ui.icon name="instagram" /></a></div></div>
</aside>
