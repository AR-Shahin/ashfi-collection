{{-- $t = Cart::totals(); $view = cart|checkout --}}
<div class="coupon-box">
  @if ($t->coupon)
    <div class="coupon__on"><x-ui.icon name="tag" :size="18" /><span><strong>{{ $t->coupon->code }}</strong> {{ $t->coupon->title }}</span>
      <form method="post" action="{{ route('cart.coupon.remove') }}" data-ajax="summary" style="display:inline">@csrf @method('DELETE')<input type="hidden" name="view" value="{{ $view }}">
        <button type="submit" class="link-btn" aria-label="Remove coupon {{ $t->coupon->code }}">Remove</button></form></div>
    @if ($t->couponMessage)<p class="coupon__msg is-error" role="status">{{ $t->couponMessage }}</p>@endif
  @else
    <form class="coupon" method="post" action="{{ route('cart.coupon') }}" data-ajax="summary" novalidate>@csrf<input type="hidden" name="view" value="{{ $view }}">
      <label class="sr-only" for="couponCode">Coupon code</label>
      <input id="couponCode" name="code" placeholder="Coupon code" autocomplete="off" autocapitalize="characters" spellcheck="false" value="{{ old('code') }}">
      <button type="submit" class="btn btn--outline btn--sm">Apply</button></form>
    <p class="coupon__msg {{ session('couponError') ? 'is-error' : '' }}" role="status">{{ session('couponError') }}</p>
    <p class="coupon__link"><a href="{{ route('offers') }}">View available offers</a></p>
  @endif
</div>
