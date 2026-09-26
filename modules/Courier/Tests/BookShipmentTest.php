<?php

use CourierHub\Enums\CourierStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Modules\Courier\Jobs\BookShipment;
use Modules\Courier\Models\CourierEvent;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderShipment;
use Modules\Settings\Models\Setting;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');
    $this->actingAs($this->user);

    $this->enableSteadfast = function (): void {
        Setting::where('group', 'courier')->whereIn('key', [
            'steadfast_enabled', 'steadfast_api_key', 'steadfast_secret_key',
        ])->update(['value' => '1']);

        Setting::where('group', 'courier')->where('key', 'steadfast_api_key')->update(['value' => 'test-api-key']);
        Setting::where('group', 'courier')->where('key', 'steadfast_secret_key')->update(['value' => 'test-secret-key']);
        Cache::forget('settings');
    };

    ($this->enableSteadfast)();

    $this->order = Order::create([
        'name' => 'Test Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Pending,
        'payment_method' => 'cod',
        'requires_shipping' => true,
    ]);

    $this->shipment = OrderShipment::create([
        'order_id' => $this->order->id,
        'shopment_status' => ShipmentStatus::Pending,
    ]);

    $this->fakeCreateOrder = fn () => Http::fake([
        '*create_order*' => Http::response([
            'consignment' => [
                'tracking_code' => 'STF-999',
                'consignment_id' => '4242',
                'status' => 'pending',
            ],
        ]),
    ]);
});

afterEach(function () {
    Cache::forget('settings');
});

test('books the shipment and persists tracking details', function () {
    ($this->fakeCreateOrder)();

    BookShipment::dispatchSync($this->order->id);

    $shipment = $this->shipment->fresh();

    expect($shipment->tracking_number)->toBe('STF-999')
        ->and($shipment->carrier)->toBe('steadfast')
        ->and($shipment->consignment_id)->toBe('4242')
        ->and($shipment->courier_status)->toBe(CourierStatus::Pending->value)
        ->and($shipment->booked_at)->not->toBeNull()
        ->and($shipment->booking_error)->toBeNull();

    expect(CourierEvent::where('order_id', $this->order->id)->count())->toBe(1);
    Http::assertSent(fn ($request) => str_contains($request->url(), 'create_order'));
});

test('high fraud risk blocks booking unless forced', function () {
    ($this->fakeCreateOrder)();
    $this->order->update(['fraud_risk' => 'high']);

    BookShipment::dispatchSync($this->order->id);

    expect($this->shipment->fresh()->tracking_number)->toBeNull()
        ->and($this->shipment->fresh()->booking_error)->toContain('Blocked by fraud check');
    Http::assertNothingSent();

    BookShipment::dispatchSync($this->order->id, true);

    expect($this->shipment->fresh()->tracking_number)->toBe('STF-999');
});

test('records configuration errors without calling the courier', function () {
    Setting::where('group', 'courier')->where('key', 'steadfast_enabled')->update(['value' => '0']);
    Cache::forget('settings');

    Http::fake();

    BookShipment::dispatchSync($this->order->id);

    expect($this->shipment->fresh()->tracking_number)->toBeNull()
        ->and($this->shipment->fresh()->booking_error)->toContain('is not enabled');
    Http::assertNothingSent();
});

test('skips shipments that are already booked', function () {
    $this->shipment->update(['tracking_number' => 'ALREADY-1']);

    Http::fake();

    BookShipment::dispatchSync($this->order->id);

    expect($this->shipment->fresh()->tracking_number)->toBe('ALREADY-1');
    Http::assertNothingSent();
});

test('admin can queue a booking for an order', function () {
    Queue::fake();

    $this->post(route('order.bookShipment', $this->order->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    Queue::assertPushed(BookShipment::class, 1);
});

test('booking is rejected for high fraud orders without confirmation', function () {
    $this->order->update(['fraud_risk' => 'high']);
    Queue::fake();

    $this->post(route('order.bookShipment', $this->order->id))
        ->assertRedirect()
        ->assertSessionHas('error');

    Queue::assertNothingPushed();

    $this->post(route('order.bookShipment', $this->order->id), ['force' => true])
        ->assertSessionHas('success');

    Queue::assertPushed(BookShipment::class, 1);
});

test('refresh pulls the latest courier status onto the shipment', function () {
    $this->shipment->update([
        'tracking_number' => 'STF-999',
        'carrier' => 'steadfast',
    ]);

    Http::fake([
        '*status_by_cid*' => Http::response([
            'delivery_status' => 'delivered',
            'tracking_code' => 'STF-999',
        ]),
    ]);

    $this->post(route('order.refreshShipment', $this->order->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    $shipment = $this->shipment->fresh();

    expect($shipment->courier_status)->toBe(CourierStatus::Delivered->value)
        ->and($shipment->shopment_status)->toBe(ShipmentStatus::Delivered)
        ->and($shipment->actual_delivery)->not->toBeNull()
        ->and($this->order->fresh()->status)->toBe(OrderStatus::Delivered);
});

test('refresh requires an existing tracking number', function () {
    $this->post(route('order.refreshShipment', $this->order->id))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($this->shipment->fresh()->tracking_number)->toBeNull();
});
