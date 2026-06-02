<?php

namespace Modules\CustomerAuth\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Customer\Http\Requests\CustomerValidate;
use Modules\Customer\Models\Customer;
use Modules\CustomerAuth\Http\Requests\LoginRequest;
use Modules\CustomerAuth\Http\Requests\SignupRequest;
use Modules\Settings\Services\MetaConversionApiService;
use Modules\Support\Http\Controllers\AppController;

class AuthenticatedSessionController extends AppController
{
    /**
     * Display the login view.
     *
     * @return Response
     */
    public function loginForm()
    {
        return inertia('CustomerAuth/LoginForm');
    }

    public function signupForm()
    {
        return inertia('CustomerAuth/SignupForm');
    }

    /**
     * Handle customer signup.
     *
     * @param  SignupRequest  $request
     * @return RedirectResponse
     */
    public function signup(CustomerValidate $request, MetaConversionApiService $metaConversionApiService)
    {
        // Create the customer
        $customer = Customer::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Log the customer in
        Auth::guard('customer')->login($customer);

        // Regenerate the session
        $request->session()->regenerate();

        $metaConversionApiService->track('CompleteRegistration', [
            'status' => true,
            'content_name' => 'Customer Signup',
        ], [
            'event_id' => 'registration_'.$customer->id,
            'event_source_url' => url('/signup'),
            'consent_granted' => $request->cookie('tracking_consent') === 'granted',
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
            'fbp' => $request->cookie('_fbp'),
            'fbc' => $request->cookie('_fbc'),
            'email' => $customer->email,
            'phone' => $customer->phone,
            'first_name' => $customer->name,
            'external_id' => (string) $customer->id,
        ]);

        // Redirect to intended page or default route
        return redirect()->intended(route('site.index'));
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        // $request->authenticate();

        // $request->session()->regenerate();

        // // return redirect()->intended(route(config('modular.default-logged-route')));
        // return redirect()->intended(route('site.index'));

        if (Auth::guard('customer')->attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            return redirect()->intended(route(config('modular.default-customer-logged-route')));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Inertia::location('/');
    }
}
