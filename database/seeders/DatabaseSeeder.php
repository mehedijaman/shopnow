<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\Database\Seeders\AclModelHasRolesSeeder;
use Modules\Acl\Database\Seeders\AclPermissionSeeder;
use Modules\Acl\Database\Seeders\AclRoleSeeder;
use Modules\Blog\Database\Seeders\BlogAclSeeder;
use Modules\Blog\Database\Seeders\BlogSeeder;
use Modules\Page\Database\Seeders\PageSeeder;
use Modules\Product\Database\Seeders\ProductAclSeeder;
use Modules\Product\Database\Seeders\ProductSeeder;
use Modules\Settings\Database\Seeders\SettingSeeder;
use Modules\User\Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AclRoleSeeder::class,
            AclPermissionSeeder::class,
            AclModelHasRolesSeeder::class,

            BlogAclSeeder::class,
            BlogSeeder::class,
            PageSeeder::class,

            ProductAclSeeder::class,
            ProductSeeder::class,

            SettingSeeder::class,
        ]);
    }
}
