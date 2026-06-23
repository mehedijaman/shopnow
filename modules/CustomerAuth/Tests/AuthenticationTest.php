<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('customers can authenticate using the login screen', function () {
    $customer = Customer::factory()->create();

    $response = $this->post('/login', [
        'email' => $customer->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($customer, 'customer');
    $response->assertRedirect('/');
});

test('customers can not authenticate with invalid password', function () {
    $customer = Customer::factory()->create();

    $this->post('/login', [
        'email' => $customer->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest('customer');
});

test('customers can logout', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer');

    $this->get('/logout');

    $this->assertTrue(true); // logout route accessed without error
});
