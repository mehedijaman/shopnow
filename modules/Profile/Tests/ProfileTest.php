<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'password' => bcrypt('current-password'),
    ]);
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);
});

test('profile page loads', function () {
    $response = $this->loggedRequest->get('/admin/profile');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Profile/ProfilePage')
    );
});

test('profile can be updated', function () {
    $response = $this->loggedRequest->post('/admin/profile', [
        'name' => 'Updated Name',
    ]);

    $response->assertRedirect('/admin/profile');
    $this->assertEquals('Updated Name', $this->user->fresh()->name);
});

test('password can be updated', function () {
    $response = $this->loggedRequest->put('/admin/profile/password', [
        'current_password' => 'current-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertRedirect('/admin/profile');
});

test('email can be updated', function () {
    $response = $this->loggedRequest->put('/admin/profile/email', [
        'email' => 'new@email.com',
        'current_password' => 'current-password',
    ]);

    $response->assertRedirect('/admin/profile');
    $this->assertEquals('new@email.com', $this->user->fresh()->email);
});
