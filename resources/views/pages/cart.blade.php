@extends('layouts.app')
@section('page', 'cart')
@section('title', 'Shopping Cart')
@section('robots', 'noindex,follow')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Cart', null]], 'heading' => 'Shopping Cart', 'lead' => 'Review your selected pieces before checkout.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container" id="cartRoot" data-summary-root>
  @include('partials.cart-body')
</div></main>
@endsection
