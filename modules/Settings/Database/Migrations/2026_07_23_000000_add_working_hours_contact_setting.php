<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['group' => 'contact', 'key' => 'working_hours'],
            [
                'value' => '[]',
                'type' => 'repeater',
                'label' => 'Working Hours',
                'description' => 'Business working hours displayed on the contact page.',
                'is_public' => true,
                'sort_order' => 5,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'contact')->where('key', 'working_hours')->delete();
    }
};
