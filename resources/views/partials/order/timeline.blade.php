@if ($order->status === 'cancelled')
  <p class="notice"><x-ui.icon name="info" :size="18" /><span>This order was cancelled on {{ ($order->cancelled_at ?? $order->updated_at)->format('j M Y') }}.</span></p>
@else
  <ol class="steps">
    @foreach ($order->steps() as $s)
      <li @class(['steps__item', 'is-done' => $s['done'], 'is-current' => $s['current'] ?? false]) @if ($s['current'] ?? false) aria-current="step" @endif>
        <span class="steps__dot">@if ($s['done'])<x-ui.icon name="check" :size="14" />@endif</span>
        <strong>{{ $s['label'] }}</strong>@if ($s['at'])<small>{{ $s['at']->format('j M Y') }}</small>@endif
      </li>
    @endforeach
  </ol>
@endif
