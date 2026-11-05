@props(['product', 'eager' => false])
@php
    $p = $product;
    $saved = app(\App\Services\Wishlist::class)->has($p->id);
    $needsSize = count($p->sizeList()) > 1;
@endphp
<article class="product-card">
  <div class="product-card__media">
    <a class="product-card__img" href="{{ $p->url() }}" tabindex="-1" aria-hidden="true">
      <span class="img-wrap"><img src="{{ $p->imageUrl() }}" alt="{{ $p->name }}" width="600" height="800" loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async"></span>
    </a>
    <div class="badges">
      @if ($p->is_best_seller)<span class="badge badge--best">Best Seller</span>@endif
      @if ($p->is_new)<span class="badge badge--new">New Arrival</span>@endif
    </div>
    <div class="product-card__tools">
      <form method="post" action="{{ route('wishlist.toggle', $p) }}" data-ajax="wishlist">@csrf
        <button type="submit" class="tool-btn {{ $saved ? 'is-active' : '' }}" data-action="wishlist" data-id="{{ $p->slug }}" aria-pressed="{{ $saved ? 'true' : 'false' }}" aria-label="{{ $saved ? 'Remove from wishlist' : 'Add to wishlist' }}"><x-ui.icon name="heart" :size="18" /></button>
      </form>
      <a class="tool-btn" href="{{ $p->url() }}" data-action="quick-view" data-url="{{ route('product.quick-view', $p) }}" aria-label="Quick view: {{ $p->name }}"><x-ui.icon name="eye" :size="18" /></a>
    </div>
  </div>
  <div class="product-card__body">
    <p class="product-card__cat">{{ $p->category?->name }}</p>
    <h3 class="product-card__name"><a href="{{ $p->url() }}">{{ $p->name }}</a></h3>
    <x-price :product="$p" />
    @if ($needsSize)
      <a class="btn btn--primary btn--block btn--sm" href="{{ $p->url() }}" data-action="add-to-cart" data-url="{{ route('product.quick-view', ['product' => $p, 'hint' => 'Please choose your size to add this item.']) }}">Add to Cart</a>
    @else
      <form method="post" action="{{ route('cart.add') }}" data-ajax="cart">@csrf
        <input type="hidden" name="product" value="{{ $p->slug }}">
        <button type="submit" class="btn btn--primary btn--block btn--sm" data-action="add-to-cart">Add to Cart</button>
      </form>
    @endif
  </div>
</article>
