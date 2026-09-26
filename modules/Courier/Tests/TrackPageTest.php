<?php

use CourierHub\Enums\CourierStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Modules\Courier\Jobs\RefreshShipmentStatus;
use Modules\Courier\Models\CourierEvent;
use Modules\Customer\Models\Customer;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderShipment;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->order = Order::create([
        'name' => 'Track Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Shipped,
    ]);

    $this->shipment = OrderShipment::create([
        'order_id' => $this->order->id,
        'shopment_status' => ShipmentStatus::Shipped,
        'carrier' => 'steadfast',
        'tracking_number' => 'STF-123',
        'courier_status' => 'in_transit',
    ]);

    CourierEvent::create([
        'courier' => 'steadfast',
        'order_id' => $this->order->id,
        'tracking_id' => 'STF-123',
        'status' => 'in_transit',
        'payload' => null,
        'created_at' => now(),
    ]);
});

test('public track page renders', function () {
    $this->get('/track')
        ->assertOk()
        ->assertSee('Track Your Parcel');
});

test('track page is linked from the header, mobile menu, and footer', function () {
    $html = $this->get(route('site.index'))
        ->assertOk()
        ->getContent();

    expect(substr_count($html, 'href="'.e(route('site.track')).'"'))->toBeGreaterThanOrEqual(3);
});

test('shows shipment details when the tracking number and phone match', function () {
    $this->get('/track/result?tracking=STF-123&phone=01712345678')
        ->assertOk()
        ->assertSee('Shipment History')
        ->assertSee('in transit');
});

test('hides shipment details when the phone does not match', function () {
    $this->get('/track/result?tracking=STF-123&phone=01811111111')
        ->assertOk()
        ->assertSee('Shipment not found')
        ->assertDontSee('Shipment History');
});

test('validates the phone format on tracking lookups', function () {
    $this->get('/track/result?tracking=STF-123&phone=01712')
        ->assertRedirect()
        ->assertSessionHasErrors('phone');
});

test('tracking lookups are rate limited', function () {
    foreach (range(1, 10) as $ignored) {
        $this->get('/track/result?tracking=STF-123&phone=01712345678');
    }

    $this->get('/track/result?tracking=STF-123&phone=01712345678')
        ->assertStatus(429);
});

test('customers see the tracking block on their orders page', function () {
    $customer = Customer::factory()->create();

    $order = Order::create([
        'name' => 'Account Buyer',
        'phone' => '01712345678',
        'customer_id' => $customer->id,
        'status' => OrderStatus::Shipped,
    ]);

    OrderShipment::create([
        'order_id' => $order->id,
        'shopment_status' => ShipmentStatus::Shipped,
        'carrier' => 'steadfast',
        'tracking_number' => 'STF-456',
    ]);

    $this->actingAs($customer, 'customer');

    $this->get('/account/orders')
        ->assertOk()
        ->assertSee('STF-456')
        ->assertSee('Full history');
});

test('shows shipment details when looked up by order number', function () {
    $this->get('/track/result?tracking='.$this->order->id.'&phone=01712345678')
        ->assertOk()
        ->assertSee('Shipment History')
        ->assertSee('in transit');
});

test('hides details when an order number lookup has the wrong phone', function () {
    $this->get('/track/result?tracking='.$this->order->id.'&phone=01811111111')
        ->assertOk()
        ->assertSee('Shipment not found')
        ->assertDontSee('Shipment History');
});

test('shows the awaiting shipment state before the courier booking', function () {
    $order = Order::create([
        'name' => 'Prebook Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Pending,
    ]);

    OrderShipment::create([
        'order_id' => $order->id,
        'shopment_status' => ShipmentStatus::Pending,
    ]);

    $this->get('/track/result?tracking='.$order->id.'&phone=01712345678')
        ->assertOk()
        ->assertSee("hasn't been shipped yet")
        ->assertDontSee('Shipment History');
});

test('shows digital orders as having no parcel to track', function () {
    $order = Order::create([
        'name' => 'Digital Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Pending,
        'requires_shipping' => false,
    ]);

    $this->get('/track/result?tracking='.$order->id.'&phone=01712345678')
        ->assertOk()
        ->assertSee("doesn't include a parcel")
        ->assertDontSee('Shipment History');
});

test('requests a live status refresh for a stale tracked shipment', function () {
    Queue::fake();

    $this->get('/track/result?tracking=STF-123&phone=01712345678')->assertOk();

    Queue::assertPushed(
        RefreshShipmentStatus::class,
        fn (RefreshShipmentStatus $job) => $job->shipmentId === $this->shipment->id
    );
});

test('does not request a live refresh when the status is fresh', function () {
    OrderShipment::where('id', $this->shipment->id)->update(['last_synced_at' => now()]);
    Queue::fake();

    $this->get('/track/result?tracking=STF-123&phone=01712345678')->assertOk();

    Queue::assertNotPushed(RefreshShipmentStatus::class);
});

test('does not request a live refresh for delivered shipments', function () {
    OrderShipment::where('id', $this->shipment->id)->update([
        'courier_status' => CourierStatus::Delivered->value,
    ]);
    Queue::fake();

    $this->get('/track/result?tracking=STF-123&phone=01712345678')->assertOk();

    Queue::assertNotPushed(RefreshShipmentStatus::class);
});

test('does not request a live refresh while a refresh is already locked', function () {
    Cache::put('courier:track-sync:'.$this->shipment->id, 1, 600);
    Queue::fake();

    $this->get('/track/result?tracking=STF-123&phone=01712345678')->assertOk();

    Queue::assertNotPushed(RefreshShipmentStatus::class);
});

test('my orders links to tracking by order number before the courier booking', function () {
    $customer = Customer::factory()->create();

    $order = Order::create([
        'name' => 'Prebook Account Buyer',
        'phone' => '01712345678',
        'customer_id' => $customer->id,
        'status' => OrderStatus::Pending,
    ]);

    OrderShipment::create([
        'order_id' => $order->id,
        'shopment_status' => ShipmentStatus::Pending,
    ]);

    $this->actingAs($customer, 'customer');

    $this->get('/account/orders')
        ->assertOk()
        ->assertSee('Track order')
        ->assertSee(route('site.track', ['tracking' => $order->id]), false);
});

test('the order confirmation page links to tracking', function () {
    $this->get(route('site.order.confirm', $this->order->id))
        ->assertOk()
        ->assertSee(route('site.track', ['tracking' => $this->order->id]), false);
});

test('the track form page is indexable while results are not', function () {
    $this->get('/track')
        ->assertOk()
        ->assertSee('content="index, follow"', false);

    $this->get('/track/result?tracking=STF-123&phone=01712345678')
        ->assertOk()
        ->assertSee('content="noindex, follow"', false);

    $this->get('/track/result?tracking=STF-123&phone=01811111111')
        ->assertOk()
        ->assertSee('content="noindex, follow"', false);
});

test('the sitemap lists the track page', function () {
    $this->get('/sitemap-static.xml')
        ->assertOk()
        ->assertSee('/track', false);
});
