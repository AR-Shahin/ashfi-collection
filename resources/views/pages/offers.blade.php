@extends('layouts.app')
@section('page', 'offers')
@section('title', 'Offers & Coupons')
@section('description', 'Current coupon codes and sale picks at Ashfi Collection – save on kurti, saree, two piece and three piece.')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Offers', null]], 'heading' => 'Offers & Coupons', 'lead' => 'Save on your favourite pieces.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container">
  <section aria-labelledby="couponsTitle">
    <div class="section-head"><p class="eyebrow">Coupon Codes</p><h2 id="couponsTitle">Use a code at checkout</h2></div>
    <div class="offers" id="offerGrid">
      @forelse ($coupons as $c)
        <article class="offer"><div class="offer__icon"><x-ui.icon :name="$c->type === 'shipping' ? 'truck' : 'tag'" :size="24" /></div>
          <h3>{{ $c->title ?: $c->code }}</h3><p>{{ $c->detail }}</p>
          @if ($c->expires_at)<small>Valid until {{ $c->expires_at->format('j F Y') }}</small>@endif
          <button type="button" class="offer__code" data-copy="{{ $c->code }}" aria-label="Copy coupon code {{ $c->code }}"><span>{{ $c->code }}</span><x-ui.icon name="copy" :size="16" /></button></article>
      @empty
        <p class="empty__text">There are no coupon codes right now. Check back soon.</p>
      @endforelse
    </div>
  </section>
  @if ($sale->isNotEmpty())
  <section aria-labelledby="saleTitle" style="margin-top:64px">
    <div class="section-head"><p class="eyebrow">On Sale</p><h2 id="saleTitle">Sale Picks</h2></div>
    <div class="grid" id="saleGrid">@foreach ($sale as $p)<x-product-card :product="$p" />@endforeach</div>
  </section>
  @endif
</div></main>
@endsection
