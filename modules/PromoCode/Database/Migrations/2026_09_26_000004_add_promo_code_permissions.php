<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $permissions = [
        'promo-code-menu',
        'promo-code-list',
        'promo-code-create',
        'promo-code-edit',
        'promo-code-delete',
        'promo-code-recycle-bin-list',
        'promo-code-recycle-bin-restore',
        'promo-code-recycle-bin-delete',
    ];

    public function up(): void
    {
        foreach ($this->permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'user'],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('permissions')
            ->where('guard_name', 'user')
            ->whereIn('name', $this->permissions)
            ->delete();
    }
};
