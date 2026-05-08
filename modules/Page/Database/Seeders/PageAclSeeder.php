<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PageAclSeeder extends Seeder
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
            'page-list',
            'page-view',
            'page-create',
            'page-edit',
            'page-delete',
            'page-recycle-bin-list',
            'page-recycle-bin-delete',
            'page-recycle-bin-restore',
        ];
    }
}
