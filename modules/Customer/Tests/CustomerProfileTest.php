<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('profile page is guest protected', function () {
    $response = $this->get('/account/profile');
    $response->assertRedirect(route('customerAuth.loginForm'));
});

test('profile page can be rendered for customer', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')->get('/account/profile');

    $response->assertStatus(200);
});

test('customer can update profile info', function () {
    $customer = Customer::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'phone' => '01711111111',
    ]);

    $response = $this->actingAs($customer, 'customer')->put('/account/profile', [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'phone' => '01722222222',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
        'phone' => '01722222222',
    ]);
});

test('customer can update password', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')->put('/account/profile', [
        'name' => $customer->name,
        'email' => $customer->email,
        'phone' => $customer->phone,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertRedirect();

    // Attempt authentication with the new password
    $authResponse = $this->post('/login', [
        'email' => $customer->email,
        'password' => 'new-password-123',
    ]);

    $this->assertAuthenticatedAs($customer, 'customer');
});
