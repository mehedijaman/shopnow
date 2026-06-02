<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['name' => 'pixel-settings-edit', 'guard_name' => 'user'],
            ['updated_at' => now(), 'created_at' => now()]
        );

        $defaults = [
            ['group' => 'pixel', 'key' => 'enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable Meta Pixel', 'description' => 'Turn Meta Pixel tracking on for storefront pages.', 'is_public' => false, 'sort_order' => 1],
            ['group' => 'pixel', 'key' => 'meta_pixel_id', 'value' => null, 'type' => 'text', 'label' => 'Meta Pixel ID', 'description' => 'Numeric Pixel ID from Meta Events Manager.', 'is_public' => false, 'sort_order' => 2],
            ['group' => 'pixel', 'key' => 'require_consent', 'value' => '1', 'type' => 'boolean', 'label' => 'Require Consent', 'description' => 'Do not fire pixel events until user grants tracking consent.', 'is_public' => false, 'sort_order' => 3],
            ['group' => 'pixel', 'key' => 'enable_non_production', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable in Non-Production', 'description' => 'Allow tracking on local/staging environments.', 'is_public' => false, 'sort_order' => 4],
            ['group' => 'pixel', 'key' => 'capi_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Enable Conversions API (CAPI)', 'description' => 'Send server-side events to Meta CAPI.', 'is_public' => false, 'sort_order' => 5],
            ['group' => 'pixel', 'key' => 'capi_access_token', 'value' => null, 'type' => 'text', 'label' => 'CAPI Access Token', 'description' => 'System user token for server-side Meta events.', 'is_public' => false, 'sort_order' => 6],
            ['group' => 'pixel', 'key' => 'api_version', 'value' => 'v23.0', 'type' => 'text', 'label' => 'Meta Graph API Version', 'description' => 'Meta Graph API version used for CAPI calls (e.g. v23.0).', 'is_public' => false, 'sort_order' => 7],
            ['group' => 'pixel', 'key' => 'test_event_code', 'value' => null, 'type' => 'text', 'label' => 'Test Event Code', 'description' => 'Optional Meta test_event_code for non-production verification.', 'is_public' => false, 'sort_order' => 8],
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
        DB::table('settings')->where('group', 'pixel')->delete();
        DB::table('permissions')
            ->where('name', 'pixel-settings-edit')
            ->where('guard_name', 'user')
            ->delete();
    }
};
