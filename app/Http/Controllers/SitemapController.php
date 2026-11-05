<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $static = ['home' => 1.0, 'shop' => 0.9, 'offers' => 0.7, 'about' => 0.5, 'contact' => 0.5, 'faq' => 0.5,
            'size-guide' => 0.4, 'policy' => 0.3, 'terms' => 0.3, 'track' => 0.3];

        return response()->view('sitemap', [
            'static' => $static,
            'categories' => Category::active()->get(),
            'products' => Product::active()->latest('updated_at')->get(['slug', 'updated_at']),
        ])->header('Content-Type', 'application/xml');
    }
}
