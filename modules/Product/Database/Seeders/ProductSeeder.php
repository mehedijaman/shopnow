<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductAttribute;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductTag;
use Modules\Product\Services\GenerateProductVariations;

use function Laravel\Prompts\info;

class ProductSeeder extends Seeder
{
    private array $productImages = [
        '1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '6.jpg',
        '7.jpg', '8.jpg', '9.jpg', '10.jpg', '11.jpg', '12.jpg',
    ];

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->setupProductImages();

        // Categories
        info('Creating product categories...');
        ProductCategory::factory()->count(10)->create();
        $categories = ProductCategory::pluck('id')->toArray();

        // Brands
        info('Creating product brands...');
        ProductBrand::factory()->count(10)->create();
        $brands = ProductBrand::pluck('id')->toArray();

        // Tags
        info('Creating product tags...');
        ProductTag::factory()->count(10)->create();
        $tags = ProductTag::pluck('id')->toArray();

        // Simple products (majority)
        info('Creating simple products...');
        Product::factory()
            ->count(20)
            ->simple()
            ->withSalePrice()
            ->create([
                'category_id' => fn () => $categories[array_rand($categories)],
                'brand_id' => fn () => $brands[array_rand($brands)],
            ]);

        // Create global attributes for variable products
        info('Creating product attributes...');
        $colorAttr = ProductAttribute::create(['name' => 'Color', 'input_type' => 'color']);
        $red = $colorAttr->values()->create(['value' => 'Red', 'slug' => 'red', 'swatch' => '#e53e3e', 'sort_order' => 0]);
        $blue = $colorAttr->values()->create(['value' => 'Blue', 'slug' => 'blue', 'swatch' => '#3182ce', 'sort_order' => 1]);
        $green = $colorAttr->values()->create(['value' => 'Green', 'slug' => 'green', 'swatch' => '#38a169', 'sort_order' => 2]);
        $black = $colorAttr->values()->create(['value' => 'Black', 'slug' => 'black', 'swatch' => '#1a202c', 'sort_order' => 3]);

        $sizeAttr = ProductAttribute::create(['name' => 'Size', 'input_type' => 'select']);
        $s = $sizeAttr->values()->create(['value' => 'S', 'slug' => 's', 'sort_order' => 0]);
        $m = $sizeAttr->values()->create(['value' => 'M', 'slug' => 'm', 'sort_order' => 1]);
        $l = $sizeAttr->values()->create(['value' => 'L', 'slug' => 'l', 'sort_order' => 2]);
        $xl = $sizeAttr->values()->create(['value' => 'XL', 'slug' => 'xl', 'sort_order' => 3]);

        // Variable products with proper variations
        info('Creating variable products...');
        $generate = app(GenerateProductVariations::class);
        $variableProducts = Product::factory()
            ->count(5)
            ->variable()
            ->create([
                'category_id' => fn () => $categories[array_rand($categories)],
                'brand_id' => fn () => $brands[array_rand($brands)],
            ]);

        foreach ($variableProducts as $vp) {
            // Link a subset of color values and all sizes
            $colorValues = fake()->randomElements([$red->id, $blue->id, $green->id, $black->id], fake()->numberBetween(2, 4));
            $sizeValues = [$s->id, $m->id, $l->id, $xl->id];
            $vp->attributeValues()->sync(array_merge($colorValues, $sizeValues));

            // Generate variations
            $generate->run($vp, [
                $colorAttr->id => $colorValues,
                $sizeAttr->id => $sizeValues,
            ]);

            // Set realistic prices and stock on each variation
            $basePrice = fake()->numberBetween(500, 3000);
            foreach ($vp->variations()->get() as $variation) {
                $variation->update([
                    'price' => $basePrice + fake()->numberBetween(-100, 500),
                    'sale_price' => fake()->boolean(30) ? $basePrice - fake()->numberBetween(50, 200) : null,
                    'quantity' => fake()->numberBetween(5, 80),
                    'sku' => strtoupper(Str::random(3).'-'.$variation->id),
                ]);
            }
        }

        // Bundle products
        info('Creating bundle products...');
        Product::factory()
            ->count(3)
            ->bundle()
            ->create([
                'category_id' => fn () => $categories[array_rand($categories)],
                'brand_id' => fn () => $brands[array_rand($brands)],
                'price' => 999,
            ]);

        // Virtual + downloadable products
        info('Creating digital products...');
        Product::factory()
            ->count(2)
            ->downloadable()
            ->withSalePrice()
            ->create([
                'category_id' => fn () => $categories[array_rand($categories)],
                'brand_id' => fn () => $brands[array_rand($brands)],
                'summary' => 'Digital product — instant download after purchase.',
                'description' => 'This is a digital product. You will receive download access immediately after completing your order.',
            ]);

        // Attach tags randomly
        $products = Product::all();
        foreach ($products as $product) {
            $product->tags()->attach(
                fake()->randomElements($tags, fake()->numberBetween(1, 3))
            );
        }

        Schema::enableForeignKeyConstraints();

        info('Done: 30 products created (20 simple, 5 variable, 3 bundle, 2 digital).');
    }

    private function setupProductImages(): void
    {
        $source = base_path('resources/images/product');
        $dest = base_path('storage/app/public/product');

        if (! is_dir($source)) {
            info('No product images found in resources/images/product — skipping image copy.');

            return;
        }

        Storage::deleteDirectory('public/product');
        info('Copying product images...');

        (new Filesystem)->ensureDirectoryExists($dest);
        (new Filesystem)->copyDirectory($source, $dest);

        info('Product images copied.');
    }

    private function getRandomProductImage(): string
    {
        return $this->productImages[array_rand($this->productImages)];
    }
}
