@php $phoneHref = preg_replace('/[^+\d]/', '', $brand['phone'] ?? ''); @endphp
<footer class="site-footer"><div class="container"><div class="footer__grid">
  <div class="footer__brand"><a class="logo logo--light" href="{{ route('home') }}"><img class="logo__img" src="{{ asset('images/brand/logo-horizontal-light.svg') }}" alt="Ashfi Collection" width="562" height="176" loading="lazy"></a>
    <p class="footer__tag">{{ $brand['tagline'] ?? '' }}</p><p class="footer__bn" lang="bn">{{ $brand['taglineBn'] ?? '' }}</p>
    <p>Elegant, comfortable and thoughtfully finished clothing for the modern Bangladeshi woman.</p>
    <div class="social"><a href="{{ $brand['facebook'] ?? '#' }}" target="_blank" rel="noopener" aria-label="Ashfi Collection on Facebook"><x-ui.icon name="facebook" /></a>
    <a href="{{ $brand['instagram'] ?? '#' }}" target="_blank" rel="noopener" aria-label="Ashfi Collection on Instagram"><x-ui.icon name="instagram" /></a></div></div>
  <nav aria-label="Quick links"><h2 class="footer__h">Quick Links</h2><ul>
    <li><a href="{{ route('home') }}">Home</a></li><li><a href="{{ route('shop') }}">Shop</a></li><li><a href="{{ route('shop', ['new' => 1]) }}">New Arrivals</a></li>
    <li><a href="{{ route('shop', ['best' => 1]) }}">Best Sellers</a></li><li><a href="{{ route('offers') }}">Offers</a></li><li><a href="{{ route('size-guide') }}">Size Guide</a></li><li><a href="{{ route('about') }}">About Us</a></li></ul></nav>
  <nav aria-label="Customer support"><h2 class="footer__h">Customer Support</h2><ul>
    <li><a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}">My Account</a></li><li><a href="{{ route('track') }}">Track Order</a></li><li><a href="{{ route('contact') }}">Contact Us</a></li><li><a href="{{ route('faq') }}">FAQs</a></li><li><a href="{{ route('policy') }}#delivery">Delivery Policy</a></li>
    <li><a href="{{ route('policy') }}#returns">Return &amp; Exchange</a></li><li><a href="{{ route('policy') }}#privacy">Privacy Policy</a></li><li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li><li><a href="{{ route('policy') }}#credits">Photo Credits</a></li></ul></nav>
  <div><h2 class="footer__h">Get in Touch</h2><ul class="footer__contact">
    <li><x-ui.icon name="pin" :size="18" /><span>{{ $brand['address'] ?? '' }}</span></li>
    <li><x-ui.icon name="phone" :size="18" /><a href="tel:{{ $phoneHref }}">{{ $brand['phone'] ?? '' }}</a></li>
    <li><x-ui.icon name="mail" :size="18" /><a href="mailto:{{ $brand['email'] ?? '' }}">{{ $brand['email'] ?? '' }}</a></li>
    <li><x-ui.icon name="clock" :size="18" /><span>{{ $brand['hours'] ?? '' }}</span></li></ul></div>
</div>
<div class="footer__bottom"><p>&copy; {{ date('Y') }} {{ $brand['name'] ?? 'Ashfi Collection' }}. All rights reserved.</p><p>Cash on Delivery &middot; Nationwide Delivery</p></div></div></footer>
