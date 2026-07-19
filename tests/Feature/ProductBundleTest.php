<?php

use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Services\CalculateBundlePrice;
use Modules\Product\Services\CheckBundleStock;
use Modules\Product\Services\ValidateBundleComposition;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->category = ProductCategory::factory()->create(['parent_id' => null]);
    $this->brand = ProductBrand::factory()->create(['parent_id' => null]);
});

test('bundle cannot include itself as a child', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $bundle->bundleConfig()->create([
        'pricing_type' => 'calculated', 'discount_type' => 'none',
    ]);

    try {
        app(ValidateBundleComposition::class)->run($bundle, [$bundle->id]);
        $this->fail('Expected exception');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(422);
    }
});

test('bundle cannot include another bundle as a child', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $otherBundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 50, 'quantity' => 5,
    ]);

    $bundle->bundleConfig()->create([
        'pricing_type' => 'calculated', 'discount_type' => 'none',
    ]);

    try {
        app(ValidateBundleComposition::class)->run($bundle, [$otherBundle->id]);
        $this->fail('Expected exception');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(422);
    }
});

test('calculated bundle price sums child products', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $childA = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 30, 'sale_price' => 25, 'quantity' => 10,
    ]);
    $childB = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 20, 'sale_price' => 15, 'quantity' => 10,
    ]);

    $bundle->bundleConfig()->create([
        'pricing_type' => 'calculated', 'discount_type' => 'none',
    ]);

    $bundle->bundleItems()->createMany([
        ['child_product_id' => $childA->id, 'quantity' => 2, 'sort_order' => 0],
        ['child_product_id' => $childB->id, 'quantity' => 1, 'sort_order' => 1],
    ]);

    $price = app(CalculateBundlePrice::class)->run($bundle);

    expect($price)->toBe(65.00); // (25*2) + 15
});

test('fixed bundle price ignores children', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $child = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 50, 'quantity' => 10,
    ]);

    $bundle->bundleConfig()->create([
        'pricing_type' => 'fixed', 'fixed_price' => 49.99, 'discount_type' => 'none',
    ]);

    $bundle->bundleItems()->create([
        'child_product_id' => $child->id, 'quantity' => 1, 'sort_order' => 0,
    ]);

    $price = app(CalculateBundlePrice::class)->run($bundle);

    expect($price)->toBe(49.99);
});

test('bundle stock reflects the most constrained child', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $childA = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 10, 'quantity' => 20,
    ]);
    $childB = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 10, 'quantity' => 10,
    ]);

    $bundle->bundleItems()->createMany([
        ['child_product_id' => $childA->id, 'quantity' => 2, 'sort_order' => 0],
        ['child_product_id' => $childB->id, 'quantity' => 3, 'sort_order' => 1],
    ]);

    $stock = app(CheckBundleStock::class)->run($bundle);

    // childA: floor(20/2) = 10, childB: floor(10/3) = 3
    expect($stock['available_bundles'])->toBe(3);
    expect($stock['in_stock'])->toBeTrue();
});

test('bundle is out of stock when any child runs out', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $child = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 10, 'quantity' => 0,
    ]);

    $bundle->bundleItems()->create([
        'child_product_id' => $child->id, 'quantity' => 1, 'sort_order' => 0,
    ]);

    $stock = app(CheckBundleStock::class)->run($bundle);

    expect($stock['in_stock'])->toBeFalse();
    expect($stock['available_bundles'])->toBe(0);
});

test('admin can add and remove bundle items', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $child = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 25, 'quantity' => 10,
    ]);

    // Save pricing config
    $this->loggedRequest->post(route('product.bundle.saveConfig', $bundle->id), [
        'pricing_type' => 'calculated',
        'discount_type' => 'percentage',
        'discount_value' => 10,
    ])->assertJson(['success' => true]);

    $bundle->refresh();
    expect($bundle->bundleConfig->pricing_type)->toBe('calculated');

    // Add item
    $addRes = $this->loggedRequest->post(route('product.bundle.items.add', $bundle->id), [
        'child_product_id' => $child->id,
        'quantity' => 2,
        'is_optional' => false,
    ]);

    $addRes->assertStatus(201);
    expect($addRes->json('item.child_product_id'))->toBe($child->id);
    expect($addRes->json('item.quantity'))->toBe(2);

    // Remove item
    $itemId = $addRes->json('item.id');
    $this->loggedRequest->delete(route('product.bundle.items.remove', [$bundle->id, $itemId]))
        ->assertJson(['success' => true]);
});

