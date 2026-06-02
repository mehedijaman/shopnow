<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Settings\Models\Setting;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    // Seed base settings rows needed for tests
    collect([
        ['group' => 'general', 'key' => 'site_name', 'value' => 'TestShop', 'type' => 'text', 'label' => 'Site Name', 'sort_order' => 1, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'general', 'key' => 'site_description', 'value' => null, 'type' => 'textarea', 'label' => 'Site Description', 'sort_order' => 2, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'branding', 'key' => 'site_name', 'value' => 'TestShop', 'type' => 'text', 'label' => 'Site Name', 'sort_order' => 1, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'branding', 'key' => 'logo', 'value' => null, 'type' => 'image', 'label' => 'Logo', 'sort_order' => 3, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'contact', 'key' => 'phone', 'value' => '[]', 'type' => 'repeater', 'label' => 'Phone Numbers', 'sort_order' => 1, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'contact', 'key' => 'email', 'value' => '[]', 'type' => 'repeater', 'label' => 'Email Addresses', 'sort_order' => 2, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'social', 'key' => 'facebook', 'value' => null, 'type' => 'text', 'label' => 'Facebook', 'sort_order' => 1, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'pixel', 'key' => 'enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable Meta Pixel', 'sort_order' => 1, 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'pixel', 'key' => 'meta_pixel_id', 'value' => null, 'type' => 'text', 'label' => 'Meta Pixel ID', 'sort_order' => 2, 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
    ])->each(function (array $setting): void {
        Setting::updateOrCreate(
            ['group' => $setting['group'], 'key' => $setting['key']],
            $setting,
        );
    });
});

afterEach(function () {
    Cache::forget('settings');
    Storage::disk('public')->deleteDirectory('settings');
});

// --- show() ---

test('settings general page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/settings/general');

    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Settings/SettingsForm', false)
            ->where('group', 'general')
            ->has('settings.site_name')
            ->has('groups')
    );
});

test('settings redirect route goes to general group', function () {
    $response = $this->loggedRequest->get('/admin/settings');

    $response->assertRedirect('/admin/settings/general');
});

test('settings returns 404 for unknown group', function () {
    $response = $this->loggedRequest->get('/admin/settings/unknown');

    $response->assertStatus(404);
});

test('unauthenticated user is redirected from settings', function () {
    $this->app['auth']->forgetGuards();

    $response = $this->get('/admin/settings/general');

    $response->assertRedirect();
});

// --- update() ---

test('general settings can be updated', function () {
    $response = $this->loggedRequest->post('/admin/settings/general', [
        'site_description' => 'Best online shop.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('settings', [
        'group' => 'general',
        'key' => 'site_description',
        'value' => 'Best online shop.',
    ]);
});

test('branding settings validation rejects empty site_name', function () {
    $response = $this->loggedRequest->post('/admin/settings/branding', [
        'site_name' => '',
    ]);

    $response->assertSessionHasErrors('site_name');
});

