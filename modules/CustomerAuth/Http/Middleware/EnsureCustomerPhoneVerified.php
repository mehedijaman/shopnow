<?php

namespace Modules\CustomerAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerPhoneVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $customer = Auth::guard('customer')->user();
        if ($customer?->requiresPhoneVerification() && ! $request->is('admin', 'admin/*') && ! $request->routeIs('customerAuth.phone.*', 'customerAuth.logout')) {
            Auth::guard('customer')->logout();
            $request->session()->regenerate();
            $request->session()->put('pending_customer_id', $customer->id);

            return redirect()->route('customerAuth.phone.show');
        }

        return $next($request);
    }
}
