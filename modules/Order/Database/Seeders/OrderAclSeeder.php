<?php

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class OrderAclSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'user',
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    private function getPermissions(): array
    {
        return [
            'order-menu',
            'order-list',
            'order-create',
            'order-edit',
            'order-delete',
        ];
    }
}
