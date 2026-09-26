<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\PromoCode\Enums\DiscountType;
use Modules\PromoCode\Models\PromoCode;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->guestToken = (string) Str::uuid();

    $category = ProductCategory::factory()->create(['parent_id' => null]);
    $brand = ProductBrand::factory()->create(['parent_id' => null]);

    $this->product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'active' => true,
        'price' => 100.00,
        'sale_price' => null,
        'quantity' => 100,
        'is_virtual' => false,
        'is_downloadable' => false,
    ]);

    $this->withHeader('X-Cart-Token', $this->guestToken)
        ->postJson('/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ])
        ->assertStatus(200);
});

afterEach(function () {
    Cache::forget('settings');
});

function promoGuestCart(): Cart
{
    return Cart::where('guest_token', test()->guestToken)->firstOrFail();
}

// ── Cart apply / preview ──

test('valid percentage coupon applies discount to the cart', function () {
    PromoCode::factory()->create([
        'code' => 'SAVE10',
        'discount_type' => DiscountType::Percentage,
        'discount_value' => 10,
    ]);

    $response = $this->withHeader('X-Cart-Token', $this->guestToken)
        ->postJson('/cart/coupon', ['code' => 'save10']);

    $response->assertStatus(200);
    expect($response->json('coupon.code'))->toBe('SAVE10');
    expect((float) $response->json('discount'))->toEqual(20.0);
    expect(promoGuestCart()->coupon_code)->toBe('SAVE10');
});

test('percentage coupon discount respects the maximum discount cap', function () {
    PromoCode::factory()->create([
        'code' => 'CAPPED',
        'discount_type' => DiscountType::Percentage,
        'discount_value' => 50,
        'maximum_discount_amount' => 15,
    ]);

    $response = $this->withHeader('X-Cart-Token', $this->guestToken)
        ->postJson('/cart/coupon', ['code' => 'CAPPED']);

    $response->assertStatus(200);
    expect((float) $response->json('discount'))->toEqual(15.0);
});

test('applying an unknown coupon returns a validation error', function () {
    $response = $this->withHeader('X-Cart-Token', $this->guestToken)
        ->postJson('/cart/coupon', ['code' => 'NOPE']);

    $response->assertStatus(422);
    expect($response->json('errors.coupon_code'))->not->toBeNull();
    expect(promoGuestCart()->coupon_code)->toBeNull();
});

test('coupon below the minimum order amount is rejected', function () {
    PromoCode::factory()->create([
        'code' => 'MIN500',
        'minimum_order_amount' => 500,
    ]);

    $response = $this->withHeader('X-Cart-Token', $this->guestToken)
        ->postJson('/cart/coupon', ['code' => 'MIN500']);

    $response->assertStatus(422);
    expect($response->json('errors.coupon_code.0'))->toContain('minimum order');
});

test('expired coupon is rejected', function () {
    PromoCode::factory()->expired()->create(['code' => 'OLD10']);

    $response = $this->withHeader('X-Cart-Token', $this->guestToken)
        ->postJson('/cart/coupon', ['code' => 'OLD10']);

    $response->assertStatus(422);
    expect($response->json('errors.coupon_code.0'))->toContain('expired');
});

test('inactive coupon is rejected', function () {
    PromoCode::factory()->inactive()->create(['code' => 'OFF10']);

    $response = $this->withHeader('X-Cart-Token', $this->guestToken)
        ->postJson('/cart/coupon', ['code' => 'OFF10']);

    $response->assertStatus(422);
});

test('coupon can be removed from the cart', function () {
    PromoCode::factory()->create(['code' => 'SAVE10']);

    $this->withHeader('X-Cart-Token', $this->guestToken)
        ->postJson('/cart/coupon', ['code' => 'SAVE10'])
        ->assertStatus(200);

    $response = $this->withHeader('X-Cart-Token', $this->guestToken)
        ->deleteJson('/cart/coupon');

    $response->assertStatus(200);
    expect($response->json('coupon'))->toBeNull();
    expect((float) $response->json('discount'))->toEqual(0.0);
    expect(promoGuestCart()->coupon_code)->toBeNull();
});

test('stale coupon is detached from the cart when it becomes invalid', function () {
    $promoCode = PromoCode::factory()->create(['code' => 'SAVE10']);

    $this->withHeader('X-Cart-Token', $this->guestToken)
        ->postJson('/cart/coupon', ['code' => 'SAVE10'])
        ->assertStatus(200);

    $promoCode->update(['active' => false]);

    $response = $this->withHeader('X-Cart-Token', $this->guestToken)
        ->getJson('/cart/fetch');

    $response->assertStatus(200);
    expect($response->json('coupon'))->toBeNull();
    expect((float) $response->json('discount'))->toEqual(0.0);
    expect(promoGuestCart()->coupon_code)->toBeNull();
});

