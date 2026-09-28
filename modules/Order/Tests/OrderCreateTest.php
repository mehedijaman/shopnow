<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Customer\Models\Customer;
use Modules\Order\Database\Seeders\OrderAclSeeder;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductVariation;
use Modules\PromoCode\Models\PromoCode;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->category = ProductCategory::factory()->create(['parent_id' => null]);
    $this->product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Create Test Product',
        'price' => 100,
        'sale_price' => null,
        'quantity' => 50,
    ]);
});

function orderCreateItemRow(Product $product, array $overrides = []): array
{
    return array_merge([
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 100,
    ], $overrides);
}

function orderCreatePayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Walk-in Customer',
        'phone' => '01711111111',
        'address' => 'House 1, Road 2, Dhaka',
        'items' => [],
    ], $overrides);
}

test('order create page renders with catalog props', function () {
    $customer = Customer::factory()->create();

    $response = $this->loggedRequest->get('/admin/order/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderForm')
            ->has('products')
            ->where('statuses', OrderStatus::values())
            ->has('paymentMethods')
            ->has('customers', 1, fn (Assert $page) => $page->where('id', $customer->id)->etc())
    );
});

test('an order can be created with line items', function () {
    $response = $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'items' => [orderCreateItemRow($this->product)],
    ]));

    $order = Order::first();

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    expect((float) $order->subtotal)->toBe(200.0)
        ->and((float) $order->total)->toBe(200.0)
        ->and((float) $order->due)->toBe(200.0)
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->payment_status->value)->toBe('unpaid')
        ->and($order->orderProducts()->count())->toBe(1);

    $line = $order->orderProducts()->first();
    expect($line->product_id)->toBe($this->product->id)
        ->and((float) $line->total_price)->toBe(200.0);
});

test('creating an order requires customer details and at least one item', function () {
    $this->loggedRequest->post('/admin/order', [
        'items' => [orderCreateItemRow($this->product)],
    ])->assertSessionHasErrors(['name', 'phone']);

    $this->loggedRequest->post('/admin/order', orderCreatePayload())
        ->assertSessionHasErrors('items');

    $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'items' => [['product_id' => 999999, 'quantity' => 1, 'unit_price' => 10]],
    ]))->assertSessionHasErrors('items.0.product_id');

    expect(Order::count())->toBe(0);
});

test('creating an order decrements variation stock', function () {
    $variableProduct = Product::factory()->variable()->create(['category_id' => $this->category->id]);
    $variation = ProductVariation::create([
        'product_id' => $variableProduct->id,
        'sku' => 'CREATE-1',
        'price' => 150,
        'sale_price' => null,
        'quantity' => 8,
        'active' => true,
        'variation_key' => 'red-l',
    ]);

    $response = $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'items' => [orderCreateItemRow($variableProduct, [
            'product_variation_id' => $variation->id,
            'quantity' => 3,
            'unit_price' => 150,
        ])],
    ]));

    $order = Order::first();

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    expect($variation->refresh()->quantity)->toBe(5);

    $line = $order->orderProducts()->first();
    expect($line->product_variation_id)->toBe($variation->id)
        ->and((float) $order->subtotal)->toBe(450.0);
});

test('creating an order snapshots bundle children and decrements their stock', function () {
    $child = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Create Bundle Child',
        'price' => 100,
        'sale_price' => null,
        'quantity' => 6,
    ]);
    $bundle = Product::factory()->bundle()->create([
        'category_id' => $this->category->id,
        'name' => 'Create Test Bundle',
        'price' => 250,
        'quantity' => 5,
    ]);
    $bundle->bundleItems()->create(['child_product_id' => $child->id, 'quantity' => 2]);

    $response = $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'items' => [orderCreateItemRow($bundle, ['quantity' => 2, 'unit_price' => 250])],
    ]));

    $order = Order::first();

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    expect((int) $child->refresh()->quantity)->toBe(2)
        ->and((int) $bundle->refresh()->quantity)->toBe(5);

    $line = $order->orderProducts()->first();
    $snapshot = $line->bundleItems()->first();
    expect($snapshot->product_id)->toBe($child->id)
        ->and((int) $snapshot->quantity)->toBe(4);
});

test('creating an order creates a pending shipment when shipping is required', function () {
    $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'items' => [orderCreateItemRow($this->product)],
    ]));

    $order = Order::first();

    expect($order->requires_shipping)->toBe(1)
        ->and($order->orderShipments()->count())->toBe(1)
        ->and($order->orderShipments()->first()->shopment_status->value)->toBe('pending');
});

