@extends('layouts.app')
@section('page', 'shop')
@section('title', $title === 'All Products' ? 'Shop All' : $title)
@section('description', $category ? 'Shop '.$category->name.' at Ashfi Collection: '.mb_strtolower($category->blurb ?? '').' with nationwide delivery and Cash on Delivery in Bangladesh.' : 'Shop kurti, two piece, three piece, saree and orna at Ashfi Collection with nationwide delivery and Cash on Delivery in Bangladesh.')
@section('canonical', route('shop', array_intersect_key($params, array_flip($category ? ['category'] : ['new', 'best', 'sale']))))
@if ($f['q'] !== '' || $f['min'] !== null || $f['max'] !== null || request('sort') || $products->currentPage() > 1)
  @section('robots', 'noindex,follow')
@endif

@section('banner')
  @include('partials.page-banner', ['crumbs' => $category ? [['Shop', route('shop')], [$category->name, null]] : [['Shop', null]], 'heading' => $title, 'lead' => $lead])
@endsection

@section('content')
@php $hidden = fn (string ...$keep) => collect($params)->only($keep); @endphp
<main id="main" class="page-body page-body--tight">
  <div class="container">
    <form id="shopForm" method="get" action="{{ route('shop') }}"></form>
    <div class="shop">
      <div class="overlay" id="filterOverlay" hidden></div>
      <aside class="filters" id="filters" aria-label="Product filters">
        <div class="filters__head"><h2>Filters</h2><button type="button" class="icon-btn" id="filterClose" aria-label="Close filters"><x-ui.icon name="x" /></button></div>
        <div class="filters__body">
          <div class="f-group"><h3>Category</h3><div id="fCats">
            <label class="f-opt"><input type="radio" form="shopForm" name="category" value="" @checked(! $category)> All Products<small>{{ $categories->sum('products_count') }}</small></label>
            @foreach ($categories as $c)
              <label class="f-opt"><input type="radio" form="shopForm" name="category" value="{{ $c->slug }}" @checked($category?->id === $c->id)> {{ $c->name }}<small>{{ $c->products_count }}</small></label>
            @endforeach
          </div></div>
          <div class="f-group"><h3>Price (৳)</h3>
            <div class="price-inputs">
              <label><span aria-hidden="true">৳</span><input type="number" form="shopForm" name="min" id="pMin" inputmode="numeric" min="0" placeholder="Min" aria-label="Minimum price" value="{{ $f['min'] }}"></label>
              <span aria-hidden="true">–</span>
              <label><span aria-hidden="true">৳</span><input type="number" form="shopForm" name="max" id="pMax" inputmode="numeric" min="0" placeholder="Max" aria-label="Maximum price" value="{{ $f['max'] }}"></label>
            </div>
            <div class="chips" id="pChips">
              @foreach (\App\Http\Controllers\ShopController::PRICE_PRESETS as [$min, $max, $label])
                @php $on = $f['min'] === $min && $f['max'] === $max; @endphp
                <a class="chip-btn" aria-pressed="{{ $on ? 'true' : 'false' }}" href="{{ route('shop', $on ? array_diff_key($params, ['min' => 1, 'max' => 1]) : array_filter(['min' => $min, 'max' => $max] + $params, fn ($v) => $v !== null)) }}">{{ $label }}</a>
              @endforeach
            </div>
          </div>
          <div class="f-group"><h3>Show only</h3>
            <label class="f-opt"><input type="checkbox" form="shopForm" name="sale" value="1" @checked($f['sale'])> On sale</label>
            <label class="f-opt"><input type="checkbox" form="shopForm" name="new" value="1" @checked($f['new'])> New arrivals</label>
            <label class="f-opt"><input type="checkbox" form="shopForm" name="best" value="1" @checked($f['best'])> Best sellers</label>
          </div>
        </div>
        <div class="filters__foot"><a class="btn btn--outline" href="{{ route('shop') }}">Clear all</a><button type="submit" form="shopForm" class="btn btn--primary" id="fApply">Show results</button></div>
      </aside>

      <section aria-label="Products">
        <div class="shop-search" role="search">
          <x-ui.icon name="search" />
          <input type="search" form="shopForm" name="q" id="shopQ" placeholder="Search products…" aria-label="Search products" autocomplete="off" value="{{ $f['q'] }}">
          @if ($f['q'] !== '')<a href="{{ route('shop', array_diff_key($params, ['q' => 1])) }}" id="shopQClear" aria-label="Clear search"><x-ui.icon name="x" /></a>@endif
        </div>
        <div class="shop__bar">
          <p class="shop__count" id="shopCount" role="status">Showing <strong>{{ $products->count() }}</strong> of {{ $products->total() }} {{ str('product')->plural($products->total()) }}</p>
          <div class="shop__controls">
            <button type="button" class="btn btn--outline btn--sm shop__filter-btn" id="filterOpen" aria-controls="filters" aria-expanded="false"><x-ui.icon name="sliders" /> Filters</button>
            <label class="sr-only" for="sortSel">Sort products</label>
            <select class="select" id="sortSel" name="sort" form="shopForm">
              @foreach (\App\Http\Controllers\ShopController::SORTS as $k => $label)<option value="{{ $k }}" @selected($f['sort'] === $k)>{{ $label }}</option>@endforeach
            </select>
            <noscript><button type="submit" form="shopForm" class="btn btn--primary btn--sm">Apply</button></noscript>
          </div>
        </div>
        @if ($active)
        <div class="active-filters" id="activeFilters">
          @foreach ($active as [$label, $url])<span class="tag">{{ $label }}<a href="{{ $url }}" aria-label="Remove filter: {{ $label }}"><x-ui.icon name="x" :size="12" /></a></span>@endforeach
          @if (count($active) > 1)<a class="chip-btn" href="{{ route('shop') }}">Clear all</a>@endif
        </div>
        @endif
        <div class="shop__results">
          @if ($products->isEmpty())
            <div class="empty"><x-ui.icon name="search" :size="44" /><h2>No products found</h2>
              <p>We couldn’t find anything matching your filters. Try a different search, widen your price range, or clear the filters.</p>
              <a class="btn btn--primary" href="{{ route('shop') }}">Clear all filters</a></div>
          @else
            <div class="grid" id="shopGrid">@foreach ($products as $p)<x-product-card :product="$p" :eager="$loop->index < 2" />@endforeach</div>
            {{ $products->links() }}
          @endif
        </div>
      </section>
    </div>
  </div>
</main>
@endsection
