<?php

use Modules\Localization\Support\MissingTranslationKeys;
use Tests\TestCase;

uses(TestCase::class);

test('a dotted key that does not resolve is recorded', function () {
    $recorder = new MissingTranslationKeys;

    $recorder->record('common.nope', 'en');

    expect($recorder->keys())->toBe(['common.nope'])
        ->and($recorder->isEmpty())->toBeFalse()
        ->and($recorder->all()['common.nope'])->toBe(['locale' => 'en', 'replace' => []]);
});

test('a namespaced key that does not resolve is recorded', function () {
    $recorder = new MissingTranslationKeys;

    $recorder->record('cart::site.nope', 'bn');

    expect($recorder->keys())->toBe(['cart::site.nope']);
});

test('each missing key is recorded once', function () {
    $recorder = new MissingTranslationKeys;

    $recorder->record('common.nope', 'en');
    $recorder->record('common.nope', 'bn');

    expect($recorder->keys())->toHaveCount(1)
        ->and($recorder->all()['common.nope']['locale'])->toBe('en');
});

test('framework validation probes are never recorded', function () {
    $recorder = new MissingTranslationKeys;

    $recorder->record('validation.required', 'en');
    $recorder->record('validation.custom.email.required', 'en');
    $recorder->record('validation.attributes.email', 'en');
    $recorder->record('validation.max.string', 'en');

    expect($recorder->isEmpty())->toBeTrue();
});

test('sentences the framework translates directly are never recorded', function () {
    $recorder = new MissingTranslationKeys;

    $recorder->record('Forbidden', 'en');
    $recorder->record('Not Found', 'en');
    $recorder->record('There is no permission named `posts.view` for guard `web`.', 'en');
    $recorder->record('Save', 'en');
    $recorder->record('১২৩', 'bn');

    expect($recorder->isEmpty())->toBeTrue();
});

test('record reports whether the lookup was kept', function () {
    $recorder = new MissingTranslationKeys;

    expect($recorder->record('common.nope', 'en'))->toBeTrue()
        ->and($recorder->record('Forbidden', 'en'))->toBeFalse()
        ->and($recorder->record('validation.required', 'en'))->toBeFalse();
});

test('the recorder can be emptied', function () {
    $recorder = new MissingTranslationKeys;

    $recorder->record('common.nope', 'en');
    $recorder->flush();

    expect($recorder->isEmpty())->toBeTrue()
        ->and($recorder->keys())->toBe([]);
});

test('the container hands out the same recorder for every lookup', function () {
    expect(app(MissingTranslationKeys::class))->toBe(app(MissingTranslationKeys::class));
});

test('the translator reports keys it cannot resolve', function () {
    $recorder = app(MissingTranslationKeys::class);
    $recorder->flush();

    try {
        __('definitely.not.a.key');

        expect($recorder->keys())->toContain('definitely.not.a.key');
    } finally {
        $recorder->flush();
    }
});

test('framework and sentence lookups stay out of the report', function () {
    $recorder = app(MissingTranslationKeys::class);
    $recorder->flush();

    try {
        __('validation.this_key_does_not_exist');
        __('Forbidden');

        expect($recorder->isEmpty())->toBeTrue();
    } finally {
        $recorder->flush();
    }
});
