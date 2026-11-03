<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Settings\Database\Seeders\SmsSettingsSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['sms-settings-edit', 'sms-test-send', 'sms-activity-view'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'user']);
        }
        (new SmsSettingsSeeder)->run();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::where('guard_name', 'user')->whereIn('name', ['sms-settings-edit', 'sms-test-send', 'sms-activity-view'])->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
