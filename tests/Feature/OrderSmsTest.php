<?php

use CourierHub\Enums\CourierStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Modules\Courier\Services\SyncShipmentStatus;
use Modules\Order\Database\Factories\OrderFactory;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Services\CreateOrderService;
use Modules\Order\Services\OrderSms;
use Modules\Order\Services\RecordOrderPayment;
use Modules\Product\Models\Product;
use Modules\Settings\Models\SmsMessage;
use Modules\Settings\Services\SmsSettings;

beforeEach(function () {
    Queue::fake();
    app(SmsSettings::class)->save(array_merge(app(SmsSettings::class)->defaults(), [
        'enabled' => true, 'order_placed' => true, 'order_updated' => true, 'provider' => 'bulksmsbd',
        'credentials' => ['api_key' => 'test-key', 'senderid' => 'SHOP'],
    ]));
    $this->order = OrderFactory::new()->create(['phone' => '01712345678', 'status' => OrderStatus::Pending, 'locale' => 'en', 'total' => 500]);
});

test('order placement intent is idempotent and snapshots rendered content', function () {
    app(OrderSms::class)->placed($this->order);
    app(OrderSms::class)->placed($this->order);
    expect(SmsMessage::count())->toBe(1);
    expect(SmsMessage::first()->body)->toContain('500.00')->and(SmsMessage::first()->phone)->toBe('01712345678');
});

test('only actual selected status transitions generate sms updates', function () {
    $this->order->update(['notes' => 'Internal edit']);
    $this->order->update(['status' => OrderStatus::Pending]);
    expect(SmsMessage::count())->toBe(0);
    $this->order->update(['status' => OrderStatus::Processing]);
    $this->order->update(['status' => OrderStatus::Processing]);
    expect(SmsMessage::count())->toBe(1);
    $this->order->update(['status' => OrderStatus::Pending]);
    expect(SmsMessage::count())->toBe(1);
    $this->order->update(['status' => OrderStatus::Processing]);
    expect(SmsMessage::count())->toBe(2);
});

test('order and sms intent roll back together', function () {
    try {
        DB::transaction(function () {
            $this->order->update(['status' => OrderStatus::Shipped]);
            throw new RuntimeException('rollback');
        });
    } catch (RuntimeException) {
    }
    expect($this->order->fresh()->status)->toBe(OrderStatus::Pending)->and(SmsMessage::count())->toBe(0);
});

test('courier status synchronization sends one matching order update', function () {
    $shipment = $this->order->orderShipments()->create(['shopment_status' => 'pending']);
    app(SyncShipmentStatus::class)->apply($shipment, CourierStatus::Delivered);
    app(SyncShipmentStatus::class)->apply($shipment->fresh(), CourierStatus::Delivered);
    expect($this->order->fresh()->status)->toBe(OrderStatus::Delivered)
        ->and(SmsMessage::where('purpose', 'order_updated')->count())->toBe(1);
});

test('digital payment completion generates a completed order sms', function () {
    $this->order->update(['requires_shipping' => false]);
    app(RecordOrderPayment::class)->run($this->order, []);
    expect($this->order->fresh()->status)->toBe(OrderStatus::Completed)
        ->and(SmsMessage::where('order_status', 'completed')->count())->toBe(1);
});

test('storefront checkout records one placement sms with database defaults', function () {
    $product = Product::factory()->create(['price' => 120, 'quantity' => 10]);
    $response = $this->post('/site-order-store', [
        'name' => 'SMS Buyer', 'phone' => '01712345678',
        'items' => [['item' => ['id' => $product->id, 'price' => 120], 'quantity' => 2]],
    ])->assertCreated();
    $message = SmsMessage::where('order_id', $response->json('order_id'))->sole();
    expect($message->purpose)->toBe('order_placed')->and($message->body)->toContain('240.00');
});

test('admin order creation sends only final placement and suppresses draft status messages', function () {
    $product = Product::factory()->create(['price' => 120, 'quantity' => 10]);
    $order = app(CreateOrderService::class)->run([
        'name' => 'SMS Admin Buyer', 'phone' => '01712345678', 'status' => 'processing',
        'items' => [['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 120]],
    ]);
    expect(SmsMessage::where('order_id', $order->id)->count())->toBe(1)
        ->and(SmsMessage::where('order_id', $order->id)->first()->purpose)->toBe('order_placed');
});
