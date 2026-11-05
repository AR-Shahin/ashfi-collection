@php $accountHref = auth()->check() ? route('account.dashboard') : route('login'); @endphp
<nav class="bottom-nav" aria-label="Quick navigation">
  <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif><x-ui.icon name="home" :size="22" /><span>Home</span></a>
  <a href="{{ route('shop') }}" @if (request()->routeIs('shop')) aria-current="page" @endif><x-ui.icon name="sliders" :size="22" /><span>Shop</span></a>
  <button type="button" data-open-drawer aria-controls="mobileDrawer"><x-ui.icon name="grid" :size="22" /><span>Categories</span></button>
  <a href="{{ $accountHref }}" @if (request()->routeIs('account.*', 'login', 'register')) aria-current="page" @endif><x-ui.icon name="user" :size="22" /><span>Account</span></a>
  <a href="{{ route('cart') }}" @if (request()->routeIs('cart')) aria-current="page" @endif><x-ui.icon name="bag" :size="22" /><span>Cart</span><i class="count-badge" data-cart-count @if (! $cartCount) hidden @endif>{{ $cartCount }}</i></a>
</nav>
