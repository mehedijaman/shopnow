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
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->permission = Permission::create(['name' => 'first']);
});

test('permission list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/acl-permission');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('AclPermission/PermissionIndex')
            ->has(
                'permissions.data',
                Permission::count()
            )
    );
});

test('permission in list has correct fields', function () {
    $response = $this->loggedRequest->get('/admin/acl-permission');
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('AclPermission/PermissionIndex')
            ->where('permissions.data.0.guard', null)
    );
});

test('permission can be created', function () {
    $response = $this->loggedRequest->post('/admin/acl-permission', [
        'name' => 'Permission Name',
    ]);

    $this->assertTrue(Permission::where('name', 'Permission Name')->exists());
    $response->assertRedirect('/admin/acl-permission');
});

test('permission edit can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/acl-permission/'.$this->permission->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('AclPermission/PermissionForm')
            ->has(
                'permission',
                fn (Assert $page) => $page
                    ->where('id', $this->permission->id)
                    ->where('name', $this->permission->name)
                    ->where('guard_name', $this->permission->guard_name)
                    ->where('created_at', $this->permission->created_at->toISOString())
                    ->where('updated_at', $this->permission->updated_at->toISOString())
            )
    );
});

test('permission can be updated', function () {
    $response = $this->loggedRequest->put('/admin/acl-permission/'.$this->permission->id, [
        'name' => 'z Permission Name',
    ]);

    $response->assertRedirect('/admin/acl-permission');
});

test('updated permission appears correctly in the list', function () {
    $this->loggedRequest->put('/admin/acl-permission/'.$this->permission->id, [
        'name' => 'z Permission Name',
    ]);

    $redirectResponse = $this->loggedRequest->get('/admin/acl-permission');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('AclPermission/PermissionIndex')
            ->has(
                'permissions.data',
                Permission::count()
            )
    );

    $this->assertDatabaseHas('permissions', [
        'id' => $this->permission->id,
        'name' => 'z Permission Name',
    ]);
});

test('permission can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/acl-permission/'.$this->permission->id);

    $response->assertRedirect('/admin/acl-permission');

    $this->assertFalse(Permission::where('id', $this->permission->id)->exists());
});
