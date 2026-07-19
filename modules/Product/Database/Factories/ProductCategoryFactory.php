<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Product\Models\ProductCategory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'parent_id' => null,
            'name' => $name,
            'description' => $this->faker->realText(200),
            'image' => $this->faker->imageUrl(640, 480, 'product-category'),
            'active' => true,
            'featured' => false,
            'slug' => Str::slug($name),
            'meta_tag_title' => Str::limit($name, 60, ''),
            'meta_tag_description' => $this->faker->sentence(8),
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function active(bool $active = true): static
    {
        return $this->state(fn () => ['active' => $active]);
    }

    public function featured(bool $featured = true): static
    {
        return $this->state(fn () => ['featured' => $featured]);
    }

    public function withParent(): static
    {
        return $this->state(fn () => [
            'parent_id' => ProductCategory::inRandomOrder()->value('id'),
        ]);
    }
}
