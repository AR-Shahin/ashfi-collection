@extends('layouts.app')
@section('page', 'login')
@section('title', 'Log In')
@section('robots', 'noindex,follow')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Log In', null]], 'heading' => 'Welcome Back', 'lead' => 'Log in to track orders and check out faster.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container"><div class="auth">
  <div class="auth__card">
    <h2>Log in to your account</h2>
    @if (session('formError'))<p class="form-msg" role="alert">{{ session('formError') }}</p>@endif
    <form id="loginForm" class="form-grid" method="post" action="{{ route('login') }}" novalidate>@csrf
      @include('partials.field', ['id' => 'loginEmail', 'name' => 'email', 'label' => 'Email address', 'type' => 'email', 'attrs' => 'autocomplete="email" required inputmode="email" autofocus'])
      @include('partials.password-field', ['id' => 'loginPassword', 'name' => 'password', 'label' => 'Password', 'autocomplete' => 'current-password'])
      <div class="auth__row"><label class="f-opt"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Remember me</label><a class="text-link" href="{{ route('password.request') }}">Forgot password?</a></div>
      <button type="submit" class="btn btn--primary btn--block">Log In</button>
    </form>
    <p class="auth__alt">New to Ashfi Collection? <a class="text-link" href="{{ route('register') }}">Create an account</a></p>
  </div>
  @include('partials.auth-perks')
</div></div></main>
@endsection
