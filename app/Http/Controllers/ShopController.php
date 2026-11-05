<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public const SORTS = [
        'latest' => 'Latest',
        'price-asc' => 'Price: Low to High',
        'price-desc' => 'Price: High to Low',
        'discount' => 'Biggest Discount',
    ];

    public const PRICE_PRESETS = [
        [null, 2000, 'Under ৳2,000'],
        [2000, 4000, '৳2,000 – ৳4,000'],
        [4000, 6000, '৳4,000 – ৳6,000'],
        [6000, null, 'Over ৳6,000'],
    ];

    public function __invoke(Request $request)
    {
        // Old links used ?filter=new|best|sale
        $filter = $request->query('filter');
        $f = [
            'category' => (string) $request->query('category', ''),
            'q' => trim((string) $request->query('q', '')),
            'min' => is_numeric($request->query('min')) ? (int) $request->query('min') : null,
            'max' => is_numeric($request->query('max')) ? (int) $request->query('max') : null,
            'new' => $request->boolean('new') || $filter === 'new',
            'best' => $request->boolean('best') || $filter === 'best',
            'sale' => $request->boolean('sale') || $filter === 'sale',
            'sort' => array_key_exists($request->query('sort'), self::SORTS) ? $request->query('sort') : 'latest',
        ];

        $categories = Category::active()->withCount(['products' => fn ($q) => $q->active()])->get();
        $category = $categories->firstWhere('slug', $f['category']);
        $price = 'COALESCE(CASE WHEN sale_price > 0 AND sale_price < price THEN sale_price END, price)';

        $query = Product::with('category')->active()
            ->when($category, fn ($q) => $q->where('category_id', $category->id))
            ->when($f['q'] !== '', function ($q) use ($f) {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], mb_strtolower($f['q'])).'%';
                $q->where(fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(short) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(colors) LIKE ?', [$term])
                    ->orWhereHas('category', fn ($c) => $c->whereRaw('LOWER(name) LIKE ?', [$term])));
            })
            ->when($f['min'] !== null, fn ($q) => $q->whereRaw("$price >= ?", [$f['min']]))
            ->when($f['max'] !== null, fn ($q) => $q->whereRaw("$price <= ?", [$f['max']]))
            ->when($f['new'], fn ($q) => $q->where('is_new', true))
            ->when($f['best'], fn ($q) => $q->where('is_best_seller', true))
            ->when($f['sale'], fn ($q) => $q->whereNotNull('sale_price')->where('sale_price', '>', 0)->whereColumn('sale_price', '<', 'price'));

        match ($f['sort']) {
            'price-asc' => $query->orderByRaw("$price asc"),
            'price-desc' => $query->orderByRaw("$price desc"),
            'discount' => $query->orderByRaw('CASE WHEN sale_price > 0 AND sale_price < price THEN 1.0 - sale_price * 1.0 / price ELSE 0 END desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(24)->withQueryString();

        [$title, $lead] = match (true) {
            $f['q'] !== '' => ['Search results for “'.$f['q'].'”', $products->total().' '.str('product')->plural($products->total()).' found.'],
            $f['new'] && ! $category => ['New Arrivals', 'Fresh pieces, just landed.'],
            $f['best'] && ! $category => ['Best Sellers', 'Our customers’ favourites.'],
            $f['sale'] && ! $category => ['Sale & Offers', 'Beautiful pieces at special prices.'],
            (bool) $category => [$category->name, $category->blurb],
            default => ['All Products', 'Explore the full Ashfi Collection.'],
        };

        // Removable "chips" for every active filter
        $active = [];
        $params = array_filter([
            'category' => $category?->slug, 'q' => $f['q'] ?: null, 'min' => $f['min'], 'max' => $f['max'],
            'new' => $f['new'] ? 1 : null, 'best' => $f['best'] ? 1 : null, 'sale' => $f['sale'] ? 1 : null,
            'sort' => $f['sort'] !== 'latest' ? $f['sort'] : null,
        ], fn ($v) => $v !== null);
        $without = fn (string ...$keys) => route('shop', array_diff_key($params, array_flip($keys)));
        if ($category) {
            $active[] = [$category->name, $without('category')];
        }
        if ($f['q'] !== '') {
            $active[] = ['“'.$f['q'].'”', $without('q')];
        }
        if ($f['min'] !== null || $f['max'] !== null) {
            $active[] = [($f['min'] !== null ? money($f['min']) : '৳0').' – '.($f['max'] !== null ? money($f['max']) : 'any'), $without('min', 'max')];
        }
        foreach (['new' => 'New Arrivals', 'best' => 'Best Sellers', 'sale' => 'On Sale'] as $k => $label) {
            if ($f[$k]) {
                $active[] = [$label, $without($k)];
            }
        }

        return view('pages.shop', compact('products', 'categories', 'category', 'f', 'params', 'title', 'lead', 'active'));
    }
}
