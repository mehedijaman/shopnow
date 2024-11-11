<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductTag;

use function Laravel\Prompts\info;

class ProductSeeder extends Seeder
{
    private $productImages = [
        '1.jpg',
        '2.jpg',
        '3.jpg',
        '4.jpg',
        '5.jpg',
        '6.jpg',
        '7.jpg',
        '8.jpg',
        '9.jpg',
        '10.jpg',
        '11.jpg',
        '12.jpg',
    ];

    public function run(): void
    {
        $this->setupProductImages();

        Schema::disableForeignKeyConstraints();

        info('Creating product categories...');
        ProductCategory::factory()->count(10)
            // ->sequence(fn(Sequence $sequence) => ['image' => $this->productImages[$sequence->index]])
            ->create();
        info('product categories created.');

        info('Creating Product brands...');
        ProductBrand::factory()->count(10)->create();
        info('Product Brand created.');

        info('Creating Product tags...');
        ProductTag::factory()->count(10)->create();
        info('Product Tags created.');

        info('Creating Products...');
        Product::factory()->count(30)
            ->create(['image' => fn () => $this->getRandomProductImage()]);
        info('Product created.');

        Schema::enableForeignKeyConstraints();
    }

    private function setupProductImages(): void
    {
        Storage::deleteDirectory('public/product');
        info('Copying product images...');

        (new Filesystem)->ensureDirectoryExists(base_path('storage/app/public/product'));
        (new Filesystem)->copyDirectory(base_path('resources/images/product'), base_path('storage/app/public/product'));

        info('The product images were copied.');
    }

    private function getRandomProductImage(): string
    {
        return $this->productImages[array_rand($this->productImages)];
    }
}
