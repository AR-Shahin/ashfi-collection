<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegacyRedirectController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Catalogue
Route::get('/shop', ShopController::class)->name('shop');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/product/{product:slug}/quick-view', [ProductController::class, 'quickView'])->name('product.quick-view');
Route::get('/search', [ProductController::class, 'search'])->name('search');

// Cart & coupons
Route::get('/cart', [CartController::class, 'show'])->name('cart');
Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::delete('/cart-coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Wishlist
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/{product:slug}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

// Checkout & orders
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::get('/checkout/summary', [CheckoutController::class, 'summary'])->name('checkout.summary');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:forms')->name('checkout.store');
Route::get('/order-success/{order:number}', [CheckoutController::class, 'success'])->name('order.success');
Route::get('/track-order', [OrderTrackingController::class, 'show'])->name('track');
Route::post('/track-order', [OrderTrackingController::class, 'track'])->middleware('throttle:forms')->name('track.lookup');

// Content pages
Route::controller(PageController::class)->group(function () {
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'sendMessage')->middleware('throttle:forms')->name('contact.send');
    Route::post('/newsletter', 'subscribe')->middleware('throttle:forms')->name('newsletter');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/offers', 'offers')->name('offers');
    Route::get('/policy', 'policy')->name('policy');
    Route::get('/size-guide', 'sizeGuide')->name('size-guide');
    Route::get('/terms', 'terms')->name('terms');
});

// Customer auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:forms');
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:forms')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:forms')->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// My account
Route::middleware('auth')->prefix('account')->name('account.')->controller(AccountController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/orders', 'orders')->name('orders');
    Route::get('/orders/{order:number}', 'order')->name('order');
    Route::post('/orders/{order:number}/cancel', 'cancel')->name('order.cancel');
    Route::post('/orders/{order:number}/reorder', 'reorder')->name('order.reorder');
    Route::get('/addresses', 'addresses')->name('addresses');
    Route::post('/addresses', 'storeAddress')->name('addresses.store');
    Route::put('/addresses/{address}', 'updateAddress')->name('addresses.update');
    Route::post('/addresses/{address}/default', 'defaultAddress')->name('addresses.default');
    Route::delete('/addresses/{address}', 'deleteAddress')->name('addresses.destroy');
    Route::get('/profile', 'profile')->name('profile');
    Route::put('/profile', 'updateProfile')->name('profile.update');
    Route::put('/password', 'updatePassword')->name('password');
});

// SEO
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// Old static-site links (index.html, product.html?id=…) keep working.
Route::get('/{page}.html', LegacyRedirectController::class)->where('page', '[a-z0-9\-]+');
Route::get('/robots.txt', fn () => response("User-agent: *\nDisallow: /admin\nDisallow: /account\nDisallow: /cart\nDisallow: /checkout\nDisallow: /order-success\nDisallow: /search\n\nSitemap: ".route('sitemap')."\n")
    ->header('Content-Type', 'text/plain'))->name('robots');
