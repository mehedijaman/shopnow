<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderProduct;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductVariation;
use Modules\PromoCode\Models\PromoCode;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->order = Order::create([
        'name' => 'Test Order',
        'phone' => '01712345678',
    ])->fresh();

    $this->category = ProductCategory::factory()->create(['parent_id' => null]);
    $this->product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Edit Test Product',
        'price' => 100,
        'sale_price' => null,
        'quantity' => 50,
    ]);
});

test('order list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/order');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderIndex')
            ->has(
                'orders.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->order->id)
                    ->where('name', $this->order->name)
                    ->where('status', $this->order->status->value)
                    ->where('payment_status', $this->order->payment_status->value)
                    ->where('total', $this->order->total)
                    ->etc()
            )
    );
});

test('order create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/order/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderForm')
    );
});

test('order edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/order/'.$this->order->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderEdit')
            ->has(
                'order',
                fn (Assert $page) => $page
                    ->where('id', $this->order->id)
                    ->where('name', $this->order->name)
                    ->etc()
            )
            ->has('products')
            ->where('lockedItems', false)
    );
});

test('order customer fields can be updated', function () {
    $response = $this->loggedRequest->put('/admin/order/'.$this->order->id, [
        'name' => 'Updated Order',
        'phone' => '01712345678',
        'address' => 'House 1, Road 2, Dhaka',
        'district' => 'Dhaka',
    ]);

    $response->assertRedirect('/admin/order/'.$this->order->id.'/show');

    $this->assertDatabaseHas('orders', [
        'id' => $this->order->id,
        'name' => 'Updated Order',
        'address' => 'House 1, Road 2, Dhaka',
        'district' => 'Dhaka',
    ]);
});

test('order can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/order/'.$this->order->id);

    $response->assertRedirect('/admin/order');

    $this->assertCount(0, Order::all());
});

test('order show page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/order/'.$this->order->id.'/show');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderShow')
            ->has(
                'order',
                fn (Assert $page) => $page
                    ->where('id', $this->order->id)
                    ->where('name', $this->order->name)
                    ->etc()
            )
    );
});

test('order status can be updated', function () {
    $response = $this->loggedRequest->from('/admin/order')->patch('/admin/order/'.$this->order->id.'/status', [
        'status' => 'processing',
    ]);

    $response->assertRedirect('/admin/order');

    $this->assertEquals(OrderStatus::Processing, Order::find($this->order->id)->status);
});

test('order invoice can be downloaded', function () {
    $response = $this->loggedRequest->get('/admin/order/'.$this->order->id.'/download-invoice');

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
    $response->assertHeader('content-disposition', 'attachment; filename=invoice-'.$this->order->id.'.pdf');
});

function orderEditPayload(Order $order, array $items, array $overrides = []): array
{
    return array_merge([
        'name' => $order->name,
        'phone' => $order->phone,
        'items' => $items,
        'shipping' => 0,
        'tax' => 0,
        'discount' => 0,
        'coupon_code' => '',
    ], $overrides);
}

function orderEditItemRow(OrderProduct $line, array $overrides = []): array
{
    return array_merge([
        'id' => $line->id,
        'product_id' => $line->product_id,
        'product_variation_id' => $line->product_variation_id,
        'quantity' => $line->quantity,
        'unit_price' => $line->unit_price,
        'discount' => $line->discount,
    ], $overrides);
}

function makeEditableOrder(array $overrides = []): Order
{
    return Order::create(array_merge([
        'name' => 'Editable Order',
        'phone' => '01712345678',
        'subtotal' => 200,
        'shipping' => 0,
        'tax' => 0,
        'discount' => 0,
        'total' => 200,
        'paid' => 0,
        'due' => 200,
    ], $overrides));
}

function addSimpleLine(Order $order, Product $product, int $quantity = 2, float $unitPrice = 100): OrderProduct
{
    return $order->orderProducts()->create([
        'product_id' => $product->id,
        'quantity' => $quantity,
        'unit_price' => $unitPrice,
        'discount' => 0,
        'total_price' => $unitPrice * $quantity,
    ]);
}

test('editing item quantity recomputes subtotal total and due', function () {
    $order = makeEditableOrder();
    $line = addSimpleLine($order, $this->product);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line, ['quantity' => 3]),
    ]));

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    $order->refresh();
    expect((float) $order->subtotal)->toBe(300.0)
        ->and((float) $order->total)->toBe(300.0)
        ->and((float) $order->due)->toBe(300.0)
        ->and($order->payment_status->value)->toBe('unpaid');
});

