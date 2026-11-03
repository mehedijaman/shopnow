<?php

namespace Modules\Settings\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Settings\Models\SmsMessage;

/** @extends Factory<SmsMessage> */
class SmsMessageFactory extends Factory
{
    protected $model = SmsMessage::class;

    public function definition(): array
    {
        return [
            'reference' => (string) Str::uuid(), 'event_key' => 'test:'.Str::uuid(),
            'purpose' => 'test', 'status' => 'pending', 'phone' => fake()->numerify('017########'),
            'body' => fake()->sentence(), 'expires_at' => now()->addDay(),
        ];
    }
}
