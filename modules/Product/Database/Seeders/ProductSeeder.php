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
    //These images are available at: resources/images/blog . Just copy them to storage/app/public/blog as mentioned in the README.md

    private $productCategoriesImages = [
        '57b76f29-dd7d-4018-b172-a06e6ef4a4cf.jpg',
        'f0d5596c-d23a-4db7-97a5-9a298b924994.jpg',
        '44a928b1-5148-4940-af48-2792f1151272.jpg',
        '5febe62d-d9bd-4054-b5a1-83b796ca9d14.jpg',
        '9ab99b32-1f04-495e-b5a2-5fa789dedd18.jpg',
        '24521518-b796-445f-8e9a-59233d8d78fa.jpg',
        '0d2dc407-6b6f-4022-9565-1ecec79daf52.jpg',
        '153cfa68-8ca8-4712-a1c1-1bd969796ef9.jpg',
        '3d7da554-1a6c-44f6-9f9f-2a6947446a65.jpg',
        'cd2dc303-24c8-41b3-b09e-83205a647d8f.jpg',
        'edb880ab-4e13-4594-b6d1-fa4125a00100.jpg',
        '034cb604-d3d0-448b-92f1-6a255fb4653d.jpg',
    ];

    private $productImages = [
        'faa02464-6d3e-49b9-a45c-de303c91f3d5.jpg',
        '49814997-b619-4281-842f-8729f2ec151c.jpg',
        'e9d82a31-b7a2-46d8-9da4-48f51f4207aa.jpg',
        'e42eefc0-7f34-4645-ba21-d9c931daf08f.jpg',
        'f5e456c2-de4b-48c2-9554-54b8b8e7bfbc.jpg',
        '2a7f2576-82a1-4f28-9f38-5a9cc216ba16.jpg',
        '94ab2a3f-2300-4754-b458-2cc36cceac05.jpg',
        'e6ebdade-56ec-4d59-9da2-cf479b7d8c46.jpg',
        'f6d0c648-5d59-4b96-9c44-ebdc0ba3ee38.jpg',
        '3fe0425a-96a4-49b5-bcde-adf75caa626d.jpg',
        '500862ad-b078-4306-9348-39edf65f6d05.jpg',
        '2348a092-55d0-4b6d-b43d-3b34280dbed9.jpg',
        '57135dbe-7d31-4380-85cd-b78179ca2400.jpg',
        '75fca485-2ea3-47d0-b609-9c8bd58d3438.jpg',
        '4c798d2c-c4a6-4293-b052-98a048914215.jpg',
        'b8cac8c5-c3d2-4a65-8ebb-1b74059fe309.jpg',
        '952536c8-e74c-4089-bce4-3cc3b474a2d2.jpg',
        '5f4ecece-6275-41b9-bd64-0e6f904a5df7.jpg',
        '463c0606-1382-49b3-8ba5-3a3b91c4ff09.jpg',
        'd27f5f87-f8dd-47df-8b2e-5086a5ba66c1.jpg',
        '5a888786-090c-40f1-85e0-f86895311eeb.jpg',
        '68255060-d85e-4081-a025-931789e6aa1d.jpg',
        '4e8d6a91-e0ef-48aa-86cd-8d18699407c3.jpg',
        '548857a2-c1b5-490e-b3a2-20a28ba98e0f.jpg',
    ];

    public function run(): void
    {
        $this->setupProductImages();

        Schema::disableForeignKeyConstraints();

        info('Creating product categories...');
        ProductCategory::factory()->count(10)
            ->sequence(fn(Sequence $sequence) => ['image' => $this->productCategoriesImages[$sequence->index]])
            ->create();
        info('product categories created.');

        info('Creating Product brands...');
        ProductBrand::factory()->count(10)->create();
        info('Product Brand created.');

        info('Creating Product tags...');
        ProductTag::factory()->count(10)->create();
        info('Product Tags created.');

        info('Creating Products...');
        Product::factory()->count(20)
            ->sequence(fn(Sequence $sequence) => ['image' => $this->productImages[$sequence->index]])
            ->create();
        info('Product created.');

        Schema::enableForeignKeyConstraints();
    }

    private function setupProductImages(): void
    {
        Storage::deleteDirectory('public/product');
        info('Copying product images...');

        (new Filesystem)->ensureDirectoryExists(base_path('storage/app/public/product'));
        (new Filesystem)->copyDirectory(base_path('resources/images/blog'), base_path('storage/app/public/product'));

        info('The product images were copied.');
    }
}
