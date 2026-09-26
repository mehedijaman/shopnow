<?php

use CourierHub\Enums\CourierStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
        'name' => 'Poll Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Processing,
    ]);

    $this->shipment = OrderShipment::create([
        'order_id' => $this->order->id,
        'shopment_status' => ShipmentStatus::Pending,
        'carrier' => 'steadfast',
        'tracking_number' => 'STF-4242',
        'consignment_id' => '4242',
    ]);
});

afterEach(function () {
    Cache::forget('settings');
});

test('poll looks up the consignment id and syncs the outcome', function () {
    Http::fake([
        '*status_by_cid*' => Http::response([
            'delivery_status' => 'in_transit',
            'tracking_code' => 'STF-4242',
        ]),
    ]);

    $this->artisan('courier:poll')->assertSuccessful();

    Http::assertSent(fn ($request) => str_contains($request->url(), 'status_by_cid/4242'));

    $shipment = $this->shipment->fresh();

    expect($shipment->courier_status)->toBe(CourierStatus::InTransit->value)
        ->and($shipment->shopment_status)->toBe(ShipmentStatus::Shipped)
        ->and($shipment->last_synced_at)->not->toBeNull();
});

test('poll aborts the run after an authentication failure', function () {
    Order::create([
        'name' => 'Second Poll Buyer',
        'phone' => '01812345678',
        'status' => OrderStatus::Processing,
    ]);

    OrderShipment::create([
        'order_id' => Order::where('name', 'Second Poll Buyer')->first()->id,
        'shopment_status' => ShipmentStatus::Pending,
        'carrier' => 'steadfast',
        'tracking_number' => 'STF-9999',
        'consignment_id' => '9999',
    ]);

    Http::fake([
        '*status_by_cid*' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    $this->artisan('courier:poll')->assertSuccessful();

    Http::assertSentCount(1);
    expect($this->shipment->fresh()->courier_status)->toBeNull();
});