test('adding a new line item updates the order', function () {
    $order = makeEditableOrder();
    $line = addSimpleLine($order, $this->product);

    $second = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Second Product',
        'price' => 50,
        'sale_price' => null,
        'quantity' => 10,
    ]);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line),
        ['product_id' => $second->id, 'quantity' => 1, 'unit_price' => 50, 'discount' => 0],
    ]));

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    $order->refresh();
    expect($order->orderProducts()->count())->toBe(2)
        ->and((float) $order->subtotal)->toBe(250.0)
        ->and((float) $order->total)->toBe(250.0)
        ->and((float) $order->due)->toBe(250.0);
});

test('removing a line item soft deletes it and restocks variations', function () {
    $order = makeEditableOrder(['subtotal' => 500, 'total' => 500, 'due' => 500]);
    $simpleLine = addSimpleLine($order, $this->product);

    $variableProduct = Product::factory()->variable()->create(['category_id' => $this->category->id]);
    $variation = ProductVariation::create([
        'product_id' => $variableProduct->id,
        'sku' => 'EDIT-1',
        'price' => 150,
        'sale_price' => null,
        'quantity' => 8,
        'active' => true,
        'variation_key' => 'red-l',
    ]);
    $variationLine = $order->orderProducts()->create([
        'product_id' => $variableProduct->id,
        'product_variation_id' => $variation->id,
        'quantity' => 2,
        'unit_price' => 150,
        'discount' => 0,
        'total_price' => 300,
    ]);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($simpleLine),
    ]));

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    expect(OrderProduct::withTrashed()->find($variationLine->id)->trashed())->toBeTrue()
        ->and($variation->refresh()->quantity)->toBe(10)
        ->and($order->orderProducts()->count())->toBe(1);

    $order->refresh();
    expect((float) $order->subtotal)->toBe(200.0)
        ->and((float) $order->total)->toBe(200.0);
});

test('changing variation quantity adjusts stock by delta', function () {
    $order = makeEditableOrder(['subtotal' => 300, 'total' => 300, 'due' => 300]);

    $variableProduct = Product::factory()->variable()->create(['category_id' => $this->category->id]);
    $variation = ProductVariation::create([
        'product_id' => $variableProduct->id,
        'sku' => 'EDIT-2',
        'price' => 150,
        'sale_price' => null,
        'quantity' => 8,
        'active' => true,
        'variation_key' => 'blue-m',
    ]);
    $line = $order->orderProducts()->create([
        'product_id' => $variableProduct->id,
        'product_variation_id' => $variation->id,
        'quantity' => 2,
        'unit_price' => 150,
        'discount' => 0,
        'total_price' => 300,
    ]);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line, ['quantity' => 3]),
    ]));

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    expect($variation->refresh()->quantity)->toBe(7);
    expect((float) $order->refresh()->subtotal)->toBe(450.0);
});

test('editing bundle quantity updates child snapshots and stock', function () {
    $child = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Bundle Child',
        'price' => 100,
        'sale_price' => null,
        'quantity' => 6,
    ]);
    $bundle = Product::factory()->bundle()->create([
        'category_id' => $this->category->id,
        'name' => 'Test Bundle',
        'price' => 250,
        'quantity' => 5,
    ]);
    $bundle->bundleItems()->create(['child_product_id' => $child->id, 'quantity' => 2]);

    $order = makeEditableOrder(['subtotal' => 250, 'total' => 250, 'due' => 250]);
    $line = $order->orderProducts()->create([
        'product_id' => $bundle->id,
        'quantity' => 1,
        'unit_price' => 250,
        'discount' => 0,
        'total_price' => 250,
    ]);
    $line->bundleItems()->create([
        'product_id' => $child->id,
        'name' => 'Bundle Child',
        'quantity' => 2,
        'unit_price' => 100,
        'total_price' => 200,
    ]);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line, ['quantity' => 2]),
    ]));

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    expect((int) $child->refresh()->quantity)->toBe(4);
    expect($line->refresh()->bundleItems()->first()->quantity)->toBe(4);
    expect((float) $order->refresh()->total)->toBe(500.0);
});

test('coupon is revalidated and discount recomputed on edit', function () {
    PromoCode::factory()->create(['code' => 'SAVE10', 'discount_value' => 10, 'usage_limit' => 1]);

    $order = makeEditableOrder([
        'coupon_code' => 'SAVE10',
        'discount' => 20,
        'subtotal' => 200,
        'total' => 180,
        'due' => 180,
    ]);
    $line = addSimpleLine($order, $this->product);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line, ['quantity' => 3]),
    ], ['coupon_code' => 'SAVE10']));

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    $order->refresh();
    expect($order->coupon_code)->toBe('SAVE10')
        ->and((float) $order->discount)->toBe(30.0)
        ->and((float) $order->total)->toBe(270.0)
        ->and((float) $order->due)->toBe(270.0);
});

