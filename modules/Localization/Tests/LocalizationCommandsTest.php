<?php

use Illuminate\Support\Facades\File;
use Tests\TestCase;

uses(TestCase::class);

test('the catalogue passes its own checks', function () {
    $this->artisan('localization:check')->assertExitCode(0);
});

test('exporting writes a bundle for every surface and locale', function () {
    $this->artisan('localization:export', ['--surface' => 'all'])->assertExitCode(0);

    foreach ([
        resource_path('js/lang'),
        base_path('resources-site/js/lang'),
    ] as $directory) {
        foreach (array_keys(config('localization.supported')) as $locale) {
            $path = $directory.'/'.$locale.'.json';

            expect(File::exists($path))->toBeTrue("Missing bundle [$path]");
        }
    }
});

test('exported keys keep the exact shape used by the translator', function () {
    $this->artisan('localization:export', ['--surface' => 'all'])->assertExitCode(0);

    foreach ([resource_path('js/lang/en.json'), base_path('resources-site/js/lang/en.json')] as $path) {
        $bundle = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);

        expect($bundle)->toHaveKeys(['common.language', 'common.switch_language'])
            ->and($bundle['common.switch_language'])->toBe('Switch language');
    }
});

test('exported bundles are sorted so diffs stay readable', function () {
    $this->artisan('localization:export', ['--surface' => 'all'])->assertExitCode(0);

    $keys = array_keys(json_decode(File::get(resource_path('js/lang/en.json')), true, flags: JSON_THROW_ON_ERROR));
    $sorted = $keys;
    sort($sorted);

    expect($keys)->toBe($sorted);
});

test('an unknown export surface fails loudly', function () {
    $this->artisan('localization:export', ['--surface' => 'nope'])->assertExitCode(1);
});

test('the bangla bundle carries the same keys as english', function () {
    $this->artisan('localization:export', ['--surface' => 'all'])->assertExitCode(0);

    $english = json_decode(File::get(resource_path('js/lang/en.json')), true, flags: JSON_THROW_ON_ERROR);
    $bangla = json_decode(File::get(resource_path('js/lang/bn.json')), true, flags: JSON_THROW_ON_ERROR);

    expect(array_keys($bangla))->toBe(array_keys($english))
        ->and($bangla['common.switch_language'])->toBe('ভাষা পরিবর্তন করুন');
});
