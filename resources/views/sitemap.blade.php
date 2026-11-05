{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($static as $name => $priority)
  <url><loc>{{ route($name) }}</loc><priority>{{ number_format($priority, 1) }}</priority></url>
@endforeach
@foreach ($categories as $c)
  <url><loc>{{ route('shop', ['category' => $c->slug]) }}</loc><lastmod>{{ $c->updated_at?->toDateString() }}</lastmod><priority>0.8</priority></url>
@endforeach
@foreach ($products as $p)
  <url><loc>{{ route('product.show', $p->slug) }}</loc><lastmod>{{ $p->updated_at?->toDateString() }}</lastmod><priority>0.7</priority></url>
@endforeach
</urlset>
