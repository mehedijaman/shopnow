<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Product\Models\ProductBrand;

class ProductBrandFactory extends Factory
{
    protected $model = ProductBrand::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'parent_id' => null,
            'name' => $name,
            'description' => $this->faker->realText(200),
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
            'parent_id' => ProductBrand::inRandomOrder()->value('id'),
        ]);
    }
}
