<?php

use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Cart\Models\Cart;
use Modules\Cart\Services\GetCartTotals;
use Modules\Customer\Models\Customer;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $user = User::factory()->create();
    Role::create(['name' => 'root']);
    $user->assignRole('root');

    $category = ProductCategory::factory()->create(['parent_id' => null]);
    $brand = ProductBrand::factory()->create(['parent_id' => null]);
    $this->product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'active' => true,
        'price' => 100.00,
        'sale_price' => null,
    ]);
});

test('site cart page loads', function () {
    $response = $this->get('/cart');

    $response->assertStatus(200);
});

test('checkout page loads', function () {
    $response = $this->get('/checkout');

    $response->assertStatus(200);
});

test('adding an item as a guest creates a cart with guest_token', function () {
    $guestToken = (string) Str::uuid();

    $response = $this->withHeader('X-Cart-Token', $guestToken)
        ->postJson('/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

    $response->assertStatus(200);

    $cart = Cart::where('guest_token', $guestToken)->first();
    expect($cart)->not->toBeNull();
    expect($cart->customer_id)->toBeNull();

    expect($cart->items()->count())->toBe(1);
    expect($cart->items()->first()->product_id)->toBe($this->product->id);
    expect($cart->items()->first()->quantity)->toBe(2);
});

test('adding an item while authenticated ties the cart to customer_id', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer');

    $response = $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $response->assertStatus(200);

    $cart = Cart::where('customer_id', $customer->id)->first();
    expect($cart)->not->toBeNull();

    expect($cart->items()->count())->toBe(1);
    expect($cart->items()->first()->product_id)->toBe($this->product->id);
});

test('add item increments quantity when same product already in cart', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $response = $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 3,
    ]);

    $response->assertStatus(200);

    $cart = Cart::where('customer_id', $customer->id)->first();
    expect($cart->items()->first()->quantity)->toBe(4);
});

test('get cart totals reflects current price', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 2,
    ]);

    $response = $this->getJson('/cart/fetch');

    $response->assertStatus(200);
    $response->assertJson([
        'subtotal' => 200.00,
        'totalQuantity' => 2,
        'totalItems' => 1,
    ]);

    $this->product->update(['price' => 150.00]);

    $response = $this->getJson('/cart/fetch');
    $response->assertJson([
        'subtotal' => 300.00,
        'totalQuantity' => 2,
    ]);
});

test('remove cart item', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $cart = Cart::where('customer_id', $customer->id)->first();
    $itemId = $cart->items()->first()->id;

    $response = $this->deleteJson("/cart/items/{$itemId}");
    $response->assertStatus(200);
    expect($response->json('totalItems'))->toBe(0);
});

test('clear cart removes all items', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $response = $this->deleteJson('/cart/items');
    $response->assertStatus(200);
    expect($response->json('totalItems'))->toBe(0);

    $cart = Cart::where('customer_id', $customer->id)->first();
    expect($cart->items()->count())->toBe(0);
});

test('add item fails for inactive product', function () {
    $this->product->update(['active' => false]);

    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $response = $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $response->assertStatus(422);
});

test('add item requires valid product', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $response = $this->postJson('/cart/items', [
        'product_id' => 99999,
        'quantity' => 1,
    ]);

    $response->assertStatus(422);
});

test('add item requires minimum quantity of 1', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $response = $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 0,
    ]);

    $response->assertStatus(422);
});

test('update cart item quantity', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $cart = Cart::where('customer_id', $customer->id)->first();
    $itemId = $cart->items()->first()->id;

    $response = $this->putJson("/cart/items/{$itemId}", [
        'product_id' => $this->product->id,
        'quantity' => 5,
    ]);
    $response->assertStatus(200);
    expect($response->json('totalQuantity'))->toBe(5);
});

test('logged in customer gets same cart across requests', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $firstCart = Cart::where('customer_id', $customer->id)->first();

    $response = $this->getJson('/cart/fetch');
    $response->assertStatus(200);
    expect($response->json('totalItems'))->toBe(1);

    $carts = Cart::where('customer_id', $customer->id)->get();
    expect($carts->count())->toBe(1);
});

test('guest cart persists across requests via header', function () {
    $guestToken = (string) Str::uuid();

    $this->withHeader('X-Cart-Token', $guestToken)
        ->postJson('/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

    $response = $this->withHeader('X-Cart-Token', $guestToken)
        ->getJson('/cart/fetch');

    $response->assertStatus(200);
    expect($response->json('totalQuantity'))->toBe(2);
});

test('logging in merges guest cart into customer cart', function () {
    $guestToken = (string) Str::uuid();

    $this->withHeader('X-Cart-Token', $guestToken)
        ->postJson('/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer');

    $this->withHeader('X-Cart-Token', $guestToken)
        ->postJson('/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

    event(new Login('customer', $customer, false));

    $cart = Cart::where('customer_id', $customer->id)->first();
    expect($cart)->not->toBeNull();

    $item = $cart->items()->first();
    expect($item)->not->toBeNull();
    expect($item->quantity)->toBe(5);

    $guestCart = Cart::where('guest_token', $guestToken)->first();
    expect($guestCart)->toBeNull();
});

test('GetCartTotals returns correct structure', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    $this->postJson('/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 3,
    ]);

    $cart = Cart::where('customer_id', $customer->id)->first();
    $getCartTotals = app(GetCartTotals::class);
    $totals = $getCartTotals->run($cart);

    expect($totals)->toHaveKeys(['items', 'totalItems', 'totalQuantity', 'subtotal', 'tax']);
    expect($totals['totalItems'])->toBe(1);
    expect($totals['totalQuantity'])->toBe(3);
    expect($totals['subtotal'])->toBe(300.00);
    expect($totals['tax'])->toBe(0);
});

test('resolving cart does not fail when guest token exists but cart is soft-deleted', function () {
    $guestToken = (string) Str::uuid();

    $cart = Cart::create(['guest_token' => $guestToken]);
    $cart->delete();

    $response = $this->withHeader('X-Cart-Token', $guestToken)
        ->get('/cart');

    $response->assertStatus(200);

    $newCarts = Cart::all();
    expect($newCarts->count())->toBe(1);
    expect($newCarts->first()->guest_token)->not->toBe($guestToken);
});
