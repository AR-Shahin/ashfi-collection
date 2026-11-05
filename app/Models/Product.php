<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'sizes' => 'array',
            'colors' => 'array',
            'details' => 'array',
            'images' => 'array',
            'is_new' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'price' => 'integer',
            'sale_price' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Price the customer pays: the sale price when it is lower than the regular price. */
    public function unitPrice(): int
    {
        return $this->onSale() ? (int) $this->sale_price : (int) $this->price;
    }

    public function onSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price > 0 && $this->sale_price < $this->price;
    }

    public function discountPct(): int
    {
        return $this->onSale() ? (int) round((1 - $this->sale_price / $this->price) * 100) : 0;
    }

    public function image(int $i = 0): ?string
    {
        $images = $this->images ?? [];

        return $images[$i] ?? $images[0] ?? null;
    }

    public function imageUrl(int $i = 0): string
    {
        return media_url($this->image($i)) ?? asset('images/og-cover.png');
    }

    public function url(): string
    {
        return route('product.show', $this);
    }

    public function sizeList(): array
    {
        return array_values(array_filter($this->sizes ?? []));
    }

    public function colorList(): array
    {
        return array_values(array_filter($this->colors ?? [], fn ($c) => ! empty($c['name'])));
    }
}
