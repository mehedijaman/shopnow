<?php

namespace Modules\Customer\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Customer\Models\Customer;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->numerify('017########'),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password', // let the 'hashed' cast hash it dynamically
            'remember_token' => Str::random(10),
            'active' => $this->faker->boolean(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),

            'last_login' => $this->faker->dateTimeBetween('-1 year', '-6 month'),

            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => $this->faker->dateTimeBetween('-5 month', 'now'),
        ];
    }
}
