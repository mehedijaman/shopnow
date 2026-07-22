<?php

use App\Jobs\SendCustomerOrderConfirmationMail;
use App\Jobs\SendOrderPlacedMail;
use App\Mail\OrderPlacedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('send order placed mail job can be dispatched', function () {
    $order = Order::create([
        'name' => 'Test Order',
        'phone' => '01712345678',
    ]);

    Queue::fake();

    SendOrderPlacedMail::dispatch($order->id, 'admin@shopnow.com');

    Queue::assertPushed(SendOrderPlacedMail::class, function ($job) use ($order) {
        return $job->orderId === $order->id && $job->adminEmail === 'admin@shopnow.com';
    });
});

test('send order placed mail job sends email to admin', function () {
    $order = Order::create([
        'name' => 'Test Order',
        'phone' => '01712345678',
        'subtotal' => 1000,
        'shipping' => 60,
        'total' => 1060,
    ]);

    Mail::fake();

    (new SendOrderPlacedMail($order->id, 'admin@shopnow.com'))->handle();

    Mail::assertSent(OrderPlacedMail::class, function ($mail) use ($order) {
        return $mail->hasTo('admin@shopnow.com') && $mail->order->id === $order->id;
    });
});

test('placing an order dispatches SendOrderPlacedMail and SendCustomerOrderConfirmationMail', function () {
    Queue::fake();
    config(['mail.from.address' => 'store@shopnow.com']);

    $product = Product::factory()->create([
        'price' => 500,
        'quantity' => 10,
        'active' => true,
    ]);

    $cartData = [
        $product->id => [
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 500,
            'total_price' => 500,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => 500,
            ],
        ],
    ];

    $response = $this->withSession(['cart' => $cartData])->post('/site-order-store', [
        'name' => 'John Customer',
        'phone' => '01711112222',
        'email' => 'john@shopnow.com',
        'district' => 'Dhaka',
        'address' => 'Test Address',
        'payment_method' => 'cod',
        'items' => [
            [
                'item' => [
                    'id' => $product->id,
                    'price' => 500,
                ],
                'quantity' => 1,
            ],
        ],
    ]);

    $response->assertCreated();

    Queue::assertPushed(SendOrderPlacedMail::class);
    Queue::assertPushed(SendCustomerOrderConfirmationMail::class);
});

test('order placed email renders product variation details', function () {
    $product = Product::factory()->create(['name' => 'T-Shirt']);
    $order = Order::create([
        'name' => 'Test Customer',
        'phone' => '01712345678',
        'subtotal' => 500,
        'shipping' => 60,
        'total' => 560,
    ]);

    $order->orderProducts()->create([
        'product_id' => $product->id,
        'variation_label' => 'Size: L, Color: Red',
        'quantity' => 1,
        'unit_price' => 500,
        'discount' => 0,
        'total_price' => 500,
    ]);

    $order->load(['orderProducts.product', 'orderProducts.productVariation', 'orderProducts.bundleItems']);

    $mailable = new OrderPlacedMail($order);
    $html = $mailable->render();

    expect($html)->toContain('T-Shirt');
    expect($html)->toContain('Variation: Size: L, Color: Red');
});
