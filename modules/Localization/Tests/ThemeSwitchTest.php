<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

afterEach(function () {
    Cache::forget('settings');
});

test('the storefront resolves the theme before first paint', function () {
    $content = $this->get('/')->assertOk()->getContent();

    // The pre-paint script reads the shared key and falls back to the OS preference.
    $this->assertStringContainsString("'modular-theme'", $content);
    $this->assertStringContainsString('prefers-color-scheme: dark', $content);
    $this->assertStringContainsString('window.ShopNowTheme', $content);

    // Every switch on the page is driven by one delegated listener.
    $this->assertStringContainsString("closest('[data-theme-toggle]')", $content);
});

test('every header surface renders a theme switcher', function () {
    $content = $this->get('/')->assertOk()->getContent();

    // Mobile header, desktop nav and the off-canvas drawer.
    $this->assertGreaterThanOrEqual(3, substr_count($content, 'data-theme-toggle') - 1);
    $this->assertGreaterThanOrEqual(3, substr_count($content, 'ri-moon-line'));
});

test('the toggle label follows the active locale', function () {
    $this->get('/')->assertOk()->assertSee('Toggle theme');

    $this->withCookie('locale', 'bn')
        ->get('/')
        ->assertOk()
        ->assertSee('থিম পরিবর্তন করুন');
});

test('a dark logo is rendered as the dark-mode counterpart of the light logo', function () {
    Setting::updateOrCreate(
        ['group' => 'branding', 'key' => 'logo'],
        ['value' => 'light-logo.png', 'type' => 'image', 'label' => 'Logo', 'is_public' => true, 'sort_order' => 1],
    );
    Setting::updateOrCreate(
        ['group' => 'branding', 'key' => 'dark_logo'],
        ['value' => 'dark-logo.png', 'type' => 'image', 'label' => 'Dark Logo', 'is_public' => true, 'sort_order' => 5],
    );
    Cache::forget('settings');

    $content = $this->get('/')->assertOk()->getContent();

    $this->assertStringContainsString('storage/settings/light-logo.png', $content);
    $this->assertStringContainsString('storage/settings/dark-logo.png', $content);
    $this->assertStringContainsString('dark:hidden', $content);
    $this->assertStringContainsString('dark:block', $content);
});

test('the light logo stands alone when no dark logo is configured', function () {
    Setting::updateOrCreate(
        ['group' => 'branding', 'key' => 'logo'],
        ['value' => 'light-logo.png', 'type' => 'image', 'label' => 'Logo', 'is_public' => true, 'sort_order' => 1],
    );
    Setting::updateOrCreate(
        ['group' => 'branding', 'key' => 'dark_logo'],
        ['value' => null, 'type' => 'image', 'label' => 'Dark Logo', 'is_public' => true, 'sort_order' => 5],
    );
    Cache::forget('settings');

    $content = $this->get('/')->assertOk()->getContent();

    $this->assertStringContainsString('storage/settings/light-logo.png', $content);
    $this->assertStringNotContainsString('storage/settings/dark-logo.png', $content);
});
