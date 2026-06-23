<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->user = User::factory()->create();
    $this->loggedRequest = $this->actingAs($this->user);

    $this->role = Role::create(['name' => 'root']);
    $this->user->assignRole($this->role);

    $this->permission = Permission::create(['name' => 'first']);
    $this->permission2 = Permission::create(['name' => 'second']);

    $this->role->syncPermissions([$this->permission->id]);
});

test('role permissions can be rendered', function () {
    $permissionCount = Permission::count();

    $response = $this->loggedRequest->get('/admin/acl-role-permission/'.$this->role->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('AclRolePermission/RolePermissionForm')
            ->has(
                'role',
                fn (Assert $page) => $page
                    ->where('id', $this->role->id)
                    ->etc()
            )
            ->has(
                'role.permissions',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->permission->id)
                    ->where('name', $this->permission->name)
            )
            ->has(
                'permissions',
                $permissionCount
            )
    );
});

test('role permissions can be updated', function () {
    $response = $this->loggedRequest->put('/admin/acl-role-permission/'.$this->role->id, [
        'rolePermissions' => [$this->permission2->id],
    ]);

    $response->assertRedirect('/admin/acl-role');

    $role = Role::with(['permissions' => function ($q) {
        $q->get(['id', 'name']);
    }])->findOrFail($this->role->id);

    $this->assertCount(1, $role->permissions);
    $this->assertEquals($this->permission2->id, $role->permissions->first()->id);
});
