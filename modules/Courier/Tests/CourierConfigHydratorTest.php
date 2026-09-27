<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Courier\Services\CourierConfigHydrator;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->setSetting = function (string $key, mixed $value): void {
        Setting::where('group', 'courier')->where('key', $key)->update(['value' => $value]);
        Cache::forget('settings');
    };

    $this->hydrator = new CourierConfigHydrator;
});

afterEach(function () {
    Cache::forget('settings');
});

test('hydrates enabled courier credentials from settings', function () {
    ($this->setSetting)('steadfast_enabled', '1');
    ($this->setSetting)('steadfast_api_key', 'api-key-1');
    ($this->setSetting)('steadfast_secret_key', 'secret-key-1');
    ($this->setSetting)('steadfast_webhook_secret', 'whsec-1');
    ($this->setSetting)('steadfast_base_url', 'https://portal.packzy.com/api/v1');

    $this->hydrator->hydrate();

    expect(config('courierhub.couriers.steadfast.enabled'))->toBeTrue()
        ->and(config('courierhub.couriers.steadfast.api_key'))->toBe('api-key-1')
        ->and(config('courierhub.couriers.steadfast.secret_key'))->toBe('secret-key-1')
        ->and(config('courierhub.couriers.steadfast.base_url'))->toBe('https://portal.packzy.com/api/v1')
        ->and(config('courierhub.webhook.secrets.steadfast'))->toBe('whsec-1')
        ->and(config('courierhub.couriers.pathao.enabled'))->toBeFalse();
});

test('applies the default courier from settings', function () {
    ($this->setSetting)('default_courier', 'pathao');

    $this->hydrator->hydrate();

    expect(config('courierhub.default'))->toBe('pathao');
});

test('hydrates fraud source settings', function () {
    ($this->setSetting)('fraud_steadfast_enabled', '0');
    ($this->setSetting)('fraud_bdcourier_enabled', '1');
    ($this->setSetting)('bdcourier_api_key', 'bd-key-1');
    ($this->setSetting)('bdcourier_endpoint', 'https://api.bdcourier.com');

    $this->hydrator->hydrate();

    expect(config('fraud.steadfast.enabled'))->toBeFalse()
        ->and(config('fraud.bdcourier.enabled'))->toBeTrue()
        ->and(config('fraud.bdcourier.api_key'))->toBe('bd-key-1')
        ->and(config('fraud.bdcourier.base_url'))->toBe('https://api.bdcourier.com');
});

test('skips hydration when the courier settings group is missing', function () {
    Setting::where('group', 'courier')->delete();
    Cache::forget('settings');

    config(['courierhub.couriers.steadfast.enabled' => 'sentinel']);

    $this->hydrator->hydrate();

    expect(config('courierhub.couriers.steadfast.enabled'))->toBe('sentinel');
});
