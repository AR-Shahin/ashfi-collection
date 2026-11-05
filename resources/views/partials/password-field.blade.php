@php $err = $errors->getBag($bag ?? 'default')->first($errorKey ?? $name); @endphp
<div class="field"><label for="{{ $id }}">{{ $label }}</label>
  <div class="pw"><input id="{{ $id }}" name="{{ $name }}" type="password" autocomplete="{{ $autocomplete }}" required @if ($err) aria-invalid="true" aria-describedby="{{ $id }}Err" @endif>
    <button type="button" class="pw__toggle" data-pw-toggle="{{ $id }}" aria-pressed="false" aria-label="Show or hide password">Show</button></div>
  <p class="field__error" id="{{ $id }}Err">{{ $err }}</p>
  @if (! empty($hint))<p class="field__hint">{{ $hint }}</p>@endif
</div>
