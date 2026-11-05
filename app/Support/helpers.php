<?php

use App\Models\Setting;
use Illuminate\Support\Str;

if (! function_exists('setting')) {
    /** Read a store setting (brand.*, announcements, delivery.*), falling back to config/store.php. */
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            $value = Setting::get($key);
        } catch (\Throwable) {
            $value = null;
        }

        return $value ?? config('store.defaults.'.$key, $default);
    }
}

if (! function_exists('money')) {
    /** Format whole taka: 1990 → ৳1,990 */
    function money(int|float|null $amount): string
    {
        return '৳'.number_format((int) round($amount ?? 0));
    }
}

if (! function_exists('media_url')) {
    /** Public URL for a stored image or video path ("images/…", "uploads/…") or a full URL. */
    function media_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }
        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        // Everything else lives under public/ (seeded images/… and admin uploads/…).
        return asset(ltrim($path, '/'));
    }
}

if (! function_exists('normalize_phone')) {
    /** Strip spaces/dashes and a leading +88 / 88 so numbers compare as 01XXXXXXXXX. */
    function normalize_phone(?string $phone): string
    {
        $p = preg_replace('/[\s\-()]/', '', (string) $phone);

        return preg_replace('/^\+?88(?=01)/', '', $p);
    }
}
