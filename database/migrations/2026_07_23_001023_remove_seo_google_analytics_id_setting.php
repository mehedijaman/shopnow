<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')
            ->where('group', 'seo')
            ->where('key', 'google_analytics_id')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->insertOrIgnore([
            'group' => 'seo',
            'key' => 'google_analytics_id',
            'value' => null,
            'type' => 'text',
            'label' => 'Google Analytics ID (G-XXXXXX)',
            'is_public' => true,
            'sort_order' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
