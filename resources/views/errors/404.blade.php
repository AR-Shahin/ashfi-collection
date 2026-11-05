@extends('layouts.app')
@section('page', '404')
@section('title', 'Page Not Found')
@section('robots', 'noindex,follow')
@section('content')
<main id="main" class="container error-page">
  <p class="error-page__num" aria-hidden="true">404</p>
  <h1>This page has slipped away</h1>
  <p style="color:var(--muted);max-width:30em;margin:0 auto 24px">The page you are looking for may have moved or no longer exists. Let us help you find something beautiful instead.</p>
  <div class="hero__cta"><a class="btn btn--primary" href="{{ route('home') }}">Back to Home</a><a class="btn btn--outline" href="{{ route('shop') }}">Browse the Shop</a></div>
</main>
@endsection
