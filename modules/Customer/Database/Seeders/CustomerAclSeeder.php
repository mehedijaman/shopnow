<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CustomerAclSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'user',
            ]);
        }
    }

    private function getPermissions(): array
    {
        return [
            'customer-menu',

            'customer-list',
            'customer-view',
            'customer-create',
            'customer-edit',
            'customer-delete',
            'customer-recycle-bin-list',
            'customer-recycle-bin-delete',
            'customer-recycle-bin-restore',
        ];
    }
}
