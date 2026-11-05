<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/** Permanently redirects the old static-site URLs (shop.html?filter=new, product.html?id=…) to the new routes. */
class LegacyRedirectController extends Controller
{
    private const MAP = [
        'index' => '/', 'shop' => '/shop', 'cart' => '/cart', 'checkout' => '/checkout', 'wishlist' => '/wishlist',
        'offers' => '/offers', 'track-order' => '/track-order', 'faq' => '/faq', 'size-guide' => '/size-guide',
        'about' => '/about', 'contact' => '/contact', 'policy' => '/policy', 'terms' => '/terms',
        'login' => '/login', 'register' => '/register', 'forgot-password' => '/forgot-password',
        'account' => '/account', 'account-orders' => '/account/orders', 'account-addresses' => '/account/addresses',
        'account-profile' => '/account/profile',
    ];

    public function __invoke(Request $request, string $page)
    {
        $query = $request->query();
        if ($page === 'product' && ! empty($query['id'])) {
            return redirect('/product/'.rawurlencode($query['id']), 301);
        }
        if ($page === 'account-order' && ! empty($query['order'])) {
            return redirect('/account/orders/'.rawurlencode($query['order']), 301);
        }
        abort_unless(isset(self::MAP[$page]), 404);

        return redirect(self::MAP[$page].($query ? '?'.http_build_query($query) : ''), 301);
    }
}
