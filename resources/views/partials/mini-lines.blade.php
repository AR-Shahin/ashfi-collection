{{-- $items: cart lines (product/size/color/qty/total) or order items --}}
<div class="mini-lines">
  @foreach ($items as $i)
    @php
        $isOrder = $i instanceof \App\Models\OrderItem;
        $name = $isOrder ? $i->name : $i->product->name;
        $img = $isOrder ? media_url($i->image) : $i->product->imageUrl();
        $qty = $isOrder ? $i->quantity : $i->qty;
        $total = $isOrder ? $i->lineTotal() : $i->total;
        $link = $isOrder ? ($i->product?->is_active ? $i->product->url() : null) : null;
    @endphp
    <div class="mini-line"><img src="{{ $img }}" alt="" width="56" height="74" loading="lazy"><div><strong>@if ($link)<a href="{{ $link }}">{{ $name }}</a>@else{{ $name }}@endif</strong><small>{{ collect([$i->color, $i->size])->filter()->implode(' · ') }} × {{ $qty }}</small></div><span>{{ money($total) }}</span></div>
  @endforeach
</div>
