@php
    $nav = $navActive ?? match (true) {
        request()->routeIs('home') => 'home',
        request()->routeIs('shop') && (request('filter') === 'new' || request()->boolean('new')) => 'new',
        request()->routeIs('shop') && (request('filter') === 'best' || request()->boolean('best')) => 'best',
        request()->routeIs('shop', 'product.show') => 'shop',
        request()->routeIs('about') => 'about',
        request()->routeIs('contact') => 'contact',
        request()->routeIs('account.*', 'login', 'register') => 'account',
        default => '',
    };
    $links = [['home', route('home'), 'Home'], ['shop', route('shop'), 'Shop'], ['new', route('shop', ['new' => 1]), 'New Arrivals']];
@endphp
@if (! empty($announcements))
<div class="announcement"><div class="container"><p class="announcement__text" id="annText" aria-live="polite">{{ $announcements[0] }}</p></div></div>
@endif
<header class="site-header" id="siteHeader"><div class="container header__inner">
  <button type="button" class="icon-btn header__menu" data-open-drawer aria-label="Open menu" aria-controls="mobileDrawer" aria-expanded="false"><x-ui.icon name="menu" :size="24" /></button>
  <a class="logo" href="{{ route('home') }}" aria-label="Ashfi Collection home"><img class="logo__img" src="{{ asset('graphic-design/logo-horizontal-light.svg') }}" alt="Ashfi Collection" width="562" height="176"></a>
  <nav class="primary-nav" aria-label="Primary"><ul>
    @foreach ($links as [$key, $href, $label])
      <li><a class="nav-link" href="{{ $href }}" @if ($nav === $key) aria-current="page" @endif>{{ $label }}</a></li>
    @endforeach
    <li class="has-dropdown"><a class="nav-link" href="{{ route('home') }}#categories" aria-haspopup="true">Categories <x-ui.icon name="down" :size="14" /></a><ul class="dropdown">
      @foreach ($navCategories as $c)<li><a href="{{ route('shop', ['category' => $c->slug]) }}">{{ $c->name }}</a></li>@endforeach
    </ul></li>
    <li><a class="nav-link" href="{{ route('shop', ['best' => 1]) }}" @if ($nav === 'best') aria-current="page" @endif>Best Sellers</a></li>
    <li><a class="nav-link" href="{{ route('about') }}" @if ($nav === 'about') aria-current="page" @endif>About Us</a></li>
    <li><a class="nav-link" href="{{ route('contact') }}" @if ($nav === 'contact') aria-current="page" @endif>Contact</a></li>
  </ul></nav>
  <div class="header__actions">
    <button type="button" class="icon-btn" data-search-toggle aria-label="Search products" aria-expanded="false" aria-controls="searchPanel"><x-ui.icon name="search" :size="22" /></button>
    @auth
      <a class="icon-btn header__account is-on" href="{{ route('account.dashboard') }}" aria-label="My account"><x-ui.icon name="user" :size="22" /></a>
    @else
      <a class="icon-btn header__account" href="{{ route('login') }}" aria-label="Log in"><x-ui.icon name="user" :size="22" /></a>
    @endauth
    <a class="icon-btn header__wish" href="{{ route('wishlist') }}" aria-label="Wishlist"><x-ui.icon name="heart" :size="22" /><span class="count-badge" data-wish-count @if (! $wishCount) hidden @endif>{{ $wishCount }}</span></a>
    <a class="icon-btn" href="{{ route('cart') }}" aria-label="Shopping cart"><x-ui.icon name="bag" :size="22" /><span class="count-badge" data-cart-count @if (! $cartCount) hidden @endif>{{ $cartCount }}</span></a>
  </div></div>
  <div class="search-panel" id="searchPanel" hidden><div class="container">
    <form class="search-form" action="{{ route('shop') }}" method="get" role="search"><x-ui.icon name="search" />
      <input type="search" name="q" id="searchInput" placeholder="Search products…" autocomplete="off" aria-label="Search products">
      <button type="submit" class="btn btn--primary btn--sm">Search</button>
      <button type="button" class="icon-btn" data-search-toggle aria-label="Close search"><x-ui.icon name="x" /></button></form>
    <ul class="search-results" id="searchResults" aria-live="polite"></ul>
  </div></div>
</header>
