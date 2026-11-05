@extends('layouts.app')
@section('page', 'home')
@section('canonical', route('home'))

@php $first = $slides->first(); @endphp
@if ($first)
  @push('preload')
    @if ($first->image_sm)<link rel="preload" as="image" href="{{ media_url($first->image_sm) }}" media="(max-width: 575px)" fetchpriority="high">@endif
    @if ($first->image_md)<link rel="preload" as="image" href="{{ media_url($first->image_md) }}" media="(min-width: 576px) and (max-width: 991px)" fetchpriority="high">@endif
    <link rel="preload" as="image" href="{{ media_url($first->image_lg) }}" media="(min-width: 992px)" fetchpriority="high">
  @endpush
@endif
@push('jsonld')
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => 'Ashfi Collection', 'url' => url('/'), 'potentialAction' => ['@type' => 'SearchAction', 'target' => ['@type' => 'EntryPoint', 'urlTemplate' => route('shop').'?q={search_term_string}'], 'query-input' => 'required name=search_term_string']], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
<main id="main">
  @if ($slides->isNotEmpty())
  <section class="hero" id="heroSlider" aria-roledescription="carousel" aria-label="Featured collections">
    <div class="hero__track" id="heroTrack">
      @foreach ($slides as $s)
      <article @class(['hero-slide', 'is-active is-live' => $loop->first]) data-align="{{ $s->align }}" data-fx="{{ $s->transition }}" data-duration="{{ $s->duration ?: 7000 }}"
        @if ($s->isVideo()) data-video="{{ media_url($s->video_lg) }}" data-video-sm="{{ media_url($s->video_sm ?: $s->video_lg) }}" @endif>
        <div class="hero-slide__media">
          <picture>
            @if ($s->image_sm)<source media="(max-width: 575px)" srcset="{{ media_url($s->image_sm) }}" width="750" height="1100">@endif
            @if ($s->image_md)<source media="(max-width: 991px)" srcset="{{ media_url($s->image_md) }}" width="1200" height="900">@endif
            <img src="{{ media_url($s->image_lg) }}" alt="{{ $s->image_alt }}" width="1920" height="1000" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif decoding="async">
          </picture>
        </div>
        <div class="container hero-slide__inner">
          <div class="hero-slide__copy">
            @if ($s->motto)<p class="hero-slide__motto" @if ($s->motto_lang) lang="{{ $s->motto_lang }}" @endif>{{ $s->motto }}</p>@endif
            @if ($loop->first)<h1 class="hero-slide__title">{{ $s->title }}</h1>@else<h2 class="hero-slide__title">{{ $s->title }}</h2>@endif
            @if ($s->description)<p class="hero-slide__desc">{{ $s->description }}</p>@endif
            <div class="hero-slide__cta">
              @if ($s->primary_label)<a class="btn btn--light" href="{{ $s->primary_url ?: route('shop') }}">{{ $s->primary_label }}</a>@endif
              @if ($s->secondary_label)<a class="btn btn--ghost-light" href="{{ $s->secondary_url ?: route('shop') }}">{{ $s->secondary_label }}</a>@endif
            </div>
          </div>
        </div>
      </article>
      @endforeach
    </div>
  </section>
  @else
    <h1 class="sr-only">Ashfi Collection</h1>
  @endif

  <section class="section section--soft" id="categories" aria-labelledby="catTitle">
    <div class="container">
      <div class="section-head"><p class="eyebrow">Shop by Category</p><h2 id="catTitle">Find Your Style</h2><div class="ornament" aria-hidden="true"><i></i></div></div>
      <div class="cat-grid" id="catGrid">
        @foreach ($categories as $c)
          <a class="cat-card" href="{{ route('shop', ['category' => $c->slug]) }}"><span class="img-wrap"><img src="{{ media_url($c->image) }}" alt="" width="600" height="800" loading="lazy" decoding="async"></span>
            <span class="cat-card__label"><span class="cat-card__name">{{ $c->name }}</span><span class="cat-card__go">Explore <x-ui.icon name="right" :size="14" /></span></span></a>
        @endforeach
        <a class="cat-card" href="{{ route('shop', ['new' => 1]) }}"><span class="img-wrap"><img src="{{ asset('images/categories/new-arrivals.jpg') }}" alt="" width="600" height="800" loading="lazy" decoding="async"></span>
          <span class="cat-card__label"><span class="cat-card__name">New Arrivals</span><span class="cat-card__go">Explore <x-ui.icon name="right" :size="14" /></span></span></a>
      </div>
    </div>
  </section>

  @if ($featured->isNotEmpty())
  <section class="section" aria-labelledby="featTitle">
    <div class="container">
      <div class="section-head"><p class="eyebrow">Handpicked for You</p><h2 id="featTitle">Featured Products</h2><div class="ornament" aria-hidden="true"><i></i></div></div>
      <div class="grid" id="featuredGrid">@foreach ($featured as $p)<x-product-card :product="$p" />@endforeach</div>
      <div class="section-foot"><a class="btn btn--outline" href="{{ route('shop') }}">View All Products</a></div>
    </div>
  </section>
  @endif

  <section class="promo" aria-labelledby="promoTitle">
    <div class="container promo__inner">
      <span class="script">Festive Edit</span>
      <h2 id="promoTitle">Elegance That Defines You</h2>
      <p>Find outfits that reflect your personality and make every occasion memorable.</p>
      <a class="btn btn--light" href="{{ route('shop', ['sale' => 1]) }}">Explore Now</a>
    </div>
  </section>

  @if ($newArrivals->isNotEmpty())
  <section class="section" id="new-arrivals" aria-labelledby="newTitle">
    <div class="container">
      <div class="section-head"><p class="eyebrow">Just Landed</p><h2 id="newTitle">New Arrivals</h2><div class="ornament" aria-hidden="true"><i></i></div></div>
      <div class="scroller-wrap">
        <button type="button" class="scroller-btn scroller-btn--prev" data-scroll="-1" aria-controls="newScroller" aria-label="Scroll new arrivals left"><x-ui.icon name="left" /></button>
        <div class="scroller" id="newScroller" tabindex="0" role="region" aria-label="New arrivals">@foreach ($newArrivals as $p)<x-product-card :product="$p" />@endforeach</div>
        <button type="button" class="scroller-btn scroller-btn--next" data-scroll="1" aria-controls="newScroller" aria-label="Scroll new arrivals right"><x-ui.icon name="right" /></button>
      </div>
      <div class="section-foot"><a class="link-arrow" href="{{ route('shop', ['new' => 1]) }}">See all new arrivals</a></div>
    </div>
  </section>
  @endif

  @if ($bestSellers->isNotEmpty())
  <section class="section section--burgundy" id="best-sellers" aria-labelledby="bestTitle">
    <div class="container">
      <div class="section-head"><p class="eyebrow">Loved by Our Customers</p><h2 id="bestTitle">Best Sellers</h2><div class="ornament" aria-hidden="true"><i></i></div>
        <p style="margin-top:14px">The pieces our customers reach for again and again.</p></div>
      <div class="grid grid--3" id="bestGrid">@foreach ($bestSellers as $p)<x-product-card :product="$p" />@endforeach</div>
      <div class="section-foot"><a class="btn btn--ghost-light" href="{{ route('shop', ['best' => 1]) }}">Shop Best Sellers</a></div>
    </div>
  </section>
  @endif

  <section class="section section--cream" aria-labelledby="storyTitle">
    <div class="container story">
      <div class="story__art"><span class="img-wrap"><img src="{{ asset('images/story.jpg') }}" alt="Folded sarees in rich colours on boutique shelves" width="600" height="800" loading="lazy" decoding="async"></span></div>
      <div class="story__copy">
        <p class="eyebrow">Our Philosophy</p>
        <h2 id="storyTitle">Welcome to Ashfi Collection</h2>
        <div class="ornament" aria-hidden="true"><i></i></div>
        <p>At Ashfi Collection, we believe fashion is more than clothing. It is an expression of personality, confidence, and individuality. We bring together elegant designs, comfortable fabrics, and thoughtful details to make your style truly yours.</p>
        <a class="btn btn--primary" href="{{ route('about') }}">Discover Our Story</a>
      </div>
    </div>
  </section>

  @if ($testimonials->isNotEmpty())
  <section class="section section--soft" aria-labelledby="revTitle">
    <div class="container">
      <div class="section-head"><p class="eyebrow">Kind Words</p><h2 id="revTitle">What Our Customers Say</h2><div class="ornament" aria-hidden="true"><i></i></div></div>
      <div class="review-grid" id="reviewGrid">
        @foreach ($testimonials as $t)
          <figure class="review" style="margin:0">@include('partials.stars', ['rating' => $t->rating])
            <blockquote style="margin:0"><p>{{ $t->text }}</p></blockquote>
            <figcaption class="review__who"><span class="avatar" aria-hidden="true">{{ mb_substr($t->name, 0, 1) }}</span>
              <span><span class="review__name">{{ $t->name }}</span><span class="review__place">{{ $t->place }}</span></span></figcaption></figure>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  @include('partials.newsletter')
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/hero.js') }}?v={{ filemtime(public_path('js/hero.js')) }}" defer></script>
@endpush
