<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        $products = Product::with('category')->active();

        return view('pages.home', [
            'slides' => HeroSlide::active()->get(),
            'categories' => Category::active()->get(),
            'featured' => (clone $products)->where('is_featured', true)->orderBy('featured_order')->take(8)->get(),
            'newArrivals' => (clone $products)->where('is_new', true)->latest()->take(10)->get(),
            'bestSellers' => (clone $products)->where('is_best_seller', true)->latest()->take(6)->get(),
            'testimonials' => Testimonial::active()->take(6)->get(),
        ]);
    }
}