test('order correctly creates bundle items via store endpoint', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $child = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 25, 'sale_price' => null, 'quantity' => 10,
    ]);

    $bundle->bundleConfig()->create([
        'pricing_type' => 'calculated', 'discount_type' => 'none',
    ]);

    $bundle->bundleItems()->create([
        'child_product_id' => $child->id, 'quantity' => 1, 'sort_order' => 0,
    ]);

    $payload = [
        'name' => 'Test Customer',
        'email' => 'customer@test.com',
        'phone' => '1234567890',
        'address' => '123 Test St',
        'city' => 'Test City',
        'postcode' => '12345',
        'payment_method' => 'cod',
        'payment_status' => 'unpaid',
        'items' => [
            [
                'quantity' => 1,
                'item' => ['id' => $bundle->id, 'name' => $bundle->name, 'price' => 50],
            ],
        ],
    ];

    $response = $this->post(route('site.order.store'), $payload);
    $response->assertStatus(201);

    $order = Order::with('orderProducts.bundleItems')->first();
    expect($order)->not->toBeNull();
    expect($order->orderProducts)->toHaveCount(1);

    $op = $order->orderProducts->first();
    expect($op->bundleItems)->toHaveCount(1);

    $bi = $op->bundleItems->first();
    expect((float) $bi->unit_price)->toBe(25.0);
    expect($bi->quantity)->toBe(1);
});

test('order correctly snapshots bundle items', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $child = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 25, 'sale_price' => null, 'quantity' => 10,
    ]);

    $bundle->bundleConfig()->create([
        'pricing_type' => 'calculated', 'discount_type' => 'none',
    ]);

    $bundle->bundleItems()->create([
        'child_product_id' => $child->id, 'quantity' => 1, 'sort_order' => 0,
    ]);

    // Directly create the order
    $order = Order::create([
        'name' => 'Test', 'email' => 'test@test.com', 'phone' => '1234567890',
        'payment_method' => 'cod', 'payment_status' => 'unpaid',
    ]);

    $op = $order->orderProducts()->create([
        'product_id' => $bundle->id, 'quantity' => 1,
        'unit_price' => 50, 'discount' => 0, 'total_price' => 50,
    ]);

    // Test snapshot: create bundle items for this order product
    $bundle->refresh();
    $bundleItems = $bundle->bundleItems()->with('childProduct')->get();
    foreach ($bundleItems as $bi) {
        $op->bundleItems()->create([
            'product_id' => $bi->child_product_id,
            'product_variation_id' => $bi->child_product_variation_id,
            'name' => $bi->childProduct?->name,
            'sku' => $bi->childProduct?->sku,
            'quantity' => $bi->quantity,
            'unit_price' => (float) ($bi->price_override ?? $bi->childProduct?->sale_price ?? $bi->childProduct?->price ?? 0),
            'total_price' => 0,
        ]);
    }

    $op->refresh();
    expect($op->bundleItems)->toHaveCount(1);

    $bi = $op->bundleItems->first();
    expect((float) $bi->unit_price)->toBe(25.0);
    expect($bi->quantity)->toBe(1);
});

test('force-delete blocked when product is referenced in bundle', function () {
    $bundle = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'bundle',
        'price' => 100, 'quantity' => 10,
    ]);

    $child = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 25, 'quantity' => 10,
    ]);

    $bundle->bundleItems()->create([
        'child_product_id' => $child->id, 'quantity' => 1, 'sort_order' => 0,
    ]);

    // Soft delete first
    $child->delete();

    // Attempt force delete
    $response = $this->loggedRequest->delete(route('product.recycleBin.destroyForce', $child->id));
    $response->assertSessionHas('error');
});
