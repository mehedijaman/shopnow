<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Modules\Courier\Services\FraudChecker;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    config([
        'courierhub.couriers.steadfast.api_key' => 'test-api-key',
        'courierhub.couriers.steadfast.secret_key' => 'test-secret-key',
        'courierhub.couriers.steadfast.base_url' => 'https://portal.packzy.com/api/v1',
    ]);
});

test('queries the documented fraud score endpoint with the courier API keys', function () {
    Http::fake([
        '*fraud_check/score*' => Http::response([
            'delivery_ratio' => 60,
            'cancellation_ratio' => 40,
            'volume_band' => 'medium',
            'total_reports' => 3,
            'fraud_categories' => ['cod_abuse'],
            'score' => null,
            'level' => null,
            'scoring_disabled' => true,
        ]),
        '*' => Http::response(['error' => 'unstubbed'], 500),
    ]);

    $payload = (new FraudChecker)->check('01712345678');

    Http::assertSent(fn ($request) => str_contains($request->url(), '/fraud_check/score/01712345678')
        && $request->hasHeader('Api-Key', 'test-api-key')
        && $request->hasHeader('Secret-Key', 'test-secret-key'));

    expect($payload['steadfast'])->not->toHaveKey('error')
        ->and($payload['steadfast']['total'])->toBe(20)
        ->and($payload['steadfast']['cancel'])->toBe(8)
        ->and($payload['steadfast']['total_reports'])->toBe(3)
        ->and($payload['steadfast']['fraud_categories'])->toBe(['cod_abuse'])
        ->and(FraudChecker::answeredPortals($payload))->toBe(1)
        ->and($payload['aggregate']['total_deliveries'])->toBe(20)
        ->and($payload['aggregate']['cancel_ratio'])->toBe(40.0);
});

test('interprets fractional ratios as percentages when they sum to at most one', function () {
    Http::fake([
        '*fraud_check/score*' => Http::response([
            'delivery_ratio' => 0.75,
            'cancellation_ratio' => 0.25,
            'volume_band' => 'high',
            'total_reports' => 0,
            'fraud_categories' => [],
        ]),
        '*' => Http::response(['error' => 'unstubbed'], 500),
    ]);

    $payload = (new FraudChecker)->check('01712345678');

    expect($payload['steadfast']['total'])->toBe(200)
        ->and($payload['steadfast']['cancel'])->toBe(50)
        ->and($payload['aggregate']['cancel_ratio'])->toBe(25.0);
});

test('skips the steadfast portal when the API keys are missing', function () {
    config([
        'courierhub.couriers.steadfast.api_key' => null,
        'courierhub.couriers.steadfast.secret_key' => null,
    ]);

    Http::fake([
        '*' => Http::response(['error' => 'unstubbed'], 500),
    ]);

    $payload = (new FraudChecker)->check('01712345678');

    expect($payload['steadfast'])->toHaveKey('error', 'Not configured')
        ->and(FraudChecker::answeredPortals($payload))->toBe(0)
        ->and(Http::assertNothingSent());
});
