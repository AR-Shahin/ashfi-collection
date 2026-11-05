{{-- Text input/textarea with label + error. Params: id, name, label, type?, value?, attrs?, textarea?, hint?, bag? --}}
@php
    $bagName = $bag ?? 'default';
    $err = $errors->getBag($bagName)->first($name);
    $val = old($name, $value ?? '');
@endphp
<div class="field"><label for="{{ $id }}">{!! $label !!}</label>
  @if (! empty($textarea))
    <textarea id="{{ $id }}" name="{{ $name }}" {!! $attrs ?? '' !!} @if ($err) aria-invalid="true" aria-describedby="{{ $id }}Err" @endif>{{ $val }}</textarea>
  @else
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type ?? 'text' }}" value="{{ $val }}" {!! $attrs ?? '' !!} @if ($err) aria-invalid="true" aria-describedby="{{ $id }}Err" @endif>
  @endif
  <p class="field__error" id="{{ $id }}Err">{{ $err }}</p>
  @if (! empty($hint))<p class="field__hint">{{ $hint }}</p>@endif
</div>
