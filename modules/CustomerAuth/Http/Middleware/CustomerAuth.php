<?php

namespace Modules\CustomerAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('customer')->guest()) {
            return redirect()->route('customerAuth.loginForm')
                ->withErrors(['email' => 'Session expired, please login again.']);
        }

        return $next($request);
    }
}
