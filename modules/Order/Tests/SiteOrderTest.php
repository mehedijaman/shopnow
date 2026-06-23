<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $category = ProductCategory::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    $this->product = Product::create([
        'category_id' => $category->id,
        'name' => 'Test Product',
        'slug' => 'test-product',
        'price' => 99.99,
        'quantity' => '10',
        'active' => true,
    ]);
});

test('order can be placed via site', function () {
    $response = $this->post('/site-order-store', [
        'name' => 'John Doe',
        'phone' => '01712345678',
        'items' => [
            [
                'item' => ['id' => $this->product->id, 'price' => 99.99],
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
