@extends('layouts.app')
@section('page', $page->slug)
@section('title', $page->title)
@section('description', $page->meta_description ?? $page->lead ?? '')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [[$page->title, null]], 'heading' => $page->title, 'lead' => $page->lead])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container"><article class="prose">{!! $page->body !!}</article></div></main>
@endsection
