<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = ['id'];

    public const STATUSES = [
        'placed' => 'Order placed',
        'confirmed' => 'Confirmed',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    public const PAYMENT_METHODS = ['cod' => 'Cash on Delivery'];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (! $order->number) {
                do {
                    $number = 'AC-'.strtoupper(bin2hex(random_bytes(4)));
                } while (static::where('number', $number)->exists());
                $order->number = $number;
            }
        });

        static::saving(function (Order $order) {
            if ($order->isDirty('status')) {
                $col = $order->status.'_at';
                if (in_array($order->status, ['confirmed', 'shipped', 'delivered', 'cancelled'], true) && ! $order->{$col}) {
                    $order->{$col} = now();
                }
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function canCancel(): bool
    {
        return $this->status === 'placed';
    }

    public function itemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /** Timeline steps for the order tracker. */
    public function steps(): array
    {
        if ($this->status === 'cancelled') {
            return [
                ['label' => 'Order placed', 'at' => $this->created_at, 'done' => true],
                ['label' => 'Cancelled', 'at' => $this->cancelled_at, 'done' => true, 'cancel' => true],
            ];
        }
        $order = ['placed', 'confirmed', 'shipped', 'delivered'];
        $reached = array_search($this->status, $order, true);
        $out = [];
        foreach ($order as $i => $s) {
            $out[] = [
                'label' => self::STATUSES[$s],
                'at' => $s === 'placed' ? $this->created_at : $this->{$s.'_at'},
                'done' => $i <= $reached,
                'current' => $i === $reached,
            ];
        }

        return $out;
    }
}
