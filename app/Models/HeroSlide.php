<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $guarded = ['id'];

    public const TRANSITIONS = ['curtain' => 'Curtain (burgundy panels)', 'iris' => 'Iris (circle reveal)', 'diagonal' => 'Diagonal wipe'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'duration' => 'integer'];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function isVideo(): bool
    {
        return filled($this->video_lg);
    }
}
