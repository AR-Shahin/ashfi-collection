@extends('layouts.app')
@section('page', 'policy')
@section('title', 'Policies')
@section('description', $page?->meta_description ?? 'Delivery, returns and privacy at Ashfi Collection.')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Policies', null]], 'heading' => 'Policies', 'lead' => 'Delivery, returns and privacy at Ashfi Collection.'])
@endsection
@section('content')
@php $d = setting('delivery'); @endphp
<main id="main" class="page-body"><div class="container"><article class="prose">
  <h2 id="delivery">Delivery Policy</h2>
  <p>We deliver across all 64 districts of Bangladesh through trusted courier partners.</p>
  <table>
    <tr><th>Area</th><th>Estimated time</th><th>Charge</th></tr>
    <tr><td>Inside Dhaka</td><td>1–2 working days</td><td>{{ money($d['insideDhaka']) }}</td></tr>
    <tr><td>Outside Dhaka</td><td>3–5 working days</td><td>{{ money($d['outsideDhaka']) }}</td></tr>
  </table>
  <p>Orders over {{ money($d['freeOver']) }} qualify for free delivery.</p>
  <ul><li>Orders are confirmed by phone before dispatch.</li><li>Please keep your phone available on the delivery day.</li><li>Cash on Delivery is available nationwide.</li></ul>

  {!! $page?->body !!}

  <h2 id="credits">Photo Credits</h2>
  <p>Photos on this site are openly licensed images from Wikimedia Commons, shown as style references. Each one is credited below, and our cropped versions stay under the same licence.</p>
  <ul>
    @foreach (config('store.photo_credits') as $c)
      <li><a href="{{ $c['url'] }}" target="_blank" rel="noopener">{{ $c['title'] }}</a> by {{ $c['author'] }} (<a href="{{ $c['licenseUrl'] }}" target="_blank" rel="noopener">{{ $c['license'] }}</a>), used for: {{ $c['usedFor'] }}, cropped and resized.</li>
    @endforeach
  </ul>
</article></div></main>
@endsection
