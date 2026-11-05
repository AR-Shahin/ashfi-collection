@extends('layouts.app')
@section('page', 'wishlist')
@section('title', 'My Wishlist')
@section('robots', 'noindex,follow')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Wishlist', null]], 'heading' => 'My Wishlist', 'lead' => auth()->check() ? 'Pieces you love, saved to your account.' : 'Pieces you love. Log in to keep them across devices.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container" id="wishRoot">
  @if ($products->isEmpty())
    <div class="empty"><x-ui.icon name="heart" :size="48" /><h2>Your wishlist is empty</h2><p>Tap the heart on any product to save it here for later.</p>
      <a class="btn btn--primary" href="{{ route('shop') }}">Discover Products</a></div>
  @else
    <p class="shop__count" style="margin-bottom:20px">{{ $products->count() }} saved {{ str('item')->plural($products->count()) }}</p>
    <div class="grid" id="wishGrid" data-wishlist-page>@foreach ($products as $p)<x-product-card :product="$p" />@endforeach</div>
  @endif
</div></main>
@endsection
