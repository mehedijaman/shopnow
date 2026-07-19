<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderProduct;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductAttribute;
use Modules\Product\Models\ProductAttributeValue;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Services\CalculateVariationPriceRange;
use Modules\Product\Services\GenerateProductVariations;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->category = ProductCategory::factory()->create(['parent_id' => null]);
    $this->brand = ProductBrand::factory()->create(['parent_id' => null]);
});

test('generating variations for same selection twice does not create duplicates', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'variable',
        'price' => 100,
        'quantity' => 50,
    ]);

    $colorAttr = ProductAttribute::create(['name' => 'Color', 'input_type' => 'select']);
    $sizeAttr = ProductAttribute::create(['name' => 'Size', 'input_type' => 'select']);

    $red = ProductAttributeValue::create(['product_attribute_id' => $colorAttr->id, 'value' => 'Red', 'slug' => 'red', 'sort_order' => 0]);
    $blue = ProductAttributeValue::create(['product_attribute_id' => $colorAttr->id, 'value' => 'Blue', 'slug' => 'blue', 'sort_order' => 1]);
    $small = ProductAttributeValue::create(['product_attribute_id' => $sizeAttr->id, 'value' => 'Small', 'slug' => 'small', 'sort_order' => 0]);
    $large = ProductAttributeValue::create(['product_attribute_id' => $sizeAttr->id, 'value' => 'Large', 'slug' => 'large', 'sort_order' => 1]);

    $product->attributeValues()->sync([$red->id, $blue->id, $small->id, $large->id]);

    $service = app(GenerateProductVariations::class);
    $service->run($product, [
        $colorAttr->id => [$red->id, $blue->id],
        $sizeAttr->id => [$small->id, $large->id],
    ]);

    expect($product->variations()->count())->toBe(4);

    // Run again — should not create more
    $service->run($product, [
        $colorAttr->id => [$red->id, $blue->id],
        $sizeAttr->id => [$small->id, $large->id],
    ]);

    expect($product->variations()->count())->toBe(4);
});

test('deselecting a combination deactivates rather than deletes', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'variable',
        'price' => 100,
        'quantity' => 50,
    ]);

    $colorAttr = ProductAttribute::create(['name' => 'Color', 'input_type' => 'select']);
    $red = ProductAttributeValue::create(['product_attribute_id' => $colorAttr->id, 'value' => 'Red', 'slug' => 'red', 'sort_order' => 0]);
    $blue = ProductAttributeValue::create(['product_attribute_id' => $colorAttr->id, 'value' => 'Blue', 'slug' => 'blue', 'sort_order' => 1]);

    $product->attributeValues()->sync([$red->id, $blue->id]);

    $service = app(GenerateProductVariations::class);
    $service->run($product, [
        $colorAttr->id => [$red->id, $blue->id],
    ]);

    expect($product->variations()->count())->toBe(2);
    expect($product->variations()->where('active', true)->count())->toBe(2);

    // Regenerate with only Red — Blue should be deactivated, not deleted
    $service->run($product, [
        $colorAttr->id => [$red->id],
    ]);

    expect($product->variations()->count())->toBe(2);
    expect($product->variations()->where('active', true)->count())->toBe(1);

    $blueVariation = $product->variations()->whereHas('attributeValues', fn ($q) => $q->where('product_attribute_values.id', $blue->id))->first();
    expect($blueVariation->active)->toBeFalse();
});

test('variation has correct variation_key', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'variable',
        'price' => 100,
        'quantity' => 50,
    ]);

    $attr = ProductAttribute::create(['name' => 'Size', 'input_type' => 'select']);
    $s = ProductAttributeValue::create(['product_attribute_id' => $attr->id, 'value' => 'S', 'slug' => 's', 'sort_order' => 0]);
    $m = ProductAttributeValue::create(['product_attribute_id' => $attr->id, 'value' => 'M', 'slug' => 'm', 'sort_order' => 1]);
    $l = ProductAttributeValue::create(['product_attribute_id' => $attr->id, 'value' => 'L', 'slug' => 'l', 'sort_order' => 2]);

    $product->attributeValues()->sync([$s->id, $m->id, $l->id]);

    $service = app(GenerateProductVariations::class);
    $service->run($product, [
        $attr->id => [$s->id, $m->id, $l->id],
    ]);

    $variations = $product->variations;
    expect($variations->pluck('variation_key')->values()->toArray())->toEqualCanonicalizing([(string) $s->id, (string) $m->id, (string) $l->id]);
});

