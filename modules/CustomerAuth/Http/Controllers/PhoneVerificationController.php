<?php

namespace Modules\CustomerAuth\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\PhoneVerification;
use Modules\CustomerAuth\Http\Requests\ChangePendingPhoneRequest;
use Modules\CustomerAuth\Http\Requests\VerifyPhoneRequest;
use Modules\CustomerAuth\Services\RegistrationOtp;
use Modules\Settings\Services\MetaConversionApiService;
use Modules\Settings\Services\SmsSettings;
use Modules\Support\Http\Controllers\AppController;

class PhoneVerificationController extends AppController
{
    public function show(Request $request, SmsSettings $settings): View|RedirectResponse
    {
        $customer = $this->pendingCustomer($request);
        if (! $customer) {
            return redirect()->route('customerAuth.loginForm');
        }
        $challenge = PhoneVerification::query()->where('customer_id', $customer->id)->first();

        return view('customer-auth::verify-phone', [
            'phone' => substr($customer->phone, 0, 3).'*****'.substr($customer->phone, -3),
            'resendAt' => $challenge?->last_sent_at->addSeconds((int) $settings->values()['otp_cooldown_seconds']),
        ]);
    }

    public function verify(VerifyPhoneRequest $request, RegistrationOtp $otp, MetaConversionApiService $tracking): RedirectResponse
    {
        $customer = $this->pendingCustomer($request);
        if (! $customer || ! $otp->verify($customer, $request->validated('code'))) {
            throw ValidationException::withMessages(['code' => __('customer-auth::otp.invalid_code')]);
        }
        $request->session()->forget('pending_customer_id');
        Auth::guard('customer')->login($customer->fresh());
        $request->session()->regenerate();
        if ($customer->registration_pending) {
            $tracking->trackRegistration($customer->fresh(), $request);
        }

        return redirect()->intended(route('site.index'))->with('success', __('customer-auth::otp.verified'));
    }

    public function changePhone(ChangePendingPhoneRequest $request, RegistrationOtp $otp): RedirectResponse
    {
        $customer = $this->pendingCustomer($request);
        abort_unless($customer, 403);
        $otp->changePhone($customer, $request->validated('phone'), $request->ip());

        return back()->with('success', __('customer-auth::otp.queued'));
    }

    public function resend(Request $request, RegistrationOtp $otp): RedirectResponse
    {
        $customer = $this->pendingCustomer($request);
        abort_unless($customer, 403);
        $otp->issue($customer, $request->ip());

        return back()->with('success', __('customer-auth::otp.queued'));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget('pending_customer_id');
        $request->session()->regenerate();

        return redirect()->route('customerAuth.loginForm');
    }

    private function pendingCustomer(Request $request): ?Customer
    {
        $customer = Customer::find($request->session()->get('pending_customer_id'));

        return $customer?->requiresPhoneVerification() ? $customer : null;
    }
}
