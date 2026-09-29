<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['group' => 'homepage', 'key' => 'show_featured_category_products'],
            [
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Show Featured Category Products',
                'description' => 'Show the per-category featured product sections.',
                'is_public' => false,
                'sort_order' => 4,
            ],
        );

        DB::table('settings')
            ->where('group', 'homepage')
            ->where('key', 'show_featured_categories')
            ->update(['description' => 'Show the featured categories grid with thumbnails.']);

        DB::table('settings')
            ->where('group', 'homepage')
            ->where('key', 'show_blog')
            ->update(['sort_order' => 5]);

        DB::table('settings')
            ->where('group', 'homepage')
            ->where('key', 'show_brands')
            ->update(['sort_order' => 6]);
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'homepage')
            ->where('key', 'show_featured_category_products')
            ->delete();

        DB::table('settings')
            ->where('group', 'homepage')
            ->where('key', 'show_featured_categories')
            ->update(['description' => 'Show the featured product categories section.']);

        DB::table('settings')
            ->where('group', 'homepage')
            ->where('key', 'show_blog')
            ->update(['sort_order' => 4]);

        DB::table('settings')
            ->where('group', 'homepage')
            ->where('key', 'show_brands')
            ->update(['sort_order' => 5]);
    }
};
