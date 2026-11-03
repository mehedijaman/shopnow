<?php

namespace Modules\CustomerAuth\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\Customer\Http\Requests\CustomerValidate;
use Modules\Customer\Models\Customer;
use Modules\CustomerAuth\Http\Requests\LoginRequest;
use Modules\CustomerAuth\Services\RegistrationOtp;
use Modules\Settings\Services\MetaConversionApiService;
use Modules\Support\Http\Controllers\AppController;

class AuthenticatedSessionController extends AppController
{
    /**
     * Display the login view.
     *
     * @return View
     */
    public function loginForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->intended(route(config('modular.default-customer-logged-route')));
        }

        return view('customer-auth::login');
    }

    public function signupForm()
    {
        return view('customer-auth::signup');
    }

    /**
     * Handle customer signup.
     *
     * @param  SignupRequest  $request
     */
    public function signup(CustomerValidate $request, MetaConversionApiService $metaConversionApiService, RegistrationOtp $otp): RedirectResponse
    {
        // Create the customer
        $customer = DB::transaction(function () use ($request, $otp): Customer {
            $customer = Customer::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
                'date_of_birth' => $request->input('date_of_birth'),
                'gender' => $request->input('gender'),
                'locale' => app()->getLocale(),
            ]);
            if ($otp->requiredForNewCustomer()) {
                $customer->forceFill(['phone_verification_required' => true, 'registration_pending' => true])->save();
                $otp->issue($customer, $request->ip());
            }

            return $customer;
        });

        if ($customer->requiresPhoneVerification()) {
            Auth::guard('customer')->logout();
            $request->session()->regenerate();
            $request->session()->put('pending_customer_id', $customer->id);

            return redirect()->route('customerAuth.phone.show');
        }

        // Log the customer in
        Auth::guard('customer')->login($customer);

        // Regenerate the session
        $request->session()->regenerate();

        $metaConversionApiService->trackRegistration($customer, $request);

        // Redirect to intended page or default route
        return redirect()->intended(route('site.index'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $customer = Auth::guard('customer')->user();
        if ($customer->requiresPhoneVerification()) {
            Auth::guard('customer')->logout();
            $request->session()->put('pending_customer_id', $customer->id);

            return redirect()->route('customerAuth.phone.show');
        }

        $request->session()->forget('pending_customer_id');

        return redirect()->intended(route(config('modular.default-customer-logged-route')));
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

        return redirect('/');
    }
}
