@extends('layouts.app')
@section('page', 'register')
@section('title', 'Create Account')
@section('robots', 'noindex,follow')
@section('banner')
  @include('partials.page-banner', ['crumbs' => [['Create Account', null]], 'heading' => 'Create Your Account', 'lead' => 'It only takes a minute.'])
@endsection
@section('content')
<main id="main" class="page-body"><div class="container"><div class="auth">
  <div class="auth__card">
    <h2>Join Ashfi Collection</h2>
    <form id="registerForm" class="form-grid" method="post" action="{{ route('register') }}" novalidate>@csrf
      @include('partials.field', ['id' => 'regName', 'name' => 'name', 'label' => 'Full name', 'attrs' => 'autocomplete="name" required'])
      <div class="form-grid form-grid--2">
        @include('partials.field', ['id' => 'regEmail', 'name' => 'email', 'label' => 'Email address', 'type' => 'email', 'attrs' => 'autocomplete="email" required inputmode="email"'])
        @include('partials.field', ['id' => 'regPhone', 'name' => 'phone', 'label' => 'Mobile number', 'type' => 'tel', 'attrs' => 'autocomplete="tel" required inputmode="tel" placeholder="01XXXXXXXXX"'])
      </div>
      @include('partials.password-field', ['id' => 'regPassword', 'name' => 'password', 'label' => 'Password', 'autocomplete' => 'new-password', 'hint' => 'At least 8 characters.'])
      @include('partials.password-field', ['id' => 'regConfirm', 'name' => 'password_confirmation', 'label' => 'Confirm password', 'autocomplete' => 'new-password'])
      <div><label class="f-opt"><input type="checkbox" name="terms" value="1" id="regTerms" @checked(old('terms')) @error('terms') aria-invalid="true" @enderror> <span>I agree to the <a class="text-link" href="{{ route('terms') }}">Terms &amp; Conditions</a> and <a class="text-link" href="{{ route('policy') }}#privacy">Privacy Policy</a></span></label><p class="field__error" id="regTermsErr">@error('terms'){{ $message }}@enderror</p></div>
      <button type="submit" class="btn btn--primary btn--block">Create Account</button>
    </form>
    <p class="auth__alt">Already have an account? <a class="text-link" href="{{ route('login') }}">Log in</a></p>
  </div>
  @include('partials.auth-perks')
</div></div></main>
@endsection
