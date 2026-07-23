<?php

namespace Modules\Slider\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Slider\Models\Slider;

class SliderFactory extends Factory
{
    protected $model = Slider::class;

    public function definition(): array
    {

        return [
            'title' => $this->faker->sentence(3), // Generates a 3-word sentence
            'description' => $this->faker->paragraph, // Generates a random paragraph
            'url' => $this->faker->url, // Generates a random URL
            'order' => $this->faker->numberBetween(1, 10), // Generates a random number between 1 and 10
            'active' => $this->faker->boolean(90), // 90% chance of being true
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Random date within the past year
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Random date within the past year
        ];
    }
}
