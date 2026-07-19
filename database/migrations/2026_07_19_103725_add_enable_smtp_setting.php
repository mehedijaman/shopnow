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
        DB::table('settings')->updateOrInsert(
            ['group' => 'mail', 'key' => 'enable_smtp'],
            [
                'value' => '0',
                'type' => 'boolean',
                'label' => 'Enable SMTP Settings',
                'description' => 'When enabled, custom SMTP settings from the database will be used. When disabled, the system fallbacks to the .env configuration.',
                'is_public' => false,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'mail')
            ->where('key', 'enable_smtp')
            ->delete();
    }
};
