{{-- $crumbs: [[label, url|null], …] (Home is added automatically) --}}
<section class="page-banner"><div class="container">
  <nav aria-label="Breadcrumb"><ul class="crumbs">
    <li><a href="{{ route('home') }}">Home</a></li>
    @foreach ($crumbs as [$label, $url])
      @if ($url)<li><a href="{{ $url }}">{{ $label }}</a></li>@else<li aria-current="page">{{ $label }}</li>@endif
    @endforeach
  </ul></nav>
  <h1>{{ $heading }}</h1>
  @if (! empty($lead))<p>{{ $lead }}</p>@endif
</div></section>
@push('jsonld')
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => collect([['Home', route('home')]])->concat($crumbs)->values()->map(fn ($c, $i) => array_filter(['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1] ?? url()->current()]))->all()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush
