<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Settings\Models\Setting;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'courier-settings-edit', 'guard_name' => 'user']);

    $this->root = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->root->assignRole('root');
});

afterEach(function () {
    Cache::forget('settings');
});

test('guests are redirected from the courier settings page', function () {
    $this->get('/admin/settings/courier')->assertRedirect();
});

test('users without the courier permission are forbidden', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/settings/courier')->assertForbidden();
});

test('root sees the courier settings group', function () {
    $this->actingAs($this->root)
        ->get('/admin/settings/courier')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Settings/SettingsForm')
            ->where('group', 'courier')
            ->has('settings.steadfast_api_key')
            ->has('settings.fraud_enabled')
            ->has('settings.fraud_cancel_ratio_threshold'));
});

test('courier settings update persists values', function () {
    $this->actingAs($this->root)->post('/admin/settings/courier', [
        'default_courier' => 'pathao',
        'default_weight_kg' => '2',
        'fraud_enabled' => true,
        'fraud_min_deliveries' => '8',
        'fraud_cancel_ratio_threshold' => '35',
    ])->assertRedirect();

    Cache::forget('settings');

    $settings = Setting::where('group', 'courier')->pluck('value', 'key');

    expect($settings['default_courier'])->toBe('pathao')
        ->and($settings['default_weight_kg'])->toBe('2')
        ->and((string) $settings['fraud_enabled'])->toBe('1')
        ->and($settings['fraud_min_deliveries'])->toBe('8')
        ->and($settings['fraud_cancel_ratio_threshold'])->toBe('35');
});
