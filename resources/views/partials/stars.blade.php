<span class="stars" role="img" aria-label="{{ $rating }} out of 5 stars">@for ($i = 1; $i <= 5; $i++)<x-ui.icon name="star" :size="16" :class="$i <= $rating ? 'is-on' : ''" />@endfor</span>