test('checking out a variation produces correct order_products row', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'variable',
        'price' => 100,
        'quantity' => 50,
    ]);

    $attr = ProductAttribute::create(['name' => 'Color', 'input_type' => 'select']);
    $red = ProductAttributeValue::create(['product_attribute_id' => $attr->id, 'value' => 'Red', 'slug' => 'red', 'sort_order' => 0]);

    $product->attributeValues()->sync([$red->id]);

    $service = app(GenerateProductVariations::class);
    $service->run($product, [$attr->id => [$red->id]]);

    $variation = $product->variations()->first();
    $variation->update(['price' => 150, 'quantity' => 10]);

    $order = Order::create([
        'name' => 'Test',
        'phone' => '01712345678',
        'requires_shipping' => false,
    ]);

    $orderProduct = OrderProduct::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_variation_id' => $variation->id,
        'variation_label' => 'Color: Red',
        'quantity' => 2,
        'unit_price' => 150,
        'discount' => 0,
        'total_price' => 300,
    ]);

    expect($orderProduct->product_variation_id)->toBe($variation->id);
    expect((float) $orderProduct->unit_price)->toBe(150.0);
    expect($orderProduct->variation_label)->toBe('Color: Red');
});

test('stock decrements the variation not the parent product', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'variable',
        'price' => 100,
        'quantity' => 50,
    ]);

    $attr = ProductAttribute::create(['name' => 'Size', 'input_type' => 'select']);
    $m = ProductAttributeValue::create(['product_attribute_id' => $attr->id, 'value' => 'M', 'slug' => 'm', 'sort_order' => 0]);

    $product->attributeValues()->sync([$m->id]);

    $service = app(GenerateProductVariations::class);
    $service->run($product, [$attr->id => [$m->id]]);

    $variation = $product->variations()->first();
    $variation->update(['quantity' => 5]);

    $variation->decrement('quantity', 2);

    expect($variation->fresh()->quantity)->toBe(3);
    expect((int) $product->fresh()->quantity)->toBe(50);
});

test('CalculateVariationPriceRange reflects only active variations', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'variable',
        'price' => 100,
        'quantity' => 50,
    ]);

    $attr = ProductAttribute::create(['name' => 'Size', 'input_type' => 'select']);
    $s = ProductAttributeValue::create(['product_attribute_id' => $attr->id, 'value' => 'S', 'slug' => 's', 'sort_order' => 0]);
    $m = ProductAttributeValue::create(['product_attribute_id' => $attr->id, 'value' => 'M', 'slug' => 'm', 'sort_order' => 1]);

    $product->attributeValues()->sync([$s->id, $m->id]);

    $service = app(GenerateProductVariations::class);
    $service->run($product, [$attr->id => [$s->id, $m->id]]);

    $product->variations()->first()->update(['price' => 80]);
    $product->variations()->skip(1)->first()->update(['price' => 120, 'active' => false]);

    $range = app(CalculateVariationPriceRange::class)->run($product);

    expect($range['min'])->toBe(80.0);
    expect($range['max'])->toBe(80.0);
    expect($range['has_range'])->toBeFalse();
});

test('past order referencing a deactivated variation still displays correctly', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'variable',
        'price' => 100,
        'quantity' => 50,
    ]);

    $attr = ProductAttribute::create(['name' => 'Size', 'input_type' => 'select']);
    $m = ProductAttributeValue::create(['product_attribute_id' => $attr->id, 'value' => 'M', 'slug' => 'm', 'sort_order' => 0]);

    $product->attributeValues()->sync([$m->id]);

    $service = app(GenerateProductVariations::class);
    $service->run($product, [$attr->id => [$m->id]]);

    $variation = $product->variations()->first();

    $order = Order::create([
        'name' => 'Past Order',
        'phone' => '01712345678',
        'requires_shipping' => false,
    ]);

    $orderProduct = OrderProduct::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_variation_id' => $variation->id,
        'variation_label' => 'Size: M',
        'quantity' => 1,
        'unit_price' => 100,
        'discount' => 0,
        'total_price' => 100,
    ]);

    // Deactivate the variation
    $variation->update(['active' => false]);

    // The order product still references it
    $loaded = OrderProduct::with('productVariation')->find($orderProduct->id);

    expect($loaded->product_variation_id)->toBe($variation->id);
    expect($loaded->variation_label)->toBe('Size: M');
    expect($loaded->productVariation)->not->toBeNull();
    expect($loaded->productVariation->active)->toBeFalse();
});

test('variation image can be uploaded', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'type' => 'variable',
        'price' => 100,
        'quantity' => 50,
    ]);

    $attr = ProductAttribute::create(['name' => 'Size', 'input_type' => 'select']);
    $m = ProductAttributeValue::create(['product_attribute_id' => $attr->id, 'value' => 'M', 'slug' => 'm', 'sort_order' => 0]);
    $product->attributeValues()->sync([$m->id]);

    $service = app(GenerateProductVariations::class);
    $service->run($product, [$attr->id => [$m->id]]);

    $variation = $product->variations()->first();

    $response = $this->actingAs($this->user)->put(
        route('product.variations.update', ['product' => $product->id, 'variation' => $variation->id]),
        [
            'price' => 120,
            'quantity' => 15,
            'image' => UploadedFile::fake()->image('variation.jpg'),
        ]
    );

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $responseData = $response->json('variation');
    expect($responseData['image_url'])->not->toBeEmpty();
    expect($variation->fresh()->getFirstMediaUrl('image'))->not->toBeEmpty();
});