test('contact settings save multiple phone and email values', function () {
    $response = $this->loggedRequest->post('/admin/settings/contact', [
        'phone' => ['01712345678', '01812345678'],
        'email' => ['info@shop.com', 'support@shop.com'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $phone = Setting::where(['group' => 'contact', 'key' => 'phone'])->first();
    $this->assertEquals(['01712345678', '01812345678'], json_decode($phone->getRawOriginal('value'), true));
});

test('social settings can be updated', function () {
    Setting::insert([
        ['group' => 'social', 'key' => 'instagram', 'value' => null, 'type' => 'text', 'label' => 'Instagram', 'sort_order' => 3, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $response = $this->loggedRequest->post('/admin/settings/social', [
        'facebook' => 'https://facebook.com/testshop',
        'instagram' => 'https://instagram.com/testshop',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('settings', [
        'group' => 'social',
        'key' => 'facebook',
        'value' => 'https://facebook.com/testshop',
    ]);
});

test('social settings rejects invalid URL', function () {
    $response = $this->loggedRequest->post('/admin/settings/social', [
        'facebook' => 'not-a-url',
    ]);

    $response->assertSessionHasErrors('facebook');
});

test('branding settings image can be uploaded', function () {
    Storage::fake('public');

    Setting::insert([
        ['group' => 'branding', 'key' => 'site_slogan', 'value' => null, 'type' => 'text', 'label' => 'Slogan', 'sort_order' => 2, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'branding', 'key' => 'favicon', 'value' => null, 'type' => 'image', 'label' => 'Favicon', 'sort_order' => 4, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'branding', 'key' => 'dark_logo', 'value' => null, 'type' => 'image', 'label' => 'Dark Logo', 'sort_order' => 5, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $file = UploadedFile::fake()->image('logo.png', 200, 50);

    $response = $this->loggedRequest->post('/admin/settings/branding', [
        'site_name' => 'TestShop',
        'logo' => $file,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $logoSetting = Setting::where(['group' => 'branding', 'key' => 'logo'])->first();
    $this->assertNotNull($logoSetting->getRawOriginal('value'));
    Storage::disk('public')->assertExists('settings/'.$logoSetting->getRawOriginal('value'));
});

test('branding logo can be removed', function () {
    Storage::fake('public');

    $existingFile = 'existing-logo-abc.png';
    Storage::disk('public')->put("settings/{$existingFile}", 'fake-image-content');

    Setting::where(['group' => 'branding', 'key' => 'logo'])->update(['value' => $existingFile]);

    Setting::insert([
        ['group' => 'branding', 'key' => 'site_slogan', 'value' => null, 'type' => 'text', 'label' => 'Slogan', 'sort_order' => 2, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'branding', 'key' => 'favicon', 'value' => null, 'type' => 'image', 'label' => 'Favicon', 'sort_order' => 4, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ['group' => 'branding', 'key' => 'dark_logo', 'value' => null, 'type' => 'image', 'label' => 'Dark Logo', 'sort_order' => 5, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $response = $this->loggedRequest->post('/admin/settings/branding', [
        'site_name' => 'TestShop',
        'remove_previous_logo' => '1',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('settings', ['group' => 'branding', 'key' => 'logo', 'value' => null]);
    Storage::disk('public')->assertMissing("settings/{$existingFile}");
});

test('cache is cleared after settings update', function () {
    Cache::put('settings', ['general.site_name' => 'OldName'], 3600);

    $this->loggedRequest->post('/admin/settings/general', [
        'site_name' => 'NewName',
    ]);

    $this->assertFalse(Cache::has('settings'));
});

// --- Helpers ---

test('setting() helper returns value from cache', function () {
    Setting::where(['group' => 'general', 'key' => 'site_name'])->update(['value' => 'CachedShop']);

    Cache::forget('settings');

    $this->assertEquals('CachedShop', setting('general.site_name'));
});

test('setting() helper returns default when key not found', function () {
    $this->assertEquals('default_value', setting('general.nonexistent_key', 'default_value'));
});

test('setting() helper supports bare key defaulting to general group', function () {
    Setting::where(['group' => 'general', 'key' => 'site_name'])->update(['value' => 'BareKeyShop']);
    Cache::forget('settings');

    $this->assertEquals('BareKeyShop', setting('site_name'));
});

test('settings_group() helper returns all keys for a group', function () {
    Cache::forget('settings');

    $group = settings_group('general');

    $this->assertArrayHasKey('site_name', $group);
    $this->assertEquals('TestShop', $group['site_name']);
});

test('setting() model casts repeater values as array', function () {
    $setting = Setting::where(['group' => 'contact', 'key' => 'phone'])->first();

    $this->assertIsArray($setting->value);
});

test('pixel settings page can be rendered for root user', function () {
    $response = $this->loggedRequest->get('/admin/settings/pixel');

    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Settings/SettingsForm', false)
            ->where('group', 'pixel')
            ->has('settings.enabled')
            ->has('groups')
    );
});

test('pixel settings page is forbidden without pixel permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin/settings/pixel');

    $response->assertStatus(403);
});

test('pixel settings rejects invalid pixel id format', function () {
    $response = $this->loggedRequest->post('/admin/settings/pixel', [
        'enabled' => true,
        'meta_pixel_id' => 'PIXEL-ID',
        'require_consent' => true,
        'enable_non_production' => false,
        'capi_enabled' => true,
        'api_version' => 'v23.0',
    ]);

    $response->assertSessionHasErrors('meta_pixel_id');
});
