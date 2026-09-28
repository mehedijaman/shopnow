<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Modules\User\Models\User;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('a guest can switch the interface language', function () {
    $response = $this->post('/locale', ['locale' => 'bn']);

    $response->assertRedirect(url('/'));
    $response->assertCookie('locale', 'bn');
});

test('the switcher returns to the page it was submitted from', function () {
    $response = $this->post('/locale', [
        'locale' => 'bn',
        '_redirect' => url('/cart'),
    ]);

    $response->assertRedirect(url('/cart'));
});

test('a relative redirect stays on this site', function () {
    $response = $this->post('/locale', [
        'locale' => 'bn',
        '_redirect' => '/cart',
    ]);

    $response->assertRedirect(url('/cart'));
});

test('an absolute redirect to another origin is refused', function () {
    $response = $this->post('/locale', [
        'locale' => 'bn',
        '_redirect' => 'https://evil.example/steal',
    ]);

    $response->assertRedirect(url('/'));
});

test('a protocol relative redirect is refused', function () {
    $response = $this->post('/locale', [
        'locale' => 'bn',
        '_redirect' => '//evil.example/steal',
    ]);

    $response->assertRedirect(url('/'));
});

test('an unsupported locale is rejected without touching the cookie', function () {
    $response = $this->post('/locale', ['locale' => 'fr']);

    $response->assertSessionHasErrors('locale');
    $response->assertCookieMissing('locale');
});

test('a missing locale is rejected', function () {
    $response = $this->post('/locale', []);

    $response->assertSessionHasErrors('locale');
    $response->assertCookieMissing('locale');
});

test('the choice is saved to the signed-in customer', function () {
    $customer = Customer::factory()->create();

    $response = $this
        ->actingAs($customer, 'customer')
        ->post('/locale', ['locale' => 'bn']);

    $response->assertCookie('locale', 'bn');
    expect($customer->fresh()->locale)->toBe('bn');
});

test('the choice is saved to the signed-in user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/locale', ['locale' => 'bn']);

    $response->assertCookie('locale', 'bn');
    expect($user->fresh()->locale)->toBe('bn');
});

test('the choice is saved to both accounts at once', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    $this->actingAs($user);
    $this->actingAs($customer, 'customer');

    $this->post('/locale', ['locale' => 'bn']);

    expect($user->fresh()->locale)->toBe('bn')
        ->and($customer->fresh()->locale)->toBe('bn');
});

test('the switch endpoint is rate limited', function () {
    foreach (range(1, 6) as $attempt) {
        $this->post('/locale', ['locale' => 'bn'])->assertRedirect();
    }

    $this->post('/locale', ['locale' => 'bn'])->assertStatus(429);
});
