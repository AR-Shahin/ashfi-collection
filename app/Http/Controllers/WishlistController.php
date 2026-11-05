<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(private Wishlist $wishlist) {}

    public function index()
    {
        return view('pages.wishlist', ['products' => $this->wishlist->products()]);
    }

    public function toggle(Request $request, Product $product)
    {
        $added = $this->wishlist->toggle($product);

        return $this->done($request, $added ? 'Saved to wishlist' : 'Removed from wishlist',
            ['saved' => $added] + ($added ? ['href' => route('wishlist'), 'linkText' => 'View'] : []));
    }
}
