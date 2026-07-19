<?php

namespace Modules\Cart\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Cart\Models\Cart;
use Symfony\Component\HttpFoundation\Response;

class ResolveCart
{
    public function handle(Request $request, Closure $next): Response
    {
        $cart = null;

        if (Auth::guard('customer')->check()) {
            $cart = Cart::firstOrCreate([
                'customer_id' => Auth::guard('customer')->id(),
            ]);
        } else {
            $token = $request->cookie('cart_token', $request->header('X-Cart-Token'));

            if ($token) {
                $cart = Cart::where('guest_token', $token)->first();
            }

            if (! $cart) {
                if (! $token || Cart::withTrashed()->where('guest_token', $token)->exists()) {
                    $token = (string) Str::uuid();
                    while (Cart::withTrashed()->where('guest_token', $token)->exists()) {
                        $token = (string) Str::uuid();
                    }
                }

                $cart = Cart::create(['guest_token' => $token]);
            }

            cookie()->queue(cookie()->forever('cart_token', $token, '/', null, false, false));
        }

        app()->instance(Cart::class, $cart);

        $request->merge(['resolvedCart' => $cart]);

        return $next($request);
    }
}
