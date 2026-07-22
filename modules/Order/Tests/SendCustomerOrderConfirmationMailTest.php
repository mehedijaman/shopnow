<?php

use App\Jobs\SendCustomerOrderConfirmationMail;
use App\Mail\CustomerOrderConfirmationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('send customer order confirmation mail job sends email to customer', function () {
    $order = Order::create([
        'name' => 'John Doe',
        'phone' => '01712345678',
        'email' => 'customer@shopnow.com',
        'payment_method' => 'cod',
        'subtotal' => 500,
        'shipping' => 60,
        'tax' => 0,
        'total' => 560,
        'status' => 'pending',
    ]);

    Mail::fake();

    (new SendCustomerOrderConfirmationMail($order->id))->handle();

    Mail::assertSent(CustomerOrderConfirmationMail::class, function ($mail) use ($order) {
        return $mail->hasTo('customer@shopnow.com') && $mail->order->id === $order->id;
    });
});

test('placing an order with customer email dispatches SendCustomerOrderConfirmationMail job', function () {
    Queue::fake();

    $product = Product::factory()->create([
        'price' => 200,
        'quantity' => 10,
        'active' => true,
    ]);

    $cartData = [
        $product->id => [
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 200,
            'total_price' => 400,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => 200,
            ],
        ],
    ];

    $response = $this->withSession(['cart' => $cartData])->post('/site-order-store', [
        'name' => 'Test Customer',
        'phone' => '01700000000',
        'email' => 'testcustomer@shopnow.com',
        'district' => 'Dhaka',
        'address' => 'House 123, Road 4',
        'payment_method' => 'cod',
        'items' => [
            [
                'item' => [
                    'id' => $product->id,
                    'price' => 200,
                ],
                'quantity' => 2,
            ],
        ],
    ]);

    $response->assertCreated();

    Queue::assertPushed(SendCustomerOrderConfirmationMail::class, function ($job) {
        return true;
    });
});
