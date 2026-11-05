@props(['product', 'class' => ''])
<div class="price {{ $class }}">
  <span class="price__now">{{ money($product->unitPrice()) }}</span>
  @if ($product->onSale())
    <s class="price__was"><span class="sr-only">Regular price </span>{{ money($product->price) }}</s>
    <span class="price__off">{{ $product->discountPct() }}% off</span>
  @endif
</div>
