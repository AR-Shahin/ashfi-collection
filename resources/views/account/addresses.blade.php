@extends('layouts.account', ['crumbs' => [['My Account', route('account.dashboard')], ['Addresses', null]], 'heading' => 'My Addresses', 'lead' => 'Save addresses for faster checkout.'])
@section('page', 'account-addresses')
@section('title', 'My Addresses')
@section('account')
@if ($editing)
  <form class="panel form-grid" id="addrForm" method="post" action="{{ $editing->exists ? route('account.addresses.update', $editing) : route('account.addresses.store') }}" novalidate>@csrf
    @if ($editing->exists) @method('PUT') @endif
    <h2>{{ $editing->exists ? 'Edit Address' : 'Add a New Address' }}</h2>
    <div class="form-grid form-grid--2">
      @include('partials.field', ['id' => 'adrLabel', 'name' => 'label', 'label' => 'Label', 'value' => $editing->label ?: 'Home', 'attrs' => 'placeholder="Home, Office…" maxlength="30" required autofocus'])
      @include('partials.field', ['id' => 'adrName', 'name' => 'name', 'label' => 'Full name', 'value' => $editing->name, 'attrs' => 'autocomplete="name" required'])
    </div>
    <div class="form-grid form-grid--2">
      @include('partials.field', ['id' => 'adrPhone', 'name' => 'phone', 'label' => 'Mobile number', 'type' => 'tel', 'value' => $editing->phone, 'attrs' => 'inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" required'])
      <div class="field"><label for="adrDistrict">District</label><select id="adrDistrict" name="district" required @error('district') aria-invalid="true" @enderror><option value="">Select district</option>
        @foreach (config('store.districts') as $dist)<option @selected(old('district', $editing->district) === $dist)>{{ $dist }}</option>@endforeach
      </select><p class="field__error" id="adrDistrictErr">@error('district'){{ $message }}@enderror</p></div>
    </div>
    @include('partials.field', ['id' => 'adrAddress', 'name' => 'address', 'label' => 'Full address', 'textarea' => true, 'value' => $editing->address, 'attrs' => 'autocomplete="street-address" placeholder="House, road, area" required'])
    <label class="f-opt"><input type="checkbox" name="is_default" value="1" @checked(old('is_default', $editing->is_default))> Make this my default address</label>
    <div class="account__actions"><button type="submit" class="btn btn--primary">Save Address</button><a class="btn btn--outline" href="{{ route('account.addresses') }}">Cancel</a></div>
  </form>
@else
  <div class="account__actions"><a class="btn btn--primary" href="{{ route('account.addresses', ['new' => 1]) }}"><x-ui.icon name="plus" :size="16" /> Add Address</a></div>
@endif
@if ($addresses->isNotEmpty())
  <div class="addr-grid">
    @foreach ($addresses as $a)
      <article @class(['addr-card', 'is-default' => $a->is_default])><header><h3>{{ $a->label }}</h3>@if ($a->is_default)<span class="pill">Default</span>@endif</header>
        @include('partials.order.address', $a->only(['name', 'address', 'district', 'phone']))
        <footer>
          <a class="link-btn" href="{{ route('account.addresses', ['edit' => $a->id]) }}"><x-ui.icon name="edit" :size="16" /> Edit</a>
          @unless ($a->is_default)<form method="post" action="{{ route('account.addresses.default', $a) }}">@csrf<button type="submit" class="link-btn">Set as default</button></form>@endunless
          <form method="post" action="{{ route('account.addresses.destroy', $a) }}" data-confirm="Delete this address?">@csrf @method('DELETE')<button type="submit" class="link-btn"><x-ui.icon name="trash" :size="16" /> Delete</button></form>
        </footer></article>
    @endforeach
  </div>
@elseif (! $editing)
  <div class="empty"><x-ui.icon name="pin" :size="44" /><h2>No saved addresses</h2><p>Add one to speed up checkout.</p></div>
@endif
@endsection
