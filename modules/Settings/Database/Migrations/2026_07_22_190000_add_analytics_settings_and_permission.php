<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['name' => 'analytics-settings-edit', 'guard_name' => 'user'],
            ['updated_at' => now(), 'created_at' => now()]
        );

        $defaults = [
            ['group' => 'analytics', 'key' => 'enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable Google Analytics', 'description' => 'Turn Google Analytics (GA4) tracking on for storefront pages.', 'is_public' => false, 'sort_order' => 1],
            ['group' => 'analytics', 'key' => 'ga_measurement_id', 'value' => null, 'type' => 'text', 'label' => 'GA Measurement ID', 'description' => 'Measurement ID from GA4 (e.g. G-XXXXXXXXXX).', 'is_public' => false, 'sort_order' => 2],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'label' => $setting['label'],
                    'description' => $setting['description'],
                    'is_public' => $setting['is_public'],
                    'sort_order' => $setting['sort_order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'analytics')->delete();
        DB::table('permissions')
            ->where('name', 'analytics-settings-edit')
            ->where('guard_name', 'user')
            ->delete();
    }
};
