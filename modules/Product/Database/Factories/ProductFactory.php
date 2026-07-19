<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Product\Enums\ProductType;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $price = $this->faker->numberBetween(100, 10000);

        return [
            'category_id' => null,
            'brand_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
            'price' => $price,
            'sale_price' => null,
            'quantity' => $this->faker->numberBetween(0, 200),
            'unit' => $this->faker->randomElement(['pcs', 'kg', 'pack', 'box', 'set', 'pair']),
            'min_order' => 1,
            'active' => true,
            'featured' => false,
            'type' => ProductType::Simple,
            'is_virtual' => false,
            'is_downloadable' => false,
            'image' => $this->faker->imageUrl(640, 480, 'product'),
            'summary' => $this->faker->sentence(12),
            'description' => $this->faker->paragraphs(3, true),
            'meta_tag_title' => Str::limit($name, 60, ''),
            'meta_tag_description' => $this->faker->sentence(10),
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function simple(): static
    {
        return $this->state(fn () => ['type' => ProductType::Simple]);
    }

    public function variable(): static
    {
        return $this->state(fn () => [
            'type' => ProductType::Variable,
            'price' => 0,
            'sale_price' => null,
            'quantity' => 0,
        ]);
    }

    public function bundle(): static
    {
        return $this->state(fn () => [
            'type' => ProductType::Bundle,
            'quantity' => 0,
        ]);
    }

    public function active(bool $active = true): static
    {
        return $this->state(fn () => ['active' => $active]);
    }

    public function featured(bool $featured = true): static
    {
        return $this->state(fn () => ['featured' => $featured]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['quantity' => 0]);
    }

    public function virtual(): static
    {
        return $this->state(fn () => ['is_virtual' => true]);
    }

    public function downloadable(): static
    {
        return $this->state(fn () => [
            'is_virtual' => true,
            'is_downloadable' => true,
        ]);
    }

    public function withSalePrice(?int $salePrice = null): static
    {
        return $this->state(function (array $attributes) use ($salePrice) {
            $price = $attributes['price'];

            return [
                'sale_price' => $salePrice ?? (int) ($price * $this->faker->randomFloat(2, 0.5, 0.9)),
            ];
        });
    }

    public function withCategory(): static
    {
        return $this->afterMaking(function (Product $product) {
            if (! $product->category_id) {
                $product->category_id = ProductCategory::factory()->create()->id;
            }
        });
    }

    public function withBrand(): static
    {
        return $this->afterMaking(function (Product $product) {
            if (! $product->brand_id) {
                $product->brand_id = ProductBrand::factory()->create()->id;
            }
        });
    }
}
