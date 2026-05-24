<?php

namespace Modules\Slider\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SliderAclSeeder extends Seeder
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
            'slider-list',
            'slider-view',
            'slider-create',
            'slider-edit',
            'slider-delete',
            'slider-recycle-bin-list',
            'slider-recycle-bin-delete',
            'slider-recycle-bin-restore',
        ];
    }
}
