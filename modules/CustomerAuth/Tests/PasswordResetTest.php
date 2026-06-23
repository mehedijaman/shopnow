<?php

use Modules\CustomerAuth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\Customer\Models\Customer;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $customer = Customer::factory()->create();

    $this->post('/send-reset-link-email', ['email' => $customer->email]);

    Notification::assertSentTo($customer, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $customer = Customer::factory()->create();

    $this->post('/send-reset-link-email', ['email' => $customer->email]);

    Notification::assertSentTo($customer, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $customer = Customer::factory()->create();

    $this->post('/send-reset-link-email', ['email' => $customer->email]);

    Notification::assertSentTo($customer, ResetPassword::class, function ($notification) use ($customer) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $customer->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasNoErrors();

        return true;
    });
});
