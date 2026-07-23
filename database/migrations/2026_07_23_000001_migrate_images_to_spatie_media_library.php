<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'products' => 'Modules\Product\Models\Product',
            'product_categories' => 'Modules\Product\Models\ProductCategory',
            'product_brands' => 'Modules\Product\Models\ProductBrand',
            'blog_posts' => 'Modules\Blog\Models\Post',
            'blog_categories' => 'Modules\Blog\Models\Category',
            'blog_authors' => 'Modules\Blog\Models\Author',
            'sliders' => 'Modules\Slider\Models\Slider',
            'pages' => 'Modules\Page\Models\Page',
        ];

        foreach ($tables as $tableName => $modelClass) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'image')) {
                continue;
            }

            // Copy existing image path references into Spatie Media table if column exists
            $records = DB::table($tableName)->whereNotNull('image')->where('image', '!=', '')->get();

            foreach ($records as $record) {
                $imagePath = $record->image;
                if (! empty($imagePath)) {
                    $disk = 'public';
                    $fileName = basename($imagePath);

                    DB::table('media')->insert([
                        'model_type' => $modelClass,
                        'model_id' => $record->id,
                        'uuid' => (string) Str::uuid(),
                        'collection_name' => 'image',
                        'name' => pathinfo($fileName, PATHINFO_FILENAME),
                        'file_name' => $fileName,
                        'mime_type' => 'image/'.(pathinfo($fileName, PATHINFO_EXTENSION) ?: 'jpeg'),
                        'disk' => $disk,
                        'conversions_disk' => $disk,
                        'size' => 0,
                        'manipulations' => '[]',
                        'custom_properties' => '[]',
                        'generated_conversions' => '[]',
                        'responsive_images' => '[]',
                        'order_column' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Drop image column safely
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'products',
            'product_categories',
            'product_brands',
            'blog_posts',
            'blog_categories',
            'blog_authors',
            'sliders',
            'pages',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'image')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('image')->nullable();
                });
            }
        }
    }
};
