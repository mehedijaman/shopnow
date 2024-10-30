<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ProductAclSeeder extends Seeder
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
            'product-menu',

            'product-list',
            'product-view',
            'product-create',
            'product-edit',
            'product-delete',
            'product-recycle-bin-list',
            'product-recycle-bin-delete',
            'product-recycle-bin-restore',

            'product-category-list',
            'product-category-view',
            'product-category-create',
            'product-category-edit',
            'product-category-delete',
            'product-category-recycle-bin-list',
            'product-category-recycle-bin-delete',
            'product-category-recycle-bin-restore',

            'product-brand-list',
            'product-brand-view',
            'product-brand-create',
            'product-brand-edit',
            'product-brand-delete',
            'product-brand-recycle-bin-list',
            'product-brand-recycle-bin-delete',
            'product-brand-recycle-bin-restore',

            'product-tag-list',
            'product-tag-view',
            'product-tag-create',
            'product-tag-edit',
            'product-tag-delete',
            'product-tag-recycle-bin-list',
            'product-tag-recycle-bin-delete',
            'product-tag-recycle-bin-restore',
        ];
    }
}
