<?php

namespace Modules\Product\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Product\Models\ProductBrand;

class ProductBrandFactory extends Factory
{
    protected $model = ProductBrand::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(4);

        return [
            'parent_id' => $this->faker->randomElement(ProductBrand::pluck('id')->toArray()),
            'name' => $name,
            'description' => $this->faker->realText(),
            'image' => $this->faker->imageUrl(),
            'active' => $this->faker->boolean(),
            'slug' => Str::slug($name),
            'meta_tag_title' => Str::limit($name, 60, ''),
            'meta_tag_description' => Str::limit($name, 160, ''),

            'created_by' => User::inRandomOrder()->first()->id,
            'updated_by' => User::inRandomOrder()->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
