<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderProduct;
use Modules\Product\Enums\ProductType;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);
});

test('new products default to type simple', function () {
    $category = ProductCategory::factory()->create(['parent_id' => null]);
    $brand = ProductBrand::factory()->create(['parent_id' => null]);

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
    ]);

    expect($product->type)->toBe(ProductType::Simple);
    expect($product->type->value)->toBe('simple');
});

test('requiresShipping returns true when is_virtual is false', function () {
    $product = Product::factory()->create(['is_virtual' => false]);

    expect($product->requiresShipping())->toBeTrue();
});

test('requiresShipping returns false when is_virtual is true', function () {
    $product = Product::factory()->create(['is_virtual' => true]);

    expect($product->requiresShipping())->toBeFalse();
});

test('requiresShipping is independent of is_downloadable', function () {
    $product = Product::factory()->create([
        'is_virtual' => false,
        'is_downloadable' => true,
    ]);

    expect($product->requiresShipping())->toBeTrue();

    $product2 = Product::factory()->create([
        'is_virtual' => true,
        'is_downloadable' => true,
    ]);

    expect($product2->requiresShipping())->toBeFalse();

    $product3 = Product::factory()->create([
        'is_virtual' => true,
        'is_downloadable' => false,
    ]);

    expect($product3->requiresShipping())->toBeFalse();

    $product4 = Product::factory()->create([
        'is_virtual' => false,
        'is_downloadable' => false,
    ]);

    expect($product4->requiresShipping())->toBeTrue();
});

test('order product can be soft deleted and excluded from default queries', function () {
    $order = Order::create([
        'name' => 'Test Order',
        'phone' => '01712345678',
    ]);

    $product = Product::factory()->create();

    $orderProduct = OrderProduct::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 99.99,
        'discount' => 0,
        'total_price' => 99.99,
    ]);

    expect(OrderProduct::all())->toHaveCount(1);

    $orderProduct->delete();

    expect(OrderProduct::all())->toHaveCount(0);
    expect(OrderProduct::withTrashed()->find($orderProduct->id))->not->toBeNull();
});
