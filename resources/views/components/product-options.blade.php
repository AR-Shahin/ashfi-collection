{{-- Colour swatches, size chips and quantity for the product page and quick view. Lives inside a cart form. --}}
@props(['product', 'hint' => null])
@php
    $colors = $product->colorList();
    $sizes = $product->sizeList();
    $single = count($sizes) === 1;
    $oldColor = old('color', $colors[0]['name'] ?? null);
@endphp
@if ($colors)
<fieldset class="opt"><legend>Colour: <strong class="js-color-name">{{ $oldColor }}</strong></legend><div class="opt__row">
  @foreach ($colors as $c)
    <label class="swatch" title="{{ $c['name'] }}"><input type="radio" name="color" value="{{ $c['name'] }}" @checked($oldColor === $c['name'])><span style="--sw:{{ preg_match('/^#[0-9a-fA-F]{3,8}$/', $c['hex'] ?? '') ? $c['hex'] : '#ccc' }}"></span><span class="sr-only">{{ $c['name'] }}</span></label>
  @endforeach
</div></fieldset>
@endif
@if ($sizes)
<fieldset class="opt"><legend>Size @if ($single): <strong>{{ $sizes[0] }}</strong>@endif</legend><div class="opt__row">
  @foreach ($sizes as $s)
    <label class="chip"><input type="radio" name="size" value="{{ $s }}" @checked($single || old('size') === $s) @if (! $single) required @endif><span>{{ $s }}</span></label>
  @endforeach
</div>
<p class="opt__error" role="alert" @if (! $hint) hidden @endif>{{ $hint ?: 'Please select a size.' }}</p></fieldset>
@endif
