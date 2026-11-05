@extends('layouts.app')
@php
    $p = $product;
    $images = $p->images ?: [null];
    $sizes = $p->sizeList();
    $details = collect($p->details ?? [])->filter(fn ($d) => filled($d['label'] ?? null));
    $care = $details->firstWhere('label', 'Care')['value'] ?? null;
    $saved = app(\App\Services\Wishlist::class)->has($p->id);
    $delivery = setting('delivery');
    $desc = trim(($p->short ? $p->short.' ' : '').($sizes ? 'Available in '.implode(', ', $sizes).'. ' : '').'Price '.money($p->unitPrice()).'. Nationwide delivery in Bangladesh.');
@endphp
@section('page', 'product')
@section('title', $p->name)
@section('description', $desc)
@section('og_type', 'product')
@section('og_image', $p->imageUrl())
@section('canonical', $p->url())
@push('jsonld')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org', '@type' => 'Product', 'name' => $p->name, 'sku' => $p->slug, 'description' => $p->description ?: $p->short,
    'image' => collect($p->images)->map(fn ($i) => media_url($i))->all(), 'category' => $p->category?->name,
    'brand' => ['@type' => 'Brand', 'name' => 'Ashfi Collection'],
    'offers' => ['@type' => 'Offer', 'url' => $p->url(), 'price' => $p->unitPrice(), 'priceCurrency' => 'BDT', 'availability' => 'https://schema.org/InStock', 'itemCondition' => 'https://schema.org/NewCondition'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => route('shop')],
    ['@type' => 'ListItem', 'position' => 3, 'name' => $p->category?->name ?? 'Products', 'item' => route('shop', ['category' => $p->category?->slug])],
    ['@type' => 'ListItem', 'position' => 4, 'name' => $p->name, 'item' => $p->url()],
]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
<main id="main">
  <section class="page-banner" style="padding:22px 0 18px"><div class="container"><nav aria-label="Breadcrumb"><ul class="crumbs" style="margin:0">
    <li><a href="{{ route('home') }}">Home</a></li><li><a href="{{ route('shop') }}">Shop</a></li>
    @if ($p->category)<li><a href="{{ route('shop', ['category' => $p->category->slug]) }}">{{ $p->category->name }}</a></li>@endif
    <li aria-current="page">{{ $p->name }}</li>
  </ul></nav></div></section>
  <div class="page-body page-body--tight"><div class="container">
    <div class="pdp" id="pdp">
      <div class="gallery" data-gallery>
        <div class="gallery__main"><span class="img-wrap" id="mainWrap"><img id="mainImg" src="{{ $p->imageUrl() }}" alt="{{ $p->name }}" width="600" height="800" fetchpriority="high"></span>
          @if (count($images) > 1)
          <button type="button" class="gallery__nav gallery__nav--prev" data-nav="-1" aria-label="Previous image"><x-ui.icon name="left" /></button>
          <button type="button" class="gallery__nav gallery__nav--next" data-nav="1" aria-label="Next image"><x-ui.icon name="right" /></button>
          @endif
        </div>
        <div class="gallery__thumbs">
          @foreach ($images as $i => $src)
            <button type="button" class="thumb" data-i="{{ $i }}" data-src="{{ media_url($src) }}" aria-label="Show image {{ $i + 1 }} of {{ count($images) }}" @if ($i === 0) aria-current="true" @endif>
              <span class="img-wrap"><img src="{{ media_url($src) }}" alt="" width="600" height="800" loading="lazy" decoding="async"></span></button>
          @endforeach
        </div>
      </div>
      <div class="pdp__info" id="pdpInfo">
        <p class="eyebrow">{{ $p->category?->name }}</p>
        @if ($p->is_best_seller || $p->is_new)
          <div class="pdp__badges">@if ($p->is_best_seller)<span class="badge badge--best">Best Seller</span>@endif @if ($p->is_new)<span class="badge badge--new">New Arrival</span>@endif</div>
        @endif
        <h1>{{ $p->name }}</h1>
        <x-price :product="$p" class="price--lg" />
        <p class="pdp__short">{{ $p->description ?: $p->short }}</p>
        <form method="post" action="{{ route('cart.add') }}" data-ajax="cart" data-options>@csrf
          <input type="hidden" name="product" value="{{ $p->slug }}">
          <x-product-options :product="$p" />
          @if (count($sizes) > 1)<p class="pdp__sizeguide"><a class="link-arrow" href="{{ route('size-guide') }}">Size guide <x-ui.icon name="right" :size="16" /></a></p>@endif
          <div class="opt"><label for="pdpQty" style="display:block;margin:0 0 10px;font-size:.8125rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Quantity</label><x-qty id="pdpQty" /></div>
          <div class="pdp__buy"><button type="submit" class="btn btn--primary" id="pdpAdd">Add to Cart</button>
            <button type="submit" class="btn btn--outline" name="buy_now" value="1" id="pdpBuy">Buy Now</button></div>
        </form>
        <form method="post" action="{{ route('wishlist.toggle', $p) }}" data-ajax="wishlist">@csrf
          <button type="submit" class="btn btn--outline btn--sm pdp__wish {{ $saved ? 'is-active' : '' }}" data-action="wishlist" data-id="{{ $p->slug }}" aria-pressed="{{ $saved ? 'true' : 'false' }}"><x-ui.icon name="heart" :size="18" /> <span class="js-wish-label">{{ $saved ? 'Saved' : 'Wishlist' }}</span></button>
        </form>
        <ul class="assure">
          <li><x-ui.icon name="truck" :size="22" /><div><strong>Nationwide delivery</strong><span>Inside Dhaka 1–2 days ({{ money($delivery['insideDhaka']) }}), outside Dhaka 3–5 days ({{ money($delivery['outsideDhaka']) }}). Free over {{ money($delivery['freeOver']) }}.</span></div></li>
          <li><x-ui.icon name="cash" :size="22" /><div><strong>Cash on Delivery</strong><span>Pay in cash when your order arrives.</span></div></li>
          <li><x-ui.icon name="rotate" :size="22" /><div><strong>7-day easy exchange</strong><span>Unworn items with tags can be exchanged.</span></div></li>
        </ul>
        <div class="acc">
          @if ($details->isNotEmpty())
          <details open><summary>Product Details</summary><div class="acc__body"><dl class="spec">@foreach ($details as $d)<dt>{{ $d['label'] }}</dt><dd>{{ $d['value'] ?? '' }}</dd>@endforeach</dl></div></details>
          @endif
          <details><summary>Delivery &amp; Returns</summary><div class="acc__body"><p>Orders are confirmed by phone and dispatched within 1–2 working days. See our <a class="link-arrow" href="{{ route('policy') }}#delivery">delivery</a> and <a class="link-arrow" href="{{ route('policy') }}#returns">return</a> policies.</p></div></details>
          <details><summary>Care Instructions</summary><div class="acc__body"><p>{{ $care ?: 'Follow the care label' }}. Colours may appear slightly different on screen than in person.</p></div></details>
        </div>
      </div>
    </div>
    @if ($related->isNotEmpty())
    <section class="related" aria-labelledby="relTitle">
      <div class="section-head"><p class="eyebrow">You May Also Like</p><h2 id="relTitle">Related Products</h2></div>
      <div class="grid">@foreach ($related as $r)<x-product-card :product="$r" />@endforeach</div>
    </section>
    @endif
  </div></div>
</main>
@endsection
