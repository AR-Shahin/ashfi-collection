@if ($lines->isEmpty())
  <div class="empty"><x-ui.icon name="bag" :size="48" /><h2>Your cart is empty</h2>
    <p>Looks like you haven’t added anything yet. Explore our latest pieces and find something you love.</p>
    <a class="btn btn--primary" href="{{ route('shop') }}">Start Shopping</a></div>
@else
<div class="cart">
  <section aria-label="Cart items"><ul class="cart-list">
    @foreach ($lines as $l)
    <li class="cart-line">
      <a class="cart-line__img" href="{{ $l->product->url() }}"><span class="img-wrap"><img src="{{ $l->product->imageUrl() }}" alt="{{ $l->product->name }}" width="600" height="800" loading="lazy"></span></a>
      <div><h3 class="cart-line__name"><a href="{{ $l->product->url() }}">{{ $l->product->name }}</a></h3>
        <p class="cart-line__meta">{{ $l->meta }}</p><x-price :product="$l->product" /></div>
      <div class="cart-line__row">
        <form class="qty" method="post" action="{{ route('cart.update', $l->key) }}" data-ajax="summary" data-qty-form>@csrf @method('PATCH')
          <button type="submit" class="qty__btn" name="qty" value="{{ $l->qty - 1 }}" aria-label="Decrease quantity of {{ $l->product->name }}"><x-ui.icon name="minus" :size="16" /></button>
          <input class="qty__input" type="text" inputmode="numeric" pattern="[0-9]*" name="qty" value="{{ $l->qty }}" maxlength="2" aria-label="Quantity of {{ $l->product->name }}">
          <button type="submit" class="qty__btn" name="qty" value="{{ min($l->qty + 1, \App\Services\Cart::maxQty()) }}" aria-label="Increase quantity of {{ $l->product->name }}"><x-ui.icon name="plus" :size="16" /></button>
        </form>
        <span class="cart-line__total" aria-label="Line total">{{ money($l->total) }}</span>
        <form method="post" action="{{ route('cart.remove', $l->key) }}" data-ajax="summary">@csrf @method('DELETE')
          <button type="submit" class="link-btn"><x-ui.icon name="trash" :size="16" /> Remove</button></form>
      </div>
    </li>
    @endforeach
  </ul>
  <p style="margin-top:20px"><a class="link-arrow" href="{{ route('shop') }}"><x-ui.icon name="left" :size="16" /> Continue shopping</a></p></section>
  <aside class="summary" aria-label="Order summary"><h2>Order Summary</h2>
    <div class="progress"><div class="progress__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $t->progress }}" aria-label="Progress to free delivery"><i style="width:{{ $t->progress }}%"></i></div>
      <p>@if ($t->leftForFree) Add {{ money($t->leftForFree) }} more for <strong>free delivery</strong>. @else 🎉 You’ve unlocked <strong>free delivery</strong>! @endif</p></div>
    <div class="sum-row"><span>Subtotal ({{ $t->count }} {{ str('item')->plural($t->count) }})</span><span>{{ money($t->subtotal) }}</span></div>
    @if ($t->discount)<div class="sum-row sum-row--save"><span>Coupon discount</span><span>−{{ money($t->discount) }}</span></div>@endif
    <div class="sum-row"><span>Delivery</span><span>{{ $t->freeDelivery || ! $t->leftForFree ? 'Free' : 'Calculated at checkout' }}</span></div>
    <div class="sum-row sum-row--total"><span>Total</span><span>{{ money($t->total) }}</span></div>
    @include('partials.coupon-box', ['view' => 'cart'])
    <p class="sum-note">Delivery: {{ money(setting('delivery.insideDhaka')) }} inside Dhaka, {{ money(setting('delivery.outsideDhaka')) }} outside Dhaka.</p>
    <a class="btn btn--primary btn--block" href="{{ route('checkout') }}">Proceed to Checkout</a></aside>
</div>
@endif
