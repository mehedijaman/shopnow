<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Modules\Customer\Models\Customer;
use Modules\Localization\Services\LocaleManager;
use Modules\User\Models\User;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('boots in english when nothing has chosen a locale', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertHeader('Content-Language', 'en');
    $response->assertSee('<html lang="en" dir="ltr" data-native-digits="false"', false);
});

test('honours the locale cookie', function () {
    $response = $this->withCookie('locale', 'bn')->get('/');

    $response->assertOk();
    $response->assertHeader('Content-Language', 'bn');
    $response->assertSee('<html lang="bn" dir="ltr" data-native-digits="true"', false);
});

test('ignores a cookie holding an unsupported locale', function () {
    $response = $this->withCookie('locale', 'fr')->get('/');

    $response->assertOk();
    $response->assertHeader('Content-Language', 'en');
    $response->assertSee('<html lang="en" dir="ltr" data-native-digits="false"', false);
});

test('the saved preference of a signed-in customer beats the cookie', function () {
    $customer = Customer::factory()->create(['locale' => 'en']);

    $response = $this
        ->actingAs($customer, 'customer')
        ->withCookie('locale', 'bn')
        ->get('/');

    $response->assertOk();
    $response->assertHeader('Content-Language', 'en');
});

test('shares the resolved locale and the catalogue of locales with inertia', function () {
    $response = $this->get('/admin');

    $response->assertOk();
    $response->assertSee('"locale":"en"', false);
    $response->assertSee('"locales":{', false);
});

test('an unsupported locale stored on an account is never trusted', function () {
    $user = User::factory()->create();
    $user->locale = 'fr';
    $user->save();

    $this->actingAs($user);

    $manager = app(LocaleManager::class);

    expect($manager->resolve(Request::create('/admin/users')))->toBe('en');
});

test('the account read belongs to the guard that owns the request', function () {
    $user = User::factory()->create(['locale' => 'bn']);
    $customer = Customer::factory()->create(['locale' => 'en']);

    $this->actingAs($user);
    $this->actingAs($customer, 'customer');

    $manager = app(LocaleManager::class);

    expect($manager->resolve(Request::create('/admin/users')))->toBe('bn')
        ->and($manager->resolve(Request::create('/cart')))->toBe('en');
});

test('an admin account does not leak into storefront requests', function () {
    $user = User::factory()->create(['locale' => 'bn']);

    $this->actingAs($user);

    $manager = app(LocaleManager::class);

    expect($manager->resolve(Request::create('/cart')))->toBe('en');
});
