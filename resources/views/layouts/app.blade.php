@php
    $siteName = $brand['name'] ?? config('app.name');
    $pageTitle = trim($__env->yieldContent('title'));
    $fullTitle = $pageTitle ? $pageTitle.' | '.$siteName : $siteName.' | Kurti, Saree & Three Piece in Bangladesh';
    $description = trim($__env->yieldContent('description')) ?: 'Ashfi Collection – premium women\'s fashion in Bangladesh. Shop elegant kurti, two piece, three piece, saree and orna with nationwide delivery and Cash on Delivery.';
    $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
    $ogImage = trim($__env->yieldContent('og_image')) ?: asset('images/og-cover.png');
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $fullTitle }}</title>
  <meta name="description" content="{{ $description }}">
  <meta name="theme-color" content="#722F45">
  <meta name="robots" content="@yield('robots', 'index,follow')">
  <link rel="canonical" href="{{ $canonical }}">
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:site_name" content="{{ $siteName }}">
  <meta property="og:title" content="{{ $pageTitle ?: $fullTitle }}">
  <meta property="og:description" content="{{ $description }}">
  <meta property="og:image" content="{{ $ogImage }}">
  <meta property="og:url" content="{{ $canonical }}">
  <meta property="og:locale" content="en_BD">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
  <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
  <link rel="manifest" href="{{ asset('site.webmanifest') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600&family=Inter:wght@400;500;600;700&family=Pinyon+Script&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&display=swap" rel="stylesheet">
  @stack('preload')
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
  <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $siteName, 'url' => url('/'), 'logo' => asset('images/brand/logo-stacked.png'), 'sameAs' => array_values(array_filter([$brand['facebook'] ?? null, $brand['instagram'] ?? null]))], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
  @stack('jsonld')
</head>
<body data-page="@yield('page')">
<a class="skip-link" href="#main">Skip to content</a>
@include('partials.header')
@hasSection('banner')
  @yield('banner')
@endif
@yield('content')
@include('partials.footer')
@include('partials.drawer')
@include('partials.bottom-nav')
<button type="button" class="to-top" aria-label="Back to top" hidden><x-ui.icon name="up" /></button>
<div id="toasts" class="toasts" aria-live="polite"></div>
@if (session('toast'))
  <script type="application/json" id="flashToast">{!! json_encode(session('toast'), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
<script>window.ASHFI = { routes: { search: @json(route('search')), cart: @json(route('cart')), wishlist: @json(route('wishlist')) }, announcements: @json(array_values($announcements ?? [])) };</script>
<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
