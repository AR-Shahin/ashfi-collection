<section class="newsletter" aria-labelledby="newsTitle">
  <div class="container">
    <div class="newsletter__box">
      <p class="eyebrow" style="color:var(--gold)">Newsletter</p>
      <h2 id="newsTitle">Stay Inspired, Stay Stylish</h2>
      <p>Subscribe to receive updates about new arrivals, exclusive offers, and the latest fashion trends.</p>
      <form class="news-form" id="newsForm" method="post" action="{{ route('newsletter') }}" data-ajax="message" data-msg="#newsMsg" novalidate>@csrf
        <label class="sr-only" for="newsEmail">Email address</label>
        <input type="email" id="newsEmail" name="email" placeholder="Your email address" autocomplete="email" required value="{{ old('email') }}">
        <button type="submit" class="btn btn--light">Subscribe</button>
      </form>
      <p class="news-msg {{ $errors->has('email') ? 'is-error' : '' }}" id="newsMsg" role="status">{{ $errors->first('email') }}</p>
    </div>
  </div>
</section>
