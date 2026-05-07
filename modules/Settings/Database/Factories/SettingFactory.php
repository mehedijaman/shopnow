<?php

namespace Modules\Settings\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Settings\Models\Setting;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'group' => $this->faker->randomElement(['general', 'branding', 'social', 'seo']),
            'key' => $this->faker->unique()->word(),
            'value' => $this->faker->sentence(3),
            'type' => 'text',
            'label' => $this->faker->words(2, true),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
