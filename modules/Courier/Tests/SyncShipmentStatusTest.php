<?php

use CourierHub\Enums\CourierStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Courier\Models\CourierEvent;
use Modules\Courier\Services\SyncShipmentStatus;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderShipment;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->sync = new SyncShipmentStatus;

    $this->order = Order::create([
        'name' => 'Test Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Pending,
    ]);

    $this->shipment = OrderShipment::create([
        'order_id' => $this->order->id,
        'shopment_status' => ShipmentStatus::Pending,
        'carrier' => 'steadfast',
        'tracking_number' => 'STF-123',
    ]);
});

test('maps courier statuses onto shipment statuses', function (CourierStatus $courier, ShipmentStatus $expected) {
    $this->sync->apply($this->shipment, $courier);

    expect($this->shipment->fresh()->shopment_status)->toBe($expected);
})->with([
    [CourierStatus::Pending, ShipmentStatus::Pending],
    [CourierStatus::Confirmed, ShipmentStatus::Pending],
    [CourierStatus::OnHold, ShipmentStatus::Processing],
    [CourierStatus::PickedUp, ShipmentStatus::Shipped],
    [CourierStatus::InTransit, ShipmentStatus::Shipped],
    [CourierStatus::OutForDelivery, ShipmentStatus::Shipped],
    [CourierStatus::PartialDelivered, ShipmentStatus::Shipped],
    [CourierStatus::Delivered, ShipmentStatus::Delivered],
    [CourierStatus::Cancelled, ShipmentStatus::Cancelled],
    [CourierStatus::ReturnInTransit, ShipmentStatus::Cancelled],
    [CourierStatus::Returned, ShipmentStatus::Cancelled],
    [CourierStatus::Failed, ShipmentStatus::Cancelled],
]);

test('does not regress a delivered shipment or downgrade its order', function () {
    $this->shipment->update([
        'shopment_status' => ShipmentStatus::Delivered,
        'courier_status' => CourierStatus::Delivered->value,
    ]);

    $this->sync->apply($this->shipment, CourierStatus::InTransit);

    expect($this->shipment->fresh()->shopment_status)->toBe(ShipmentStatus::Delivered)
        ->and($this->shipment->courier_status)->toBe(CourierStatus::InTransit->value);
});

test('advances the order and stamps shipment timestamps', function () {
    $this->sync->apply($this->shipment, CourierStatus::InTransit);
    $this->sync->apply($this->shipment, CourierStatus::Delivered);

    $shipment = $this->shipment->fresh();

    expect($this->order->fresh()->status)->toBe(OrderStatus::Delivered)
        ->and($shipment->shopment_status)->toBe(ShipmentStatus::Delivered)
        ->and($shipment->shipment_date)->not->toBeNull()
        ->and($shipment->actual_delivery)->not->toBeNull();
});

test('writes a courier event only when the status changes', function () {
    expect($this->sync->apply($this->shipment, CourierStatus::InTransit))->toBeTrue()
        ->and($this->sync->apply($this->shipment, CourierStatus::InTransit))->toBeFalse();

    expect(CourierEvent::where('tracking_id', 'STF-123')->count())->toBe(1);
});

test('parses estimated delivery and keeps the previous date on garbage input', function () {
    $this->sync->apply($this->shipment, CourierStatus::InTransit, [], '2026-10-05');

    expect((string) $this->shipment->fresh()->estimated_delivery)->toContain('2026-10-05');

    $this->sync->apply($this->shipment, CourierStatus::OutForDelivery, [], 'not-a-date');

    expect((string) $this->shipment->fresh()->estimated_delivery)->toContain('2026-10-05');
});

test('cancelled shipments are terminal', function () {
    $this->shipment->update(['shopment_status' => ShipmentStatus::Cancelled]);

    $this->sync->apply($this->shipment, CourierStatus::Delivered);

    expect($this->shipment->fresh()->shopment_status)->toBe(ShipmentStatus::Cancelled)
        ->and($this->order->fresh()->status)->not->toBe(OrderStatus::Delivered);
});
