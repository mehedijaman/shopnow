<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class BlogAclSeeder extends Seeder
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
            // Main Menu
            'Blog',

            // BlogPost/PostIndex.vue
            'Blog: Post - List',
            'Blog: Post - Create',
            'Blog: Post - Edit',
            'Blog: Post - Delete',
            'Blog: Post - Recycle Bin List',
            'Blog: Post - Recycle Bin Restore',
            'Blog: Post - Recycle Bin Delete',

            // BlogCategory/CategoryIndex.vue
            'Blog: Category - List',
            'Blog: Category - Create',
            'Blog: Category - Edit',
            'Blog: Category - Delete',
            'Blog: Category - Recycle Bin List',
            'Blog: Category - Recycle Bin Restore',
            'Blog: Category - Recycle Bin Delete',

            // BlogAuthor/AuthorIndex.vue
            'Blog: Author - List',
            'Blog: Author - Create',
            'Blog: Author - Edit',
            'Blog: Author - Delete',
            'Blog: Author - Recycle Bin List',
            'Blog: Author - Recycle Bin Restore',
            'Blog: Author - Recycle Bin Delete',

            // BlogTag/TagIndex.vue
            'Blog: Tag - List',
            'Blog: Tag - Create',
            'Blog: Tag - Edit',
            'Blog: Tag - Delete',
            'Blog: Tag - Recycle Bin List',
            'Blog: Tag - Recycle Bin Restore',
            'Blog: Tag - Recycle Bin Delete',
        ];
    }
}
