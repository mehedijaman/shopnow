<?php

use CourierHub\Enums\CourierStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Modules\Courier\Jobs\RefreshShipmentStatus;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderShipment;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Setting::where('group', 'courier')->whereIn('key', [
        'steadfast_enabled', 'steadfast_api_key', 'steadfast_secret_key',
    ])->update(['value' => '1']);
    Setting::where('group', 'courier')->where('key', 'steadfast_api_key')->update(['value' => 'test-api-key']);
    Setting::where('group', 'courier')->where('key', 'steadfast_secret_key')->update(['value' => 'test-secret-key']);
    Cache::forget('settings');

    $this->order = Order::create([
        'name' => 'Refresh Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Shipped,
    ]);

    $this->shipment = OrderShipment::create([
        'order_id' => $this->order->id,
        'shopment_status' => ShipmentStatus::Shipped,
        'carrier' => 'steadfast',
        'tracking_number' => 'STF-9000',
        'consignment_id' => '9000',
        'courier_status' => 'in_transit',
    ]);
});

afterEach(function () {
    Cache::forget('settings');
});

test('refreshes a stale shipment from the courier api', function () {
    Http::fake([
        '*status_by_cid*' => Http::response([
            'delivery_status' => 'delivered',
            'tracking_code' => 'STF-9000',
        ]),
    ]);

    RefreshShipmentStatus::dispatchSync($this->shipment->id);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'status_by_cid/9000'));

    $shipment = $this->shipment->fresh();

    expect($shipment->courier_status)->toBe(CourierStatus::Delivered->value)
        ->and($shipment->last_synced_at)->not->toBeNull();
});

test('swallows courier auth failures without retrying the request', function () {
    Http::fake([
        '*' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    expect(fn () => RefreshShipmentStatus::dispatchSync($this->shipment->id))
        ->not->toThrow(Throwable::class);

    Http::assertSentCount(1);

    expect($this->shipment->fresh()->last_synced_at)->toBeNull();
});

test('does not call the api when the courier is disabled', function () {
    Setting::where('group', 'courier')->where('key', 'steadfast_enabled')->update(['value' => '0']);
    Cache::forget('settings');

    Http::fake();

    RefreshShipmentStatus::dispatchSync($this->shipment->id);

    Http::assertNothingSent();
});

test('isRefreshable only for stale, booked, non-terminal shipments', function () {
    $shipment = $this->shipment;

    expect(RefreshShipmentStatus::isRefreshable($shipment))->toBeTrue();

    $shipment->last_synced_at = now();
    expect(RefreshShipmentStatus::isRefreshable($shipment))->toBeFalse();

    $shipment->last_synced_at = now()->subMinutes(11);
    expect(RefreshShipmentStatus::isRefreshable($shipment))->toBeTrue();

    $shipment->courier_status = CourierStatus::Delivered->value;
    expect(RefreshShipmentStatus::isRefreshable($shipment))->toBeFalse();

    $shipment->courier_status = 'in_transit';
    $shipment->shopment_status = ShipmentStatus::Cancelled;
    expect(RefreshShipmentStatus::isRefreshable($shipment))->toBeFalse();

    $shipment->shopment_status = ShipmentStatus::Shipped;
    $shipment->tracking_number = null;
    expect(RefreshShipmentStatus::isRefreshable($shipment))->toBeFalse();

    $shipment->tracking_number = 'STF-9000';
    $shipment->carrier = null;
    expect(RefreshShipmentStatus::isRefreshable($shipment))->toBeFalse();
});
