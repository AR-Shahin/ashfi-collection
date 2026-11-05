<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/** Wishlist for signed-in customers (database) and guests (session, merged on login). */
class Wishlist
{
    private const KEY = 'wishlist';

    private ?array $ids = null;

    public function ids(): array
    {
        if ($this->ids !== null) {
            return $this->ids;
        }
        $user = auth()->user();

        return $this->ids = $user
            ? $user->wishlist()->pluck('products.id')->all()
            : array_values(session(self::KEY, []));
    }

    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    /** @return bool true when the product was added */
    public function toggle(Product $product): bool
    {
        $this->ids = null;
        $user = auth()->user();
        if ($user) {
            $changes = $user->wishlist()->toggle([$product->id]);

            return ! empty($changes['attached']);
        }
        $ids = session(self::KEY, []);
        $added = ! in_array($product->id, $ids, true);
        $ids = $added ? [...$ids, $product->id] : array_values(array_diff($ids, [$product->id]));
        session([self::KEY => $ids]);

        return $added;
    }

    public function remove(Product $product): void
    {
        if ($this->has($product->id)) {
            $this->toggle($product);
        }
    }

    public function products(): Collection
    {
        $ids = $this->ids();

        return Product::with('category')->active()->whereIn('id', $ids)->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))->values();
    }

    public function count(): int
    {
        return count($this->ids());
    }

    /** Called after login/registration: keep anything the guest saved. */
    public function mergeGuest(): void
    {
        $ids = session(self::KEY, []);
        if ($ids && ($user = auth()->user())) {
            $valid = Product::whereIn('id', $ids)->pluck('id')->all();
            $user->wishlist()->syncWithoutDetaching($valid);
        }
        $this->ids = null;
        session()->forget(self::KEY);
    }
}
