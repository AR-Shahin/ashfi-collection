@extends('layouts.account', ['crumbs' => [['My Account', route('account.dashboard')], ['Profile & Password', null]], 'heading' => 'Profile & Password', 'lead' => 'Keep your details up to date.'])
@section('page', 'account-profile')
@section('title', 'Profile & Password')
@section('account')
<form class="panel form-grid" id="profileForm" method="post" action="{{ route('account.profile.update') }}" novalidate>@csrf @method('PUT')
  <h2>Personal Details</h2>
  @include('partials.field', ['id' => 'pfName', 'name' => 'name', 'label' => 'Full name', 'value' => $user->name, 'attrs' => 'autocomplete="name" required', 'bag' => 'profile'])
  <div class="form-grid form-grid--2">
    @include('partials.field', ['id' => 'pfEmail', 'name' => 'email', 'label' => 'Email address', 'type' => 'email', 'value' => $user->email, 'attrs' => 'inputmode="email" autocomplete="email" required', 'bag' => 'profile'])
    @include('partials.field', ['id' => 'pfPhone', 'name' => 'phone', 'label' => 'Mobile number', 'type' => 'tel', 'value' => $user->phone, 'attrs' => 'inputmode="tel" autocomplete="tel" required', 'bag' => 'profile'])
  </div>
  <div><button type="submit" class="btn btn--primary">Save Changes</button></div>
</form>
<form class="panel form-grid" id="passwordForm" method="post" action="{{ route('account.password') }}" novalidate>@csrf @method('PUT')
  <h2>Change Password</h2>
  @include('partials.password-field', ['id' => 'pwCurrent', 'name' => 'current_password', 'label' => 'Current password', 'autocomplete' => 'current-password', 'bag' => 'password'])
  <div class="form-grid form-grid--2">
    @include('partials.password-field', ['id' => 'pwNew', 'name' => 'password', 'label' => 'New password', 'autocomplete' => 'new-password', 'hint' => 'At least 8 characters.', 'bag' => 'password'])
    @include('partials.password-field', ['id' => 'pwConfirm', 'name' => 'password_confirmation', 'label' => 'Confirm new password', 'autocomplete' => 'new-password', 'bag' => 'password'])
  </div>
  <div><button type="submit" class="btn btn--primary">Update Password</button></div>
</form>
@endsection
