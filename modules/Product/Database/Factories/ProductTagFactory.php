<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Product\Models\ProductTag;

class ProductTagFactory extends Factory
{
    protected $model = ProductTag::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
