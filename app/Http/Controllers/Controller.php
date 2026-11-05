<?php

namespace App\Http\Controllers;

use App\Services\Cart;
use App\Services\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Forms post normally and work without JavaScript; app.js sends the same forms
     * with fetch and gets JSON back (message + fresh header counts) instead of a redirect.
     */
    protected function done(Request $request, string $message, array $json = [], bool $ok = true, ?string $redirect = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => $ok,
                'message' => $message,
                'cartCount' => app(Cart::class)->count(),
                'wishCount' => app(Wishlist::class)->count(),
            ] + $json, $ok ? 200 : 422);
        }

        $to = $redirect ? redirect($redirect) : back();

        return $to->with('toast', ['message' => $message, 'type' => $ok ? 'success' : 'error'] + array_intersect_key($json, array_flip(['href', 'linkText'])));
    }
}
