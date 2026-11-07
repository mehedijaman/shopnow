<?php

use Modules\Settings\Services\AppearanceSettings;
use Tests\TestCase;

uses(TestCase::class);

test('every preset has readable text and generated action colors', function (string $preset) {
    $appearance = app(AppearanceSettings::class);
    $palette = $appearance->defaults($preset);
    $appearance->validateContrast($palette);

    foreach ($appearance->variables($palette) as $variables) {
        foreach (['primary', 'accent'] as $brand) {
            expect($appearance->contrast($variables['--site-'.$brand], $variables['--site-on-'.$brand]))->toBeGreaterThanOrEqual(4.5)
                ->and($appearance->contrast($variables['--site-'.$brand.'-hover'], $variables['--site-on-'.$brand]))->toBeGreaterThanOrEqual(4.5);
        }
        foreach (['surface', 'background', 'primary-soft', 'surface-muted'] as $background) {
            expect($appearance->contrast($variables['--site-link'], $variables['--site-'.$background]))->toBeGreaterThanOrEqual(4.5);
        }
        expect($appearance->contrast($variables['--site-focus'], $variables['--site-surface']))->toBeGreaterThanOrEqual(3);
        foreach (['topbar-text', 'topbar-link', 'topbar-muted'] as $foreground) {
            expect($appearance->contrast($variables['--site-'.$foreground], $variables['--site-topbar']))->toBeGreaterThanOrEqual(4.5);
        }
    }
})->with(['default', 'emerald', 'violet', 'rose', 'warm']);

test('very bright and dark brand seeds keep readable labels and links', function (string $color) {
    $appearance = app(AppearanceSettings::class);
    $palette = $appearance->defaults();
    $palette['light']['primary'] = $color;
    $palette['dark']['primary'] = $color;
    foreach ($appearance->variables($palette) as $variables) {
        expect($appearance->contrast($variables['--site-primary'], $variables['--site-on-primary']))->toBeGreaterThanOrEqual(4.5)
            ->and($appearance->contrast($variables['--site-link'], $variables['--site-surface']))->toBeGreaterThanOrEqual(4.5);
    }
})->with(['#FFFFFF', '#000000', '#FFFF00', '#00FFFF', '#FF00FF']);

test('missing malformed and unsupported palettes use canonical defaults', function (mixed $stored) {
    $appearance = app(AppearanceSettings::class);
    expect($appearance->normalize($stored))->toBe($appearance->defaults());
})->with([[null], ['invalid-json'], [['unexpected' => true]], [['version' => 999]]]);
