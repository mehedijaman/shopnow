<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Customer\Models\Customer;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->customer = Customer::create([
        'name' => 'Test Customer',
        'email' => 'test@example.com',
        'phone' => '12345678901',
        'password' => bcrypt('password'),
    ])->fresh();
});

test('customer list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/customer');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerIndex')
            ->has(
                'customers.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->customer->id)
                    ->where('name', $this->customer->name)
                    ->where('email', $this->customer->email)
                    ->where('active', (bool) $this->customer->active)
                    ->etc()
            )
    );
});

test('customer create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/customer/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerForm')
    );
});

test('customer can be created', function () {
    $response = $this->loggedRequest->post('/admin/customer', [
        'name' => 'Customer Name',
        'phone' => '12345678901',
        'email' => 'customer@test.com',
        'password' => 'password',
        'confirm_password' => 'password',
        'active' => true,
    ]);

    $customers = Customer::all();

    $response->assertRedirect('/admin/customer');
    $this->assertCount(2, $customers);
    $this->assertEquals('Customer Name', $customers->last()->name);
});

test('customer edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/customer/'.$this->customer->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerForm')
            ->has(
                'customer',
                fn (Assert $page) => $page
                    ->where('id', $this->customer->id)
                    ->where('name', $this->customer->name)
                    ->where('email', $this->customer->email)
                    ->etc()
            )
    );
});

test('customer can be updated', function () {
    $response = $this->loggedRequest->put('/admin/customer/'.$this->customer->id, [
        'name' => 'Updated Name',
        'phone' => '12345678901',
        'email' => 'updated@test.com',
        'active' => true,
    ]);

    $response->assertRedirect('/admin/customer');

    $redirectResponse = $this->loggedRequest->get('/admin/customer');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerIndex')
            ->has(
                'customers.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->customer->id)
                    ->where('name', 'Updated Name')
                    ->where('email', 'updated@test.com')
                    ->where('active', true)
                    ->etc()
            )
    );
});

test('customer can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/customer/'.$this->customer->id);

    $response->assertRedirect('/admin/customer');

    $this->assertCount(0, Customer::all());
});

test('customer recycle bin can be rendered', function () {
    $this->loggedRequest->delete('/admin/customer/'.$this->customer->id);

    $response = $this->loggedRequest->get('/admin/customer/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Customer/CustomerRecycleBin')
            ->has(
                'customers.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->customer->id)
                    ->where('name', $this->customer->name)
                    ->where('email', $this->customer->email)
                    ->where('active', (bool) $this->customer->active)
                    ->etc()
            )
    );
});

test('customer can be restored', function () {
    $this->loggedRequest->delete('/admin/customer/'.$this->customer->id);

    $response = $this->loggedRequest->get('/admin/customer/recycle-bin/'.$this->customer->id.'/restore');

    $response->assertRedirect('/admin/customer/recycle-bin');

    $this->assertCount(1, Customer::all());
});