test('creating an order skips the shipment for virtual products', function () {
    $virtualProduct = Product::factory()->virtual()->create([
        'category_id' => $this->category->id,
        'price' => 100,
        'sale_price' => null,
        'quantity' => 10,
    ]);

    $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'items' => [orderCreateItemRow($virtualProduct)],
    ]));

    $order = Order::first();

    expect($order->requires_shipping)->toBe(0)
        ->and($order->orderShipments()->count())->toBe(0)
        ->and((float) $order->shipping)->toBe(0.0);
});

test('a coupon can be applied when creating an order', function () {
    PromoCode::factory()->create(['code' => 'SAVE10', 'discount_value' => 10]);

    $response = $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'coupon_code' => 'save10',
        'discount' => 0,
        'items' => [orderCreateItemRow($this->product)],
    ]));

    $order = Order::first();

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    expect($order->coupon_code)->toBe('SAVE10')
        ->and((float) $order->discount)->toBe(20.0)
        ->and((float) $order->total)->toBe(180.0)
        ->and((float) $order->due)->toBe(180.0);
});

test('an invalid coupon rejects order creation', function () {
    $response = $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'coupon_code' => 'NOPE',
        'items' => [orderCreateItemRow($this->product)],
    ]));

    $response->assertSessionHasErrors('coupon_code');
    expect(Order::count())->toBe(0);
});

test('paid amount drives payment status and balance due', function () {
    $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'paid' => 200,
        'items' => [orderCreateItemRow($this->product)],
    ]));

    $paidOrder = Order::first();
    expect($paidOrder->payment_status->value)->toBe('paid')
        ->and((float) $paidOrder->due)->toBe(0.0);

    $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'paid' => 50,
        'items' => [orderCreateItemRow($this->product)],
    ]));

    $partialOrder = Order::latest('id')->first();
    expect($partialOrder->payment_status->value)->toBe('unpaid')
        ->and((float) $partialOrder->due)->toBe(150.0);
});

test('the selected status and linked customer are stored', function () {
    $customer = Customer::factory()->create();

    $response = $this->loggedRequest->post('/admin/order', orderCreatePayload([
        'customer_id' => $customer->id,
        'status' => 'completed',
        'payment_method' => 'cod',
        'items' => [orderCreateItemRow($this->product)],
    ]));

    $order = Order::first();

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    expect($order->customer_id)->toBe($customer->id)
        ->and($order->status)->toBe(OrderStatus::Completed)
        ->and($order->payment_method)->toBe('cod')
        ->and($order->orderProducts()->count())->toBe(1);
});

test('coupon preview endpoint validates a code against the posted subtotal', function () {
    PromoCode::factory()->create(['code' => 'SAVE10', 'discount_value' => 10]);

    $response = $this->loggedRequest->postJson('/admin/order/coupon-preview', [
        'coupon_code' => 'save10',
        'subtotal' => 250,
    ]);

    $response->assertSuccessful();

    expect($response->json('coupon_code'))->toBe('SAVE10')
        ->and((float) $response->json('discount'))->toBe(25.0)
        ->and($response->json('waives_shipping'))->toBeFalse();
});

test('coupon preview endpoint rejects an unknown code', function () {
    $response = $this->loggedRequest->postJson('/admin/order/coupon-preview', [
        'coupon_code' => 'NOPE',
        'subtotal' => 200,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('coupon_code');
});

test('order create routes require the order create permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/order/create')->assertForbidden();
    $this->actingAs($user)->post('/admin/order', [])->assertForbidden();
    $this->actingAs($user)->postJson('/admin/order/coupon-preview', [])->assertForbidden();

    Permission::firstOrCreate(['name' => 'order-create', 'guard_name' => 'user']);
    $user->givePermissionTo('order-create');

    $this->actingAs($user)->get('/admin/order/create')->assertSuccessful();
    $this->actingAs($user)->post('/admin/order', [])->assertSessionHasErrors('name');
});

test('order acl seeder creates the order permissions', function () {
    $this->seed(OrderAclSeeder::class);

    foreach (['order-menu', 'order-list', 'order-create', 'order-edit', 'order-delete'] as $permission) {
        $this->assertDatabaseHas('permissions', [
            'name' => $permission,
            'guard_name' => 'user',
        ]);
    }
});
