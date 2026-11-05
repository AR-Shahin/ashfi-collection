@extends('layouts.app')
@section('page', 'forgot-password')
@section('title', 'Forgot Password')
@section('robots', 'noindex,follow')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Log In', route('login')], ['Forgot Password', null]], 'heading' => 'Forgot Your Password?', 'lead' => 'We’ll help you get back in.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container"><div class="auth auth--single">
  <div class="auth__card">
    @if (session('linkSent'))
      <div tabindex="-1">
        <h2>Check your email</h2>
        <p class="auth__lead">If an account exists for <strong>{{ session('linkSent') }}</strong>, we’ve sent a link to reset your password. It expires in 60 minutes.</p>
        <p class="field__hint">Didn’t get it? Check your spam folder, or <a class="text-link" href="{{ route('password.request') }}">try again</a>.</p>
      </div>
    @else
      <h2>Reset your password</h2>
      <p class="auth__lead">Enter the email address on your account and we’ll send you a reset link.</p>
      <form id="forgotForm" class="form-grid" method="post" action="{{ route('password.email') }}" novalidate>@csrf
        @include('partials.field', ['id' => 'forgotEmail', 'name' => 'email', 'label' => 'Email address', 'type' => 'email', 'attrs' => 'autocomplete="email" required inputmode="email"'])
        <button type="submit" class="btn btn--primary btn--block">Send Reset Link</button>
      </form>
    @endif
    <p class="auth__alt"><a class="text-link" href="{{ route('login') }}">Back to log in</a></p>
  </div>
</div></div></main>
@endsection
