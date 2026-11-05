<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load('category');

        $related = Product::with('category')->active()->whereKeyNot($product->id)
            ->where('category_id', $product->category_id)->latest()->take(4)->get();
        if ($related->count() < 4) {
            $related = $related->concat(Product::with('category')->active()
                ->whereNotIn('id', $related->pluck('id')->push($product->id))
                ->latest()->take(4 - $related->count())->get());
        }

        return view('pages.product', compact('product', 'related'));
    }

    /** Dialog body for the quick-view popup (loaded by app.js). */
    public function quickView(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('partials.quick-view', ['product' => $product->load('category'), 'hint' => $request->query('hint')]);
    }

    /** Live search for the header search panel. */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 1) {
            return response()->json(['results' => []]);
        }
        $term = '%'.str_replace(['%', '_'], ['\%', '\_'], mb_strtolower($q)).'%';
        $results = Product::with('category')->active()
            ->where(fn ($w) => $w->whereRaw('LOWER(name) LIKE ?', [$term])
                ->orWhereRaw('LOWER(short) LIKE ?', [$term])
                ->orWhereRaw('LOWER(colors) LIKE ?', [$term])
                ->orWhereHas('category', fn ($c) => $c->whereRaw('LOWER(name) LIKE ?', [$term])))
            ->latest()->take(5)->get()
            ->map(fn (Product $p) => ['name' => $p->name, 'url' => $p->url(), 'image' => $p->imageUrl(), 'price' => money($p->unitPrice())]);

        return response()->json(['results' => $results, 'all' => route('shop', ['q' => $q])]);
    }
}
