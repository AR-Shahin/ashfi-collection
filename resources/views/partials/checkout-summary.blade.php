<h2>Order Summary</h2>
@include('partials.mini-lines', ['items' => $lines])
<div class="sum-row"><span>Subtotal</span><span>{{ money($t->subtotal) }}</span></div>
@if ($t->discount)<div class="sum-row sum-row--save"><span>Coupon discount</span><span>−{{ money($t->discount) }}</span></div>@endif
<div class="sum-row"><span>Delivery charge</span><span>{{ $t->delivery === 0 ? 'Free' : money($t->delivery) }}</span></div>
<div class="sum-row sum-row--total"><span>Total</span><span>{{ money($t->total) }}</span></div>
@include('partials.coupon-box', ['view' => 'checkout'])
<p class="sum-note">Payment: Cash on Delivery</p>
