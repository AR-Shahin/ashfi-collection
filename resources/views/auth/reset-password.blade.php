@extends('layouts.app')
@section('page', 'reset-password')
@section('title', 'Choose a New Password')
@section('robots', 'noindex,nofollow')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Log In', route('login')], ['New Password', null]], 'heading' => 'Choose a New Password', 'lead' => 'Make it one you don’t use anywhere else.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container"><div class="auth auth--single">
  <div class="auth__card">
    <h2>Set your new password</h2>
    @if (session('formError'))<p class="form-msg" role="alert">{{ session('formError') }}</p>@endif
    <form id="resetForm" class="form-grid" method="post" action="{{ route('password.update') }}" novalidate>@csrf
      <input type="hidden" name="token" value="{{ $token }}">
      @include('partials.field', ['id' => 'resetEmail', 'name' => 'email', 'label' => 'Email address', 'type' => 'email', 'value' => $email, 'attrs' => 'autocomplete="email" required inputmode="email"'])
      @include('partials.password-field', ['id' => 'resetPassword', 'name' => 'password', 'label' => 'New password', 'autocomplete' => 'new-password', 'hint' => 'At least 8 characters.'])
      @include('partials.password-field', ['id' => 'resetConfirm', 'name' => 'password_confirmation', 'label' => 'Confirm new password', 'autocomplete' => 'new-password'])
      <button type="submit" class="btn btn--primary btn--block">Save Password</button>
    </form>
    <p class="auth__alt"><a class="text-link" href="{{ route('login') }}">Back to log in</a></p>
  </div>
</div></div></main>
@endsection
