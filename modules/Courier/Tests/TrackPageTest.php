<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
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
