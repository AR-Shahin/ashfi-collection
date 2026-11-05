<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/** Imports the catalogue that used to live in js/data.js (exported to data/storefront.json). */
class StorefrontSeeder extends Seeder
{
    public function run(): void
    {
        $d = json_decode(file_get_contents(__DIR__.'/data/storefront.json'), true, flags: JSON_THROW_ON_ERROR);

        Setting::put('brand', $d['brand']);
        Setting::put('announcements', $d['announcements']);
        Setting::put('delivery', $d['delivery']);

        $categories = [];
        foreach ($d['categories'] as $i => $c) {
            if (($c['slug'] ?? '') === 'new') {
                continue; // "New Arrivals" is a filter, not a real category
            }
            $categories[$c['slug']] = Category::updateOrCreate(['slug' => $c['slug']], [
                'name' => $c['name'],
                'blurb' => $c['blurb'] ?? null,
                'image' => $c['image'] ?? null,
                'sort_order' => $i,
                'is_active' => true,
            ])->id;
        }

        $featured = array_flip($d['featuredIds']);
        foreach ($d['products'] as $p) {
            $product = Product::updateOrCreate(['slug' => $p['id']], [
                'category_id' => $categories[$p['category']],
                'name' => $p['name'],
                'short' => $p['short'] ?? null,
                'description' => $p['description'] ?? null,
                'price' => $p['price'],
                'sale_price' => $p['salePrice'] ?? null,
                'sizes' => $p['sizes'] ?? [],
                'colors' => $p['colors'] ?? [],
                'details' => collect($p['details'] ?? [])->map(fn ($v, $k) => ['label' => $k, 'value' => $v])->values()->all(),
                'images' => $p['images'] ?? [],
                'is_new' => (bool) ($p['isNew'] ?? false),
                'is_best_seller' => (bool) ($p['isBestSeller'] ?? false),
                'is_featured' => isset($featured[$p['id']]),
                'featured_order' => $featured[$p['id']] ?? 0,
                'is_active' => true,
            ]);
            if (! empty($p['createdAt'])) {
                $product->forceFill(['created_at' => Carbon::parse($p['createdAt'])])->saveQuietly();
            }
        }

        foreach ($d['heroSlides'] as $i => $s) {
            HeroSlide::updateOrCreate(['title' => $s['title']], [
                'motto' => $s['motto'] ?? null,
                'motto_lang' => $s['mottoLang'] ?? null,
                'description' => $s['description'] ?? null,
                'image_lg' => $s['image']['lg'],
                'image_md' => $s['image']['md'] ?? null,
                'image_sm' => $s['image']['sm'] ?? null,
                'image_alt' => $s['image']['alt'] ?? null,
                'video_lg' => $s['video']['lg'] ?? null,
                'video_sm' => $s['video']['sm'] ?? null,
                'primary_label' => $s['primary']['label'] ?? null,
                'primary_url' => $this->url($s['primary']['href'] ?? null),
                'secondary_label' => $s['secondary']['label'] ?? null,
                'secondary_url' => $this->url($s['secondary']['href'] ?? null),
                'transition' => $s['transition'] ?? 'fade',
                'align' => $s['align'] ?? 'left',
                'duration' => $s['duration'] ?? 7000,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }

        foreach ($d['coupons'] as $c) {
            Coupon::updateOrCreate(['code' => $c['code']], [
                'type' => $c['type'],
                'value' => $c['value'] ?? 0,
                'max_discount' => $c['maxDiscount'] ?? null,
                'min_spend' => $c['minSpend'] ?? 0,
                'expires_at' => $c['expires'] ?? null,
                'title' => $c['title'] ?? null,
                'detail' => $c['detail'] ?? null,
                'is_active' => true,
            ]);
        }

        foreach ($d['testimonials'] as $i => $t) {
            Testimonial::updateOrCreate(['name' => $t['name']], [
                'place' => $t['place'] ?? null,
                'rating' => $t['rating'] ?? 5,
                'text' => $t['text'],
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }

    /** Turns the old static links ("shop.html?filter=new") into site-relative URLs. */
    private function url(?string $href): ?string
    {
        if (! $href || str_starts_with($href, '#') || str_starts_with($href, 'http')) {
            return $href;
        }
        [$path, $query] = array_pad(explode('?', $href, 2), 2, null);
        $path = preg_replace('/\.html$/', '', $path);
        $path = $path === 'index' ? '/' : '/'.$path;
        if ($path === '/product' && $query && preg_match('/id=([^&]+)/', $query, $m)) {
            return '/product/'.$m[1];
        }

        return $path.($query ? '?'.$query : '');
    }
}
