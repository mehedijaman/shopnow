<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Customer\Models\Customer;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderPayment;
use Modules\Order\Services\RecordOrderPayment;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Support\Events\OrderPaymentConfirmed;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $category = ProductCategory::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    $this->physicalProduct = Product::create([
        'category_id' => $category->id,
        'name' => 'Physical Product',
        'slug' => 'physical-product',
        'price' => 99.99,
        'quantity' => '10',
        'active' => true,
        'is_virtual' => false,
        'is_downloadable' => false,
    ]);

    $this->virtualProduct = Product::create([
        'category_id' => $category->id,
        'name' => 'Virtual Product',
        'slug' => 'virtual-product',
        'price' => 49.99,
        'quantity' => '10',
        'active' => true,
        'is_virtual' => true,
        'is_downloadable' => true,
    ]);
});

test('order can be placed via site', function () {
    $response = $this->post('/site-order-store', [
        'name' => 'John Doe',
        'phone' => '01712345678',
        'items' => [
            [
                'item' => ['id' => $this->physicalProduct->id, 'price' => 99.99],
                'quantity' => 2,
            ],
        ],
    ]);

    $response->assertStatus(201);

    $response->assertJson([
        'message' => 'Order placed successfully.',
        'order_id' => $response->json('order_id'),
    ]);

    $this->assertDatabaseHas('orders', [
        'name' => 'John Doe',
        'phone' => '01712345678',
    ]);
});

test('order confirmation page loads', function () {
    $order = Order::create([
        'name' => 'John Doe',
        'phone' => '01712345678',
    ]);

    $response = $this->get('/order-confirm/'.$order->id);

    $response->assertStatus(200);
});

test('virtual-only order sets requires_shipping false and creates no shipment', function () {
    $response = $this->post('/site-order-store', [
        'name' => 'Jane Doe',
        'phone' => '01812345678',
        'items' => [
            [
                'item' => ['id' => $this->virtualProduct->id, 'price' => 49.99],
                'quantity' => 1,
            ],
        ],
    ]);

    $response->assertStatus(201);

    $order = Order::where('name', 'Jane Doe')->first();

    expect($order)->not->toBeNull();
    expect($order->requires_shipping)->toBe(0);
    expect($order->orderShipments()->count())->toBe(0);
});

test('mixed cart sets requires_shipping true and creates shipment', function () {
    $response = $this->post('/site-order-store', [
        'name' => 'Jack Doe',
        'phone' => '01912345678',
        'items' => [
            [
                'item' => ['id' => $this->virtualProduct->id, 'price' => 49.99],
                'quantity' => 1,
            ],
            [
                'item' => ['id' => $this->physicalProduct->id, 'price' => 99.99],
                'quantity' => 2,
            ],
        ],
    ]);

    $response->assertStatus(201);

    $order = Order::where('name', 'Jack Doe')->first();

    expect($order)->not->toBeNull();
    expect($order->requires_shipping)->toBe(1);
    expect($order->orderShipments()->count())->toBe(1);
    expect($order->orderShipments()->first()->shopment_status)->toBe('pending');
});

test('physical-only order sets requires_shipping true and creates shipment', function () {
    $response = $this->post('/site-order-store', [
        'name' => 'Jill Doe',
        'phone' => '01912345679',
        'items' => [
            [
                'item' => ['id' => $this->physicalProduct->id, 'price' => 99.99],
                'quantity' => 1,
            ],
        ],
    ]);

    $response->assertStatus(201);

    $order = Order::where('name', 'Jill Doe')->first();

    expect($order)->not->toBeNull();
    expect($order->requires_shipping)->toBe(1);
    expect($order->orderShipments()->count())->toBe(1);
});

test('RecordOrderPayment creates OrderPayment row and fires event on success', function () {
    Event::fake();

    $order = Order::create([
        'name' => 'Payment Test',
        'phone' => '01700000000',
        'total' => 99.99,
        'requires_shipping' => false,
    ]);

    $order->orderProducts()->create([
        'product_id' => $this->virtualProduct->id,
        'quantity' => 1,
        'unit_price' => 49.99,
        'discount' => 0,
        'total_price' => 49.99,
    ]);

    $service = app(RecordOrderPayment::class);
    $payment = $service->run($order, [
        'payment_status' => 'success',
        'payment_method' => 'cod',
        'amount_paid' => 99.99,
    ]);

    expect($payment)->toBeInstanceOf(OrderPayment::class);
    expect($payment->payment_status)->toBe('success');
    expect((float) $payment->amount_paid)->toBe(99.99);

    $this->assertDatabaseHas('order_payments', [
        'order_id' => $order->id,
        'payment_status' => 'success',
        'payment_method' => 'cod',
    ]);

    Event::assertDispatched(OrderPaymentConfirmed::class, function ($event) use ($order) {
        return $event->orderId === $order->id
            && count($event->items) === 1
            && $event->items[0]['is_downloadable'] === true;
    });
});

