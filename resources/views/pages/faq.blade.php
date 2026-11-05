@extends('layouts.app')
@section('page', 'faq')
@section('title', 'FAQs')
@section('description', 'Answers about delivery, payment, Cash on Delivery, sizes, returns and your Ashfi Collection account.')
@push('jsonld')
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $groups->flatten()->map(fn ($f) => ['@type' => 'Question', 'name' => $f->question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f->answer)]])->values()->all()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['FAQs', null]], 'heading' => 'Frequently Asked Questions', 'lead' => 'Quick answers about ordering from us.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container"><div class="faq faq--page">
  @foreach ($groups as $group => $faqs)
    <section aria-labelledby="faq{{ $loop->index }}"><h2 id="faq{{ $loop->index }}">{{ $group }}</h2><div class="acc">
      @foreach ($faqs as $f)<details @if ($loop->parent->first && $loop->first) open @endif><summary>{{ $f->question }}</summary><div class="acc__body">{!! $f->answer !!}</div></details>@endforeach
    </div></section>
  @endforeach
  <div class="faq__more"><h2>Still need help?</h2><p>Our team is happy to help.</p><a class="btn btn--primary" href="{{ route('contact') }}">Contact Us</a></div>
</div></div></main>
@endsection
