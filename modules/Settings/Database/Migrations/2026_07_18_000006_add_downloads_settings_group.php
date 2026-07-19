<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['group' => 'downloads', 'key' => 'default_expiry_days'],
            [
                'value' => '30',
                'type' => 'text',
                'label' => 'Default Download Expiry (Days)',
                'description' => 'Number of days before download links expire. Set 0 for no expiry.',
                'is_public' => false,
                'sort_order' => 1,
            ],
        );

        DB::table('settings')->updateOrInsert(
            ['group' => 'downloads', 'key' => 'default_limit'],
            [
                'value' => '5',
                'type' => 'text',
                'label' => 'Default Download Limit',
                'description' => 'Maximum number of times a file can be downloaded. Set 0 for unlimited.',
                'is_public' => false,
                'sort_order' => 2,
            ],
        );
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'downloads')
            ->whereIn('key', ['default_expiry_days', 'default_limit'])
            ->delete();
    }
};
