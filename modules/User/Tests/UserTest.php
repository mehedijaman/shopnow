<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\User\Models\User;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->loggedRequest = $this->actingAs($this->user);
});

test('user list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/user');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('User/UserIndex')
            ->has(
                'users.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->user->id)
                    ->where('name', $this->user->name)
                    ->where('email', $this->user->email)
                    ->where('created_at', $this->user->created_at->format('d/m/Y H:i\h'))
            )
    );
});

test('user can be created', function () {
    $response = $this->loggedRequest->post('/admin/user', [
        'name' => 'New Name',
        'email' => 'new@email.com',
        'password' => 'password',
    ]);

    $users = User::all();

    $response->assertRedirect('/admin/user');
    $this->assertCount(2, $users);
    $this->assertEquals('New Name', $users->last()->name);
});

test('user edit can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/user/'.$this->user->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('User/UserForm')
            ->has(
                'user',
                fn (Assert $page) => $page
                    ->where('id', $this->user->id)
                    ->where('name', $this->user->name)
                    ->where('email', $this->user->email)
            )
    );
});

test('user can be updated', function () {
    $response = $this->loggedRequest->put('/admin/user/'.$this->user->id, [
        'name' => 'New Name',
        'email' => 'new@email.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/admin/user');

    $redirectResponse = $this->loggedRequest->get('/admin/user');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('User/UserIndex')
            ->has(
                'users.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->user->id)
                    ->where('name', 'New Name')
                    ->where('email', 'new@email.com')
                    ->where('created_at', $this->user->created_at->format('d/m/Y H:i\h'))
            )
    );
});

test('user can be soft deleted', function () {
    $response = $this->loggedRequest->delete('/admin/user/'.$this->user->id);

    $response->assertRedirect('/admin/user');

    $this->assertCount(0, User::all());
    $this->assertCount(1, User::withTrashed()->get());
});

test('user recycle bin can be rendered', function () {
    $deletedUser = User::factory()->create();
    $deletedUser->delete();

    $response = $this->loggedRequest->get('/admin/user/recycle-bin');

    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('User/UserRecycleBin')
            ->has(
                'users.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $deletedUser->id)
                    ->where('name', $deletedUser->name)
                    ->where('email', $deletedUser->email)
                    ->where('created_at', $deletedUser->created_at->format('d/m/Y H:i\h'))
            )
    );
});

test('user can be restored from recycle bin', function () {
    $deletedUser = User::factory()->create();
    $deletedUser->delete();

    $response = $this->loggedRequest->get('/admin/user/recycle-bin/'.$deletedUser->id.'/restore');

    $response->assertRedirect('/admin/user/recycle-bin');
    $this->assertCount(2, User::all());
});

test('user can be force deleted', function () {
    $deletedUser = User::factory()->create();
    $deletedUser->delete();

    $response = $this->loggedRequest->delete('/admin/user/recycle-bin/'.$deletedUser->id.'/destroy');

    $response->assertRedirect('/admin/user/recycle-bin');
    $this->assertCount(1, User::withTrashed()->get());
});

test('user recycle bin can be emptied', function () {
    $deletedUser1 = User::factory()->create();
    $deletedUser1->delete();
    $deletedUser2 = User::factory()->create();
    $deletedUser2->delete();

    $response = $this->loggedRequest->delete('/admin/user/recycle-bin/empty');

    $response->assertRedirect('/admin/user/recycle-bin');
    $this->assertCount(1, User::withTrashed()->get());
});

test('all users in recycle bin can be restored', function () {
    $deletedUser1 = User::factory()->create();
    $deletedUser1->delete();
    $deletedUser2 = User::factory()->create();
    $deletedUser2->delete();

    $response = $this->loggedRequest->get('/admin/user/recycle-bin/restore-all');

    $response->assertRedirect('/admin/user/recycle-bin');
    $this->assertCount(3, User::all());
});
