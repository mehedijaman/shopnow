<?php

namespace Modules\CustomerAuth\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Customer\Models\Customer;
use Modules\Support\Http\Controllers\AppController;
use Modules\Customer\Http\Requests\CustomerValidate;
use Modules\CustomerAuth\Http\Requests\LoginRequest;

class AuthenticatedSessionController extends AppController
{
    /**
     * Display the login view.
     *
     * @return \Inertia\Response
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
     * @param  \Modules\CustomerAuth\Http\Requests\SignupRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signup(CustomerValidate $request)
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

        // Redirect to intended page or default route
        return redirect()->intended(route('site.index'));
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        // $request->authenticate();

        // $request->session()->regenerate();

        // // return redirect()->intended(route(config('modular.default-logged-route')));
        // return redirect()->intended(route('site.index'));

        if (Auth::guard('customer')->attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('site.index'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Inertia::location(config('modular.login-url'));
    }
}
