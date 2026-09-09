<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['group' => 'analytics', 'key' => 'gtm_container_id'],
            [
                'value' => null,
                'type' => 'text',
                'label' => 'GTM Container ID',
                'description' => 'Google Tag Manager container ID (e.g. GTM-XXXXXXX). Leave empty to disable GTM.',
                'is_public' => false,
                'sort_order' => 3,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'analytics')
            ->where('key', 'gtm_container_id')
            ->delete();
    }
};
