<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session cart. One source of truth for money – the same rules the static site
 * the static site used in js/store.js, now enforced on the server.
 */
class Cart
{
    private const KEY = 'cart.lines';

    private const COUPON = 'cart.coupon';

    private ?Collection $cache = null;

    /* ---------------------------------------------------------- lines */

    public static function key(int $productId, ?string $size, ?string $color): string
    {
        return $productId.'|'.($size ?? '').'|'.($color ?? '');
    }

    public static function maxQty(): int
    {
        return (int) config('store.max_qty', 10);
    }

    private function raw(): array
    {
        return session(self::KEY, []);
    }

    private function save(array $lines): void
    {
        session([self::KEY => $lines]);
        $this->cache = null;
    }

    public function add(Product $product, ?string $size, ?string $color, int $qty = 1): void
    {
        $lines = $this->raw();
        $key = self::key($product->id, $size, $color);
        $current = $lines[$key]['qty'] ?? 0;
        $lines[$key] = [
            'product_id' => $product->id,
            'size' => $size,
            'color' => $color,
            'qty' => max(1, min(self::maxQty(), $current + $qty)),
        ];
        $this->save($lines);
    }

    public function setQty(string $key, int $qty): void
    {
        $lines = $this->raw();
        if (! isset($lines[$key])) {
            return;
        }
        if ($qty < 1) {
            unset($lines[$key]);
        } else {
            $lines[$key]['qty'] = min(self::maxQty(), $qty);
        }
        $this->save($lines);
    }

    public function remove(string $key): void
    {
        $lines = $this->raw();
        unset($lines[$key]);
        $this->save($lines);
    }

    public function clear(): void
    {
        session()->forget([self::KEY, self::COUPON]);
        $this->cache = null;
    }

    /** Lines joined with live products; lines whose product disappeared are dropped. */
    public function lines(): Collection
    {
        if ($this->cache) {
            return $this->cache;
        }
        $raw = $this->raw();
        $products = Product::with('category')->active()->whereIn('id', array_column($raw, 'product_id'))->get()->keyBy('id');

        return $this->cache = collect($raw)
            ->filter(fn ($l) => $products->has($l['product_id']))
            ->map(function ($l, $key) use ($products) {
                $p = $products[$l['product_id']];
                $unit = $p->unitPrice();

                return (object) [
                    'key' => $key,
                    'product' => $p,
                    'size' => $l['size'],
                    'color' => $l['color'],
                    'qty' => (int) $l['qty'],
                    'unit' => $unit,
                    'total' => $unit * (int) $l['qty'],
                    'meta' => collect([$l['color'], $l['size'] && $l['size'] !== 'Free Size' ? 'Size '.$l['size'] : $l['size']])->filter()->implode(' · '),
                ];
            });
    }

    public function count(): int
    {
        return (int) collect($this->raw())->sum('qty');
    }

    public function isEmpty(): bool
    {
        return $this->lines()->isEmpty();
    }

    public function subtotal(): int
    {
        return (int) $this->lines()->sum('total');
    }

    /* -------------------------------------------------------- coupons */

    public static function findCoupon(?string $code): ?Coupon
    {
        $code = strtoupper(trim((string) $code));

        return $code === '' ? null : Coupon::where('code', $code)->where('is_active', true)->first();
    }

    /** @return array{ok: bool, message: string} */
    public function applyCoupon(?string $code): array
    {
        $c = self::findCoupon($code);
        if (! $c) {
            return ['ok' => false, 'message' => 'That coupon code isn’t valid.'];
        }
        if ($c->isExpired()) {
            return ['ok' => false, 'message' => 'That coupon has expired.'];
        }
        if ($this->subtotal() < $c->min_spend) {
            return ['ok' => false, 'message' => $c->code.' needs a minimum spend of '.money($c->min_spend).'.'];
        }
        session([self::COUPON => $c->code]);

        return ['ok' => true, 'message' => $c->code.' applied.'];
    }

    public function removeCoupon(): void
    {
        session()->forget(self::COUPON);
    }

    /** What the applied coupon is worth against this subtotal, and why if it is worth nothing. */
    public function couponState(int $subtotal): array
    {
        $c = self::findCoupon(session(self::COUPON));
        if (! $c || $c->isExpired()) {
            return ['coupon' => null, 'discount' => 0, 'freeDelivery' => false, 'message' => ''];
        }
        if ($subtotal < $c->min_spend) {
            return ['coupon' => $c, 'discount' => 0, 'freeDelivery' => false,
                'message' => 'Add '.money($c->min_spend - $subtotal).' more to use '.$c->code.'.'];
        }
        $discount = match ($c->type) {
            'percent' => min((int) round($subtotal * $c->value / 100), $c->max_discount ?: PHP_INT_MAX),
            'flat' => min($c->value, $subtotal),
            default => 0,
        };

        return ['coupon' => $c, 'discount' => $discount, 'freeDelivery' => $c->type === 'shipping', 'message' => ''];
    }

    /* ------------------------------------------------------- delivery */

    public static function deliveryCharge(string $area, int $subtotal): int
    {
        $d = setting('delivery');
        if ($subtotal <= 0 || $subtotal >= (int) $d['freeOver']) {
            return 0;
        }

        return $area === 'outside' ? (int) $d['outsideDhaka'] : (int) $d['insideDhaka'];
    }

    public static function areaFor(?string $district): string
    {
        return $district === 'Dhaka' ? 'inside' : 'outside';
    }

    /** Pass no area while delivery is still unknown (cart page). */
    public function totals(?string $area = null): object
    {
        $sub = $this->subtotal();
        $state = $this->couponState($sub);
        $delivery = $area ? ($state['freeDelivery'] ? 0 : self::deliveryCharge($area, $sub)) : null;
        $freeOver = (int) setting('delivery.freeOver');

        return (object) [
            'count' => (int) $this->lines()->sum('qty'),
            'subtotal' => $sub,
            'discount' => $state['discount'],
            'delivery' => $delivery,
            'freeDelivery' => $state['freeDelivery'],
            'total' => $sub - $state['discount'] + ($delivery ?? 0),
            'coupon' => $state['coupon'],
            'couponMessage' => $state['message'],
            'freeOver' => $freeOver,
            'leftForFree' => max(0, $freeOver - $sub),
            'progress' => $freeOver ? min(100, (int) round($sub / $freeOver * 100)) : 100,
        ];
    }
}
