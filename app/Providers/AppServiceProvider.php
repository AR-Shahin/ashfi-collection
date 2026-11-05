<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\Cart;
use App\Services\Wishlist;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(Cart::class);
        $this->app->scoped(Wishlist::class);
    }

    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');

        RateLimiter::for('forms', fn (Request $r) => Limit::perMinute(10)->by($r->ip()));
        RateLimiter::for('auth', fn (Request $r) => Limit::perMinute(6)->by(strtolower((string) $r->input('email')).'|'.$r->ip()));

        // Everything the header, drawer, footer and bottom nav need.
        View::composer('layouts.app', function ($view) {
            $view->with([
                // Plain arrays: Laravel 13 does not unserialize cached objects.
                'navCategories' => collect(Cache::remember('nav.categories', 600, fn () => Category::active()->get(['slug', 'name'])->toArray()))->map(fn ($c) => (object) $c),
                'brand' => setting('brand'),
                'announcements' => setting('announcements', []),
                'cartCount' => app(Cart::class)->count(),
                'wishCount' => app(Wishlist::class)->count(),
            ]);
        });
    }
}
