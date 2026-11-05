@extends('layouts.app')
@section('page', 'about')
@section('title', 'About Us')
@section('description', 'Ashfi Collection brings elegant, comfortable and affordable women\'s fashion to Bangladesh – kurti, saree, two piece and three piece.')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['About Us', null]], 'heading' => 'About Ashfi Collection', 'lead' => 'Elegance in Every Thread.'])
@endsection
@section('content')
<main id="main">
  <section class="section">
    <div class="container story">
      <div class="story__art"><span class="img-wrap"><img src="{{ asset('images/story.jpg') }}" alt="Folded sarees in rich colours on boutique shelves" width="600" height="800" decoding="async"></span></div>
      <div class="story__copy">
        <p class="eyebrow">Our Story</p>
        <h2>Fashion That Feels Like You</h2>
        <p>Ashfi Collection began with a simple idea: women should be able to find beautiful, well-made clothing without paying luxury prices. We design and select pieces that balance tradition with a modern sensibility.</p>
        <p>From everyday kurtis to festive three pieces and timeless sarees, every item is chosen for its fabric, finish and the confidence it gives the person wearing it.</p>
        <a class="btn btn--primary" href="{{ route('shop') }}">Shop the Collection</a>
      </div>
    </div>
  </section>
  <section class="section section--cream" aria-labelledby="valTitle">
    <div class="container">
      <div class="section-head"><p class="eyebrow">What We Stand For</p><h2 id="valTitle">Our Promise</h2><div class="ornament" aria-hidden="true"><i></i></div></div>
      <div class="review-grid">
        <div class="review"><h3>Thoughtful Quality</h3><p>Comfortable fabrics, careful stitching and details that hold up wash after wash.</p></div>
        <div class="review"><h3>Affordable Luxury</h3><p>A premium look and feel at honest prices, with regular offers on seasonal pieces.</p></div>
        <div class="review"><h3>Trusted Service</h3><p>Clear communication, Cash on Delivery and an easy exchange policy across Bangladesh.</p></div>
      </div>
    </div>
  </section>
  <section class="section" aria-label="Ashfi in numbers">
    <div class="container"><div class="stats">
      <div class="stat"><strong>64</strong><span>Districts delivered to</span></div>
      <div class="stat"><strong>{{ $categoryCount }}</strong><span>Signature categories</span></div>
      <div class="stat"><strong>COD</strong><span>Pay when you receive</span></div>
      <div class="stat"><strong>7 days</strong><span>Easy exchange window</span></div>
    </div></div>
  </section>
</main>
@endsection
