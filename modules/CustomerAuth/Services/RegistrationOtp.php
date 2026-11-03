<?php

namespace Modules\CustomerAuth\Services;

use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\PhoneVerification;
use Modules\Settings\Models\SmsMessage;
use Modules\Settings\Services\SmsMessages;
use Modules\Settings\Services\SmsSettings;

class RegistrationOtp
{
    public function __construct(private SmsSettings $settings, private SmsMessages $messages) {}

    public function requiredForNewCustomer(): bool
    {
        return (bool) $this->settings->values()['otp_enabled'];
    }

    public function issue(Customer $customer, string $ip): void
    {
        $this->synchronized($customer, function () use ($customer, $ip): void {
            DB::transaction(function () use ($customer, $ip): void {
                $this->issueLocked($customer, $ip);

            });
        });
    }

    public function changePhone(Customer $customer, string $phone, string $ip): void
    {
        $this->synchronized($customer, function () use ($customer, $phone, $ip): void {
            DB::transaction(function () use ($customer, $phone, $ip): void {
                $customer = Customer::query()->lockForUpdate()->findOrFail($customer->id);
                if (! $customer->requiresPhoneVerification()) {
                    throw ValidationException::withMessages(['phone' => __('customer-auth::otp.invalid_phone')]);
                }
                $customer->update(['phone' => $phone]);
                $this->issueLocked($customer, $ip);
            });
        });
    }

    private function issueLocked(Customer $customer, string $ip): void
    {
        $customer = Customer::query()->lockForUpdate()->findOrFail($customer->id);
        if (! $customer->requiresPhoneVerification()) {
            return;
        }
        $settings = $this->settings->values();
        if (! $settings['enabled']) {
            throw ValidationException::withMessages(['code' => __('customer-auth::otp.unavailable')]);
        }
        $challenge = PhoneVerification::query()->where('customer_id', $customer->id)->first();
        if ($challenge && $challenge->last_sent_at->addSeconds((int) $settings['otp_cooldown_seconds'])->isFuture()) {
            throw ValidationException::withMessages(['code' => __('customer-auth::otp.cooldown')]);
        }
        $phone = SmsSettings::phone($customer->phone);
        if ($phone === null) {
            throw ValidationException::withMessages(['phone' => __('customer-auth::otp.invalid_phone')]);
        }
        $keys = [
            'sms-otp-hour:'.$customer->id => [5, 3600],
            'sms-otp-day:'.hash('sha256', $phone) => [10, 86400],
            'sms-otp-ip:'.hash('sha256', $ip) => [20, 3600],
        ];
        foreach ($keys as $key => [$limit, $decay]) {
            if (RateLimiter::tooManyAttempts($key, $limit)) {
                throw ValidationException::withMessages(['code' => __('customer-auth::otp.too_many')]);
            }
        }
        if (RateLimiter::tooManyAttempts('sms-otp-verify:'.$customer->id, 10)) {
            throw ValidationException::withMessages(['code' => __('customer-auth::otp.too_many')]);
        }
        foreach ($keys as $key => [$limit, $decay]) {
            RateLimiter::hit($key, $decay);
        }
        $nonce = (string) Str::uuid();
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = now()->addMinutes((int) $settings['otp_expiry_minutes']);
        SmsMessage::query()->where('customer_id', $customer->id)->where('purpose', 'otp')->where('status', 'pending')
            ->update(['status' => 'cancelled', 'body' => null, 'finished_at' => now()]);
        $challenge = PhoneVerification::query()->updateOrCreate(['customer_id' => $customer->id], [
            'phone' => $phone, 'nonce' => $nonce, 'digest' => $this->digest($nonce, $phone, $code),
            'attempts' => 0, 'expires_at' => $expires, 'last_sent_at' => now(), 'consumed_at' => null,
        ]);
        $locale = $customer->locale === 'bn' ? 'bn' : 'en';
        $body = $this->settings->render($settings['otp_'.$locale], ['otp' => $code, 'minutes' => $settings['otp_expiry_minutes']]);
        $this->messages->create('otp:'.$nonce, 'otp', $phone, $body, [
            'customer_id' => $customer->id, 'verification_id' => $challenge->id, 'expires_at' => $expires,
        ]);
    }

    public function verify(Customer $customer, string $code): bool
    {
        return $this->synchronized($customer, function () use ($customer, $code): bool {
            return DB::transaction(function () use ($customer, $code): bool {
                $customer = Customer::query()->lockForUpdate()->findOrFail($customer->id);
                $key = 'sms-otp-verify:'.$customer->id;
                $challenge = PhoneVerification::query()->where('customer_id', $customer->id)->lockForUpdate()->first();
                $maxAttempts = (int) $this->settings->values()['otp_max_attempts'];
                if (! $customer->requiresPhoneVerification() || ! $challenge || $challenge->consumed_at || $challenge->expires_at->isPast()
                    || $challenge->phone !== $customer->phone || $challenge->attempts >= $maxAttempts || RateLimiter::tooManyAttempts($key, 10)) {
                    return false;
                }
                $challenge->increment('attempts');
                RateLimiter::hit($key, 3600);
                if (! hash_equals($challenge->digest, $this->digest($challenge->nonce, $challenge->phone, $code))) {
                    return false;
                }
                $challenge->update(['consumed_at' => now()]);
                $customer->forceFill(['phone_verified_at' => now(), 'registration_pending' => false])->save();
                SmsMessage::query()->where('customer_id', $customer->id)->where('purpose', 'otp')->where('status', 'pending')
                    ->update(['status' => 'cancelled', 'body' => null, 'finished_at' => now()]);
                RateLimiter::clear($key);

                return true;
            });
        });
    }

    private function synchronized(Customer $customer, Closure $callback): mixed
    {
        try {
            return Cache::lock('sms-otp-customer:'.$customer->id, 60)->block(5, $callback);
        } catch (LockTimeoutException) {
            throw ValidationException::withMessages(['code' => __('customer-auth::otp.cooldown')]);
        }
    }

    private function digest(string $nonce, string $phone, string $code): string
    {
        return hash_hmac('sha256', $nonce.'|'.$phone.'|'.$code, config('app.key'));
    }
}
