<?php

namespace Modules\Product\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(4);

        return [
            'category_id' => $this->faker->randomElement(ProductCategory::pluck('id')->toArray()),
            'brand_id' => $this->faker->randomElement(ProductBrand::pluck('id')->toArray()),

            'name' => $name,
            'slug' => Str::slug($name),
            'price' => $this->faker->numberBetween(100, 1000),
            'sale_price' => $this->faker->numberBetween(100, 1000),
            'quantity' => $this->faker->numberBetween(100, 1000),
            'unit' => $this->faker->word(),
            'min_order' => $this->faker->numberBetween(100, 1000),
            'active' => $this->faker->boolean(),
            'featured' => $this->faker->boolean(),
            'image' => $this->faker->imageUrl(),
            'summary' => $this->faker->realText(),
            'description' => $this->faker->realText(),
            'meta_tag_title' => Str::limit($name, 60, ''),
            'meta_tag_description' => Str::limit($name, 160, ''),

            'created_by' => User::inRandomOrder()->first()->id,
            'updated_by' => User::inRandomOrder()->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