// ── Order placement ──

test('order placement recomputes the subtotal server-side and applies the coupon', function () {
    PromoCode::factory()->create([
        'code' => 'SAVE10',
        'discount_type' => DiscountType::Percentage,
        'discount_value' => 10,
    ]);

    $response = $this->post('/site-order-store', [
        'name' => 'John Doe',
        'phone' => '01712345678',
        'coupon_code' => 'SAVE10',
        'items' => [
            // deliberately wrong client price — server must use product price 100.00
            ['item' => ['id' => $this->product->id, 'price' => 1], 'quantity' => 2],
        ],
    ]);

    $response->assertStatus(201);

    $order = Order::findOrFail($response->json('order_id'));

    expect($order->coupon_code)->toBe('SAVE10');
    expect((float) $order->subtotal)->toEqual(200.00);
    expect((float) $order->discount)->toEqual(20.00);
    expect((float) $order->total)->toEqual(180.00);
    expect((float) $order->due)->toEqual(180.00);
});

test('order placement with an invalid coupon is rejected', function () {
    PromoCode::factory()->expired()->create(['code' => 'OLD10']);

    $response = $this->postJson('/site-order-store', [
        'name' => 'John Doe',
        'phone' => '01712345678',
        'coupon_code' => 'OLD10',
        'items' => [
            ['item' => ['id' => $this->product->id, 'price' => 100], 'quantity' => 1],
        ],
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.coupon_code'))->not->toBeNull();
    expect(Order::count())->toBe(0);
});

test('scheduled coupon is rejected at checkout', function () {
    PromoCode::factory()->scheduled()->create(['code' => 'SOON10']);

    $response = $this->postJson('/site-order-store', [
        'name' => 'John Doe',
        'phone' => '01712345678',
        'coupon_code' => 'SOON10',
        'items' => [
            ['item' => ['id' => $this->product->id, 'price' => 100], 'quantity' => 1],
        ],
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.coupon_code.0'))->toContain('not active yet');
    expect(Order::count())->toBe(0);
});

test('coupon with exhausted usage limit is rejected at checkout', function () {
    PromoCode::factory()->usageLimit(1)->create(['code' => 'ONCE10']);

    $orderPayload = [
        'name' => 'John Doe',
        'phone' => '01712345678',
        'coupon_code' => 'ONCE10',
        'items' => [
            ['item' => ['id' => $this->product->id, 'price' => 100], 'quantity' => 1],
        ],
    ];

    $this->post('/site-order-store', $orderPayload)->assertStatus(201);

    $response = $this->postJson('/site-order-store', $orderPayload);

    $response->assertStatus(422);
    expect($response->json('errors.coupon_code.0'))->toContain('usage limit');
    expect(Order::count())->toBe(1);
});

test('free shipping coupon waives the shipping cost', function () {
    DB::table('settings')->updateOrInsert(
        ['group' => 'shipping', 'key' => 'options'],
        [
            'value' => json_encode([['id' => 'standard', 'name' => 'Standard Delivery', 'price' => 60, 'enabled' => true]]),
            'type' => 'repeater',
            'label' => 'Shipping Options',
            'description' => null,
            'is_public' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );
    Cache::forget('settings');

    // control order: shipping charged below the free threshold
    $this->post('/site-order-store', [
        'name' => 'John Doe',
        'phone' => '01712345678',
        'shipping_method' => 'Standard Delivery',
        'items' => [
            ['item' => ['id' => $this->product->id, 'price' => 100], 'quantity' => 1],
        ],
    ])->assertStatus(201);

    expect((float) Order::latest('id')->first()->shipping)->toEqual(60.00);

    PromoCode::factory()->freeShipping()->create(['code' => 'FREESHIP']);

    $this->post('/site-order-store', [
        'name' => 'John Doe',
        'phone' => '01712345678',
        'shipping_method' => 'Standard Delivery',
        'coupon_code' => 'FREESHIP',
        'items' => [
            ['item' => ['id' => $this->product->id, 'price' => 100], 'quantity' => 1],
        ],
    ])->assertStatus(201);

    $order = Order::latest('id')->first();
    expect($order->coupon_code)->toBe('FREESHIP');
    expect((float) $order->shipping)->toEqual(0.00);
    expect((float) $order->discount)->toEqual(0.00);
    expect((float) $order->total)->toEqual(100.00);
});

// ── Admin CRUD ──

function promoActingAsRoot(): void
{
    $user = User::factory()->create();
    Role::create(['name' => 'root']);
    $user->assignRole('root');
    test()->actingAs($user);
}

test('promo code list can be rendered', function () {
    promoActingAsRoot();

    $promoCode = PromoCode::factory()->create();

    $response = $this->get('/admin/promo-codes');

    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('PromoCode/PromoCodeIndex')
            ->has('promoCodes.data', 1, fn (Assert $page) => $page
                ->where('code', $promoCode->code)
                ->where('status', 'active')
                ->etc())
    );
});

test('promo code can be created', function () {
    promoActingAsRoot();

    $response = $this->post('/admin/promo-codes', [
        'code' => 'WELCOME20',
        'discount_type' => DiscountType::Percentage->value,
        'discount_value' => 20,
        'minimum_order_amount' => 500,
        'maximum_discount_amount' => 100,
        'usage_limit' => 100,
        'per_customer_limit' => 1,
        'active' => true,
    ]);

    $response->assertRedirect('/admin/promo-codes');
    $this->assertDatabaseHas('promo_codes', [
        'code' => 'WELCOME20',
        'discount_value' => 20,
        'minimum_order_amount' => 500,
        'active' => 1,
    ]);
});

test('promo code creation validates the code', function () {
    promoActingAsRoot();

    PromoCode::factory()->create(['code' => 'TAKEN']);

    $response = $this->post('/admin/promo-codes', [
        'code' => 'taken',
        'discount_type' => DiscountType::Percentage->value,
        'discount_value' => 150,
    ]);

    $response->assertInvalid(['code', 'discount_value']);
});

test('free shipping promo code does not require a discount value', function () {
    promoActingAsRoot();

    $response = $this->post('/admin/promo-codes', [
        'code' => 'FREESHIP',
        'discount_type' => DiscountType::FreeShipping->value,
        'active' => true,
    ]);

    $response->assertRedirect('/admin/promo-codes');
    $this->assertDatabaseHas('promo_codes', [
        'code' => 'FREESHIP',
        'discount_type' => DiscountType::FreeShipping->value,
    ]);
});

test('promo code can be created with only an expiry date', function () {
    promoActingAsRoot();

    $response = $this->post('/admin/promo-codes', [
        'code' => 'EXPIRY10',
        'discount_type' => DiscountType::Percentage->value,
        'discount_value' => 10,
        'starts_at' => null,
        'expires_at' => now()->addDays(7)->format('Y-m-d\TH:i'),
    ]);

    $response->assertRedirect('/admin/promo-codes');
    $this->assertDatabaseHas('promo_codes', [
        'code' => 'EXPIRY10',
        'starts_at' => null,
    ]);
});

test('promo code expiry cannot be before its start date', function () {
    promoActingAsRoot();

    $response = $this->post('/admin/promo-codes', [
        'code' => 'BADRANGE',
        'discount_type' => DiscountType::Percentage->value,
        'discount_value' => 10,
        'starts_at' => now()->addDays(5)->format('Y-m-d\TH:i'),
        'expires_at' => now()->addDay()->format('Y-m-d\TH:i'),
    ]);

    $response->assertInvalid(['expires_at']);
});

test('promo code can be updated', function () {
    promoActingAsRoot();

    $promoCode = PromoCode::factory()->create();

    $response = $this->put('/admin/promo-codes/'.$promoCode->id, [
        'code' => $promoCode->code,
        'discount_type' => DiscountType::FixedAmount->value,
        'discount_value' => 250,
        'active' => false,
    ]);

    $response->assertRedirect('/admin/promo-codes');
    $this->assertDatabaseHas('promo_codes', [
        'id' => $promoCode->id,
        'discount_type' => DiscountType::FixedAmount->value,
        'discount_value' => 250,
        'active' => 0,
    ]);
});

test('promo code can be deleted, restored and force deleted', function () {
    promoActingAsRoot();

    $promoCode = PromoCode::factory()->create();

    // soft delete
    $this->delete('/admin/promo-codes/'.$promoCode->id)
        ->assertRedirect('/admin/promo-codes');
    expect(PromoCode::onlyTrashed()->count())->toBe(1);
    expect(PromoCode::count())->toBe(0);

    // recycle bin renders
    $this->get('/admin/promo-codes/recycle-bin')
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page->component('PromoCode/PromoCodeRecycleBin'));

    // restore
    $this->get('/admin/promo-codes/recycle-bin/'.$promoCode->id.'/restore')
        ->assertRedirect(route('promoCode.recycleBin.index'));
    expect(PromoCode::find($promoCode->id))->not->toBeNull();

    // soft delete again, then force delete
    $this->delete('/admin/promo-codes/'.$promoCode->id);
    $this->delete('/admin/promo-codes/recycle-bin/'.$promoCode->id.'/destroy')
        ->assertRedirect(route('promoCode.recycleBin.index'));
    expect(PromoCode::onlyTrashed()->count())->toBe(0);
    expect(DB::table('promo_codes')->where('id', $promoCode->id)->exists())->toBeFalse();
});
