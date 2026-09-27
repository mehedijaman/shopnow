<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Modules\Courier\Services\FraudChecker;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    config([
        'fraud.steadfast.enabled' => true,
        'fraud.bdcourier.enabled' => false,
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
        ->and(FraudChecker::answeredProviders($payload))->toBe(1)
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

test('skips the steadfast source when the API keys are missing', function () {
    config([
        'courierhub.couriers.steadfast.api_key' => null,
        'courierhub.couriers.steadfast.secret_key' => null,
    ]);

    Http::fake([
        '*' => Http::response(['error' => 'unstubbed'], 500),
    ]);

    $payload = (new FraudChecker)->check('01712345678');

    expect($payload['steadfast'])->toHaveKey('error', 'Not configured')
        ->and(FraudChecker::answeredProviders($payload))->toBe(0)
        ->and(Http::assertNothingSent());
});

test('skips the steadfast source when it is disabled', function () {
    config(['fraud.steadfast.enabled' => false]);

    Http::fake([
        '*' => Http::response(['error' => 'unstubbed'], 500),
    ]);

    $payload = (new FraudChecker)->check('01712345678');

    expect($payload['steadfast'])->toHaveKey('error', 'Disabled')
        ->and(Http::assertNothingSent());
});

test('queries the bdcourier endpoint with a bearer key and maps the summary', function () {
    config([
        'fraud.bdcourier.enabled' => true,
        'fraud.bdcourier.api_key' => 'bd-key',
        'fraud.bdcourier.base_url' => 'https://api.bdcourier.com',
    ]);

    Http::fake([
        '*fraud_check/score*' => Http::response([
            'delivery_ratio' => 60,
            'cancellation_ratio' => 40,
            'volume_band' => 'medium',
            'total_reports' => 0,
            'fraud_categories' => [],
        ]),
        '*courier-check' => Http::response([
            'status' => 'success',
            'data' => [
                'pathao' => [
                    'name' => 'Pathao',
                    'logo' => 'https://api.bdcourier.com/c-logo/pathao-logo.png',
                    'total_parcel' => 150,
                    'success_parcel' => 120,
                    'cancelled_parcel' => 30,
                    'success_ratio' => 80,
                ],
                'steadfast' => [
                    'name' => 'SteadFast',
                    'logo' => 'https://api.bdcourier.com/c-logo/steadfast-logo.png',
                    'total_parcel' => 200,
                    'success_parcel' => 175,
                    'cancelled_parcel' => 25,
                    'success_ratio' => 87.5,
                ],
                'summary' => [
                    'total_parcel' => 620,
                    'success_parcel' => 530,
                    'cancelled_parcel' => 90,
                    'success_ratio' => 85.48,
                ],
                'reports' => [
                    [
                        'id' => 'abc123',
                        'name' => 'John Doe',
                        'details' => 'Fraud reported by merchant',
                        'created_at' => '2024-01-01T00:00:00.000000Z',
                        'courierLogo' => 'https://api.bdcourier.com/c-logo/steadfast-logo.png',
                        'courierName' => 'SteadFast',
                    ],
                ],
            ],
        ]),
        '*' => Http::response(['error' => 'unstubbed'], 500),
    ]);

    $payload = (new FraudChecker)->check('01712345678');

    Http::assertSent(fn ($request) => str_contains($request->url(), '/courier-check')
        && $request->hasHeader('Authorization', 'Bearer bd-key')
        && $request['phone'] === '01712345678');

    expect($payload['bdcourier'])->not->toHaveKey('error')
        ->and($payload['bdcourier']['total'])->toBe(620)
        ->and($payload['bdcourier']['success'])->toBe(530)
        ->and($payload['bdcourier']['cancel'])->toBe(90)
        ->and($payload['bdcourier']['success_ratio'])->toBe(85.48)
        ->and($payload['bdcourier']['couriers']['pathao']['total_parcel'])->toBe(150)
        ->and($payload['bdcourier']['couriers']['steadfast']['success_ratio'])->toBe(87.5)
        ->and($payload['bdcourier']['reports'])->toHaveCount(1)
        ->and($payload['bdcourier']['reports'][0]['courierName'])->toBe('SteadFast')
        ->and(FraudChecker::answeredProviders($payload))->toBe(2)
        ->and($payload['aggregate']['total_deliveries'])->toBe(640)
        ->and($payload['aggregate']['total_cancel'])->toBe(98);
});

test('skips the bdcourier source without a key even when enabled', function () {
    config([
        'fraud.steadfast.enabled' => false,
        'fraud.bdcourier.enabled' => true,
        'fraud.bdcourier.api_key' => null,
    ]);

    Http::fake([
        '*' => Http::response(['error' => 'unstubbed'], 500),
    ]);

    $payload = (new FraudChecker)->check('01712345678');

    expect($payload['bdcourier'])->toHaveKey('error', 'Not configured')
        ->and(Http::assertNothingSent());
});

test('captures a bdcourier error response without failing the payload', function () {
    config([
        'fraud.bdcourier.enabled' => true,
        'fraud.bdcourier.api_key' => 'bd-key',
        'fraud.bdcourier.base_url' => 'https://api.bdcourier.com',
    ]);

    Http::fake([
        '*fraud_check/score*' => Http::response([
            'delivery_ratio' => 100,
            'cancellation_ratio' => 0,
            'volume_band' => 'low',
            'total_reports' => 0,
            'fraud_categories' => [],
        ]),
        '*courier-check' => Http::response(['message' => 'Invalid key'], 401),
        '*' => Http::response(['error' => 'unstubbed'], 500),
    ]);

    $payload = (new FraudChecker)->check('01712345678');

    expect($payload['bdcourier'])->toHaveKey('error')
        ->and($payload['bdcourier']['status'])->toBe(401)
        ->and(FraudChecker::answeredProviders($payload))->toBe(1)
        ->and($payload['aggregate']['total_deliveries'])->toBe(5);
});
