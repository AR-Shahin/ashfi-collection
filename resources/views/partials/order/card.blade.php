<article class="order-card"><header class="order-card__head"><div><h3>{{ $order->number }}</h3><small>{{ $order->created_at->format('j M Y') }}</small></div>@include('partials.order.pill')</header>
  <div class="order-card__thumbs">@foreach ($order->items->take(4) as $i)<img src="{{ media_url($i->image) }}" alt="" width="48" height="64" loading="lazy">@endforeach</div>
  <footer class="order-card__foot"><span>{{ $order->itemCount() }} {{ str('item')->plural($order->itemCount()) }} · <strong>{{ money($order->total) }}</strong></span>
    <a class="btn btn--outline btn--sm" href="{{ route('account.order', $order) }}" aria-label="View order {{ $order->number }}">View Order</a></footer></article>