test('unknown coupon code rejects the update', function () {
    $order = makeEditableOrder();
    $line = addSimpleLine($order, $this->product);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line),
    ], ['coupon_code' => 'NOPE']));

    $response->assertSessionHasErrors('coupon_code');
    expect((float) $order->refresh()->total)->toBe(200.0);
});

test('coupon usage limits exclude the order being edited', function () {
    PromoCode::factory()->usageLimit(1)->create(['code' => 'ONCE']);

    // Another order already consumed the only allowed use.
    Order::create(['name' => 'Other Order', 'phone' => '01800000000', 'coupon_code' => 'ONCE']);

    $order = makeEditableOrder();
    $line = addSimpleLine($order, $this->product);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line),
    ], ['coupon_code' => 'ONCE']));

    $response->assertSessionHasErrors('coupon_code');
    expect($order->refresh()->coupon_code)->toBeNull();
});

test('manual discount applies when no coupon is set', function () {
    $order = makeEditableOrder();
    $line = addSimpleLine($order, $this->product);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line),
    ], ['discount' => 25]));

    $response->assertRedirect('/admin/order/'.$order->id.'/show');

    $order->refresh();
    expect((float) $order->discount)->toBe(25.0)
        ->and((float) $order->total)->toBe(175.0)
        ->and((float) $order->due)->toBe(175.0);
});

test('items and totals are locked once order is shipped', function () {
    $order = makeEditableOrder(['status' => OrderStatus::Shipped]);
    $line = addSimpleLine($order, $this->product);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line, ['quantity' => 5]),
    ]));

    $response->assertSessionHasErrors('items');
    expect($line->refresh()->quantity)->toBe(2);

    // Customer fields stay editable with an unchanged payload.
    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line),
    ], ['name' => 'Renamed Shipped Order']));

    $response->assertRedirect('/admin/order/'.$order->id.'/show');
    expect($order->refresh()->name)->toBe('Renamed Shipped Order')
        ->and($line->refresh()->quantity)->toBe(2);
});

test('items and totals are locked when courier consignment exists', function () {
    $order = makeEditableOrder();
    $line = addSimpleLine($order, $this->product);

    $order->orderShipments()->create([
        'shopment_status' => ShipmentStatus::Pending,
        'consignment_id' => 'CF-9999',
    ]);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, [
        orderEditItemRow($line, ['quantity' => 5]),
    ]));

    $response->assertSessionHasErrors('items');
    expect($line->refresh()->quantity)->toBe(2);
});

test('clearing all items is rejected', function () {
    $order = makeEditableOrder();
    addSimpleLine($order, $this->product);

    $response = $this->loggedRequest->put('/admin/order/'.$order->id, orderEditPayload($order, []));

    $response->assertSessionHasErrors('items');
    expect($order->orderProducts()->count())->toBe(1);
});

test('changing phone clears stale fraud results', function () {
    $this->order->update([
        'fraud_risk' => 'high',
        'fraud_checked_at' => now(),
        'fraud_details' => ['score' => 90],
    ]);

    $response = $this->loggedRequest->put('/admin/order/'.$this->order->id, [
        'name' => $this->order->name,
        'phone' => '01999999999',
    ]);

    $response->assertRedirect('/admin/order/'.$this->order->id.'/show');

    $this->order->refresh();
    expect($this->order->phone)->toBe('01999999999')
        ->and($this->order->fraud_risk)->toBeNull()
        ->and($this->order->fraud_checked_at)->toBeNull()
        ->and($this->order->fraud_details)->toBeNull();
});

test('coupon preview endpoint validates a code against the edited subtotal', function () {
    PromoCode::factory()->create(['code' => 'SAVE10', 'discount_value' => 10]);

    $order = makeEditableOrder();

    $response = $this->loggedRequest->putJson('/admin/order/'.$order->id.'/coupon', [
        'coupon_code' => 'save10',
        'subtotal' => 250,
    ]);

    $response->assertSuccessful();

    expect($response->json('coupon_code'))->toBe('SAVE10')
        ->and((float) $response->json('discount'))->toBe(25.0)
        ->and($response->json('waives_shipping'))->toBeFalse();
});

test('coupon preview endpoint rejects an unknown code', function () {
    $order = makeEditableOrder();

    $response = $this->loggedRequest->putJson('/admin/order/'.$order->id.'/coupon', [
        'coupon_code' => 'NOPE',
        'subtotal' => 200,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('coupon_code');
});
