<?php

namespace Modules\PromoCode\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PromoCodeAclSeeder extends Seeder
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

    private function getPermissions(): array
    {
        return [
            'promo-code-menu',
            'promo-code-list',
            'promo-code-create',
            'promo-code-edit',
            'promo-code-delete',
            'promo-code-recycle-bin-list',
            'promo-code-recycle-bin-restore',
            'promo-code-recycle-bin-delete',
        ];
    }
}