test('RecordOrderPayment does not fire event on failed payment', function () {
    Event::fake();

    $order = Order::create([
        'name' => 'Fail Test',
        'phone' => '01700000001',
        'total' => 49.99,
        'requires_shipping' => true,
    ]);

    $service = app(RecordOrderPayment::class);
    $payment = $service->run($order, [
        'payment_status' => 'failed',
        'payment_method' => 'cod',
    ]);

    expect($payment->payment_status)->toBe('failed');

    Event::assertNotDispatched(OrderPaymentConfirmed::class);
});

test('RecordOrderPayment auto-completes order when requires_shipping is false', function () {
    $order = Order::create([
        'name' => 'Auto Complete',
        'phone' => '01700000002',
        'total' => 49.99,
        'status' => 'pending',
        'requires_shipping' => false,
    ]);

    $service = app(RecordOrderPayment::class);
    $service->run($order, [
        'payment_status' => 'success',
        'payment_method' => 'cod',
    ]);

    $order->refresh();

    expect($order->status)->toBe('completed');
});

test('RecordOrderPayment does not auto-complete order when requires_shipping is true', function () {
    $order = Order::create([
        'name' => 'No Auto Complete',
        'phone' => '01700000003',
        'total' => 99.99,
        'status' => 'pending',
        'requires_shipping' => true,
    ]);

    $service = app(RecordOrderPayment::class);
    $service->run($order, [
        'payment_status' => 'success',
        'payment_method' => 'cod',
    ]);

    $order->refresh();

    expect($order->status)->toBe('pending');
});

test('OrderPaymentConfirmed event payload has correct structure', function () {
    $customer = Customer::factory()->create();

    $order = Order::create([
        'name' => 'Event Payload',
        'phone' => '01700000004',
        'customer_id' => $customer->id,
        'email' => 'test@example.com',
        'total' => 49.99,
    ]);

    $order->orderProducts()->create([
        'product_id' => $this->virtualProduct->id,
        'quantity' => 2,
        'unit_price' => 24.995,
        'discount' => 0,
        'total_price' => 49.99,
    ]);

    Event::fake();

    $service = app(RecordOrderPayment::class);
    $service->run($order, [
        'payment_status' => 'success',
        'payment_method' => 'cod',
    ]);

    Event::assertDispatched(OrderPaymentConfirmed::class, function (OrderPaymentConfirmed $event) use ($order, $customer) {
        expect($event->orderId)->toBe($order->id);
        expect($event->customerId)->toBe($customer->id);
        expect($event->customerEmail)->toBe('test@example.com');
        expect($event->items)->toBeArray();
        expect($event->items[0])->toHaveKeys(['product_id', 'order_product_id', 'quantity', 'is_downloadable']);
        expect($event->items[0]['is_downloadable'])->toBeTrue();

        return true;
    });
});

test('order placed by authenticated customer sets customer_id', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')->post('/site-order-store', [
        'name' => 'Authenticated customer',
        'phone' => '01712345678',
        'items' => [
            [
                'item' => ['id' => $this->physicalProduct->id, 'price' => 99.99],
                'quantity' => 1,
            ],
        ],
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('orders', [
        'name' => 'Authenticated customer',
        'customer_id' => $customer->id,
    ]);
});

test('orders page is guest protected', function () {
    $response = $this->get('/account/orders');
    $response->assertRedirect(route('customerAuth.loginForm'));
});

test('orders page can be rendered for authenticated customer', function () {
    $customer = Customer::factory()->create();

    $order = Order::create([
        'name' => 'Customer Order',
        'phone' => '01712345678',
        'customer_id' => $customer->id,
    ]);

    $response = $this->actingAs($customer, 'customer')->get('/account/orders');

    $response->assertStatus(200);
    $response->assertSee('#'.$order->id);
});
