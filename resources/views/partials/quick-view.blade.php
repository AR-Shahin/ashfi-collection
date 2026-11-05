{{-- Loaded into <dialog class="qv"> by app.js --}}
<button type="button" class="qv__close icon-btn" data-qv-close aria-label="Close quick view"><x-ui.icon name="x" :size="22" /></button>
<div class="qv__grid">
  <div class="qv__media"><span class="img-wrap"><img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" width="600" height="800" decoding="async"></span></div>
  <div class="qv__info">
    <p class="eyebrow">{{ $product->category?->name }}</p>
    <h2 id="qvTitle" class="qv__title">{{ $product->name }}</h2>
    <x-price :product="$product" class="price--lg" />
    <p class="qv__desc">{{ $product->short }}</p>
    <form method="post" action="{{ route('cart.add') }}" data-ajax="cart" data-options data-close-dialog>@csrf
      <input type="hidden" name="product" value="{{ $product->slug }}">
      <x-product-options :product="$product" :hint="$hint" />
      <div class="qv__buy"><x-qty id="qvQty" /><button type="submit" class="btn btn--primary">Add to Cart</button></div>
    </form>
    <a class="link-arrow" href="{{ $product->url() }}">View full details <x-ui.icon name="right" :size="16" /></a>
  </div>
</div>
