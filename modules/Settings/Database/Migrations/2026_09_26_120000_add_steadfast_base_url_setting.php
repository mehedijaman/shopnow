<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')
            ->where('group', 'courier')
            ->where('sort_order', '>=', 15)
            ->update(['sort_order' => DB::raw('sort_order + 1')]);

        DB::table('settings')->updateOrInsert(
            ['group' => 'courier', 'key' => 'steadfast_base_url'],
            [
                'value' => 'https://portal.packzy.com/api/v1',
                'type' => 'text',
                'label' => 'Steadfast API Base URL',
                'description' => 'API root used for booking and tracking. portal.packzy.com is the current SteadFast host.',
                'is_public' => false,
                'sort_order' => 15,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        Cache::forget('settings');
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'courier')
            ->where('key', 'steadfast_base_url')
            ->delete();

        DB::table('settings')
            ->where('group', 'courier')
            ->where('sort_order', '>', 15)
            ->update(['sort_order' => DB::raw('sort_order - 1')]);

        Cache::forget('settings');
    }
};
