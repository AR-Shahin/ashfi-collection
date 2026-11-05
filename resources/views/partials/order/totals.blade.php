<div class="sum-row"><span>Subtotal</span><span>{{ money($order->subtotal) }}</span></div>
@if ($order->discount)<div class="sum-row sum-row--save"><span>Coupon ({{ $order->coupon_code }})</span><span>−{{ money($order->discount) }}</span></div>@endif
<div class="sum-row"><span>Delivery</span><span>{{ $order->delivery_charge ? money($order->delivery_charge) : 'Free' }}</span></div>
<div class="sum-row sum-row--total"><span>Total</span><span>{{ money($order->total) }}</span></div>
