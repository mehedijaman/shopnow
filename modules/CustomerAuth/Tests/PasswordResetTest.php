<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\AdminAuth\Notifications\ResetPassword as AdminAuthResetPassword;
use Modules\User\Models\User;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('reset password link screen can be rendered', function () {
    $response = $this->get('/customer-auth/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/customer-auth/send-reset-link-email', ['email' => $user->email]);

    Notification::assertSentTo($user, AdminAuthResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/customer-auth/send-reset-link-email', ['email' => $user->email]);

    Notification::assertSentTo($user, AdminAuthResetPassword::class, function ($notification) {
        $response = $this->get('/customer-auth/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/customer-auth/send-reset-link-email', ['email' => $user->email]);

    Notification::assertSentTo($user, AdminAuthResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/customer-auth/reset-password/', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors();

        return true;
    });
});
