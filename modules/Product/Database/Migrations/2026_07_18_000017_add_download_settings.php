<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['group' => 'downloads', 'key' => 'default_expiry_days', 'value' => '30', 'type' => 'text', 'label' => 'Default Expiry Days', 'description' => 'Number of days before download permissions expire. Set to 0 for no expiry.', 'is_public' => false, 'sort_order' => 1],
            ['group' => 'downloads', 'key' => 'default_limit', 'value' => '5', 'type' => 'text', 'label' => 'Default Download Limit', 'description' => 'Maximum number of times a customer can download each file. Set to 0 for unlimited.', 'is_public' => false, 'sort_order' => 2],
        ];

        foreach ($settings as $setting) {
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
        DB::table('settings')->where('group', 'downloads')->delete();
    }
};
