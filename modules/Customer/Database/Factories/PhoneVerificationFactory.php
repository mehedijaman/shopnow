<?php

namespace Modules\Customer\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\PhoneVerification;

/** @extends Factory<PhoneVerification> */
class PhoneVerificationFactory extends Factory
{
    protected $model = PhoneVerification::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'phone' => fn (array $attributes): string => Customer::findOrFail($attributes['customer_id'])->phone,
            'nonce' => (string) Str::uuid(),
            'digest' => fn (array $attributes): string => hash_hmac('sha256', $attributes['nonce'].'|'.$attributes['phone'].'|123456', config('app.key')),
            'attempts' => 0, 'last_sent_at' => now(), 'expires_at' => now()->addMinutes(5),
        ];
    }
}
