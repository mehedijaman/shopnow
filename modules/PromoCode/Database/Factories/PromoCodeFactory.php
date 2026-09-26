<?php

namespace Modules\PromoCode\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\PromoCode\Models\PromoCode;

class PromoCodeFactory extends Factory
{
    protected $model = PromoCode::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('??####')),
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'minimum_order_amount' => 0,
            'maximum_discount_amount' => null,
            'usage_limit' => null,
            'per_customer_limit' => null,
            'starts_at' => null,
            'expires_at' => null,
            'active' => true,
        ];
    }

    public function fixedAmount(float $value = 200): static
    {
        return $this->state(fn () => [
            'discount_type' => 'fixed_amount',
            'discount_value' => $value,
        ]);
    }

    public function freeShipping(): static
    {
        return $this->state(fn () => [
            'discount_type' => 'free_shipping',
            'discount_value' => 0,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->addDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'active' => false,
        ]);
    }

    public function usageLimit(int $limit): static
    {
        return $this->state(fn () => [
            'usage_limit' => $limit,
        ]);
    }
}
