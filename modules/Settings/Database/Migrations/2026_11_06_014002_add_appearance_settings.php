<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Settings\Services\AppearanceSettings;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app(AppearanceSettings::class)->initialize();
        Permission::firstOrCreate(['name' => 'appearance-settings-edit', 'guard_name' => 'user']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Cache::forget('settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('group', 'appearance')->where('key', 'palette')->delete();
        Permission::where('name', 'appearance-settings-edit')->where('guard_name', 'user')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Cache::forget('settings');
    }
};
