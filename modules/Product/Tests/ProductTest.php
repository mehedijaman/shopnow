<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductAttribute;
use Modules\Product\Models\ProductAttributeValue;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductTag;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->productCategory = ProductCategory::factory()->create(['parent_id' => null]);
    $this->productBrand = ProductBrand::factory()->create(['parent_id' => null]);
    $this->product = Product::factory()->create(['active' => true, 'featured' => false]);
    $this->product = $this->product->fresh();
    $this->product->load('category:id,name', 'brand:id,name');
});

test('product list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Product/ProductIndex')
            ->has(
                'products.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->product->id)
                    ->where('image_url', $this->product->image_url)
                    ->where('name', $this->product->name)
                    ->where('price', $this->product->price)
                    ->where('sale_price', $this->product->sale_price)
                    ->where('quantity', $this->product->quantity)
                    ->where('unit', $this->product->unit)
                    ->where('category', $this->product->category?->name)
                    ->where('brand', $this->product->brand?->name)
                    ->where('active', true)
                    ->where('featured', false)
                    ->etc()
            )
    );
});

test('product create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Product/ProductForm')
            ->has('categories')
            ->has('tags')
            ->has('brands')
    );
});

test('product can be created', function () {
    $response = $this->loggedRequest->post('/admin/product', [
        'category_id' => null,
        'brand_id' => null,
        'name' => 'Test Product',
        'price' => 99.99,
        'sale_price' => null,
        'quantity' => 10,
        'unit' => 'pcs',
        'active' => true,
        'featured' => false,
    ]);

    $products = Product::all();

    $response->assertRedirect('/admin/product');
    $this->assertCount(2, $products);
    $this->assertEquals('Test Product', $products->last()->name);
});

test('product edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product/'.$this->product->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Product/ProductForm')
            ->has(
                'product',
                fn (Assert $page) => $page
                    ->where('id', $this->product->id)
                    ->where('name', $this->product->name)
                    ->where('slug', $this->product->slug)
                    ->where('price', $this->product->price)
                    ->where('sale_price', $this->product->sale_price)
                    ->where('quantity', $this->product->quantity)
                    ->where('unit', $this->product->unit)
                    ->where('active', true)
                    ->where('featured', false)
                    ->where('image_url', $this->product->image_url)
                    ->where('tags', [])
                    ->etc()
            )
    );
});

test('product can be updated', function () {
    $response = $this->loggedRequest->put('/admin/product/'.$this->product->id, [
        'name' => 'Updated',
        'price' => 49.99,
        'quantity' => 5,
        'active' => true,
        'featured' => false,
    ]);

    $response->assertRedirect('/admin/product/'.$this->product->id.'/edit');

    $redirectResponse = $this->loggedRequest->get('/admin/product');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('Product/ProductIndex')
            ->has(
                'products.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->product->id)
                    ->where('name', 'Updated')
                    ->etc()
            )
    );
});

test('product can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/product/'.$this->product->id);

    $response->assertRedirect('/admin/product');

    $this->assertCount(0, Product::all());
});

test('product recycle bin can be rendered', function () {
    $this->loggedRequest->delete('/admin/product/'.$this->product->id);

    $response = $this->loggedRequest->get('/admin/product/recycle-bin');

    $response->assertStatus(200);
});

test('product can be restored from recycle bin', function () {
    $this->loggedRequest->delete('/admin/product/'.$this->product->id);

    $this->assertCount(0, Product::all());

    $response = $this->loggedRequest->get('/admin/product/recycle-bin/'.$this->product->id.'/restore');

    $response->assertRedirect('/admin/product/recycle-bin');

    $this->assertCount(1, Product::all());
});

test('product list can be filtered by category, tag, and attributes', function () {
    $category = ProductCategory::factory()->create();
    $tag = ProductTag::create(['name' => 'FeaturedTag', 'slug' => 'featured-tag']);

    $attribute = ProductAttribute::create(['name' => 'Color', 'slug' => 'color', 'input_type' => 'select']);
    $attributeValue = ProductAttributeValue::create([
        'product_attribute_id' => $attribute->id,
        'value' => 'Red',
        'slug' => 'red',
    ]);

    $matchingProduct = Product::factory()->create([
        'category_id' => $category->id,
        'active' => true,
    ]);
    $matchingProduct->tags()->attach($tag->id);
    $matchingProduct->attributeValues()->attach($attributeValue->id);

    $nonMatchingProduct = Product::factory()->create([
        'category_id' => null,
        'active' => true,
    ]);

    // Test Category filter
    $response = $this->loggedRequest->get('/admin/product?category='.$category->id);
    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
            ->has('products.data', 1)
            ->where('products.data.0.id', $matchingProduct->id)
    );

    // Test Tag filter
    $response = $this->loggedRequest->get('/admin/product?tag='.$tag->id);
    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
            ->has('products.data', 1)
            ->where('products.data.0.id', $matchingProduct->id)
    );

    // Test Attribute filter
    $response = $this->loggedRequest->get('/admin/product?attribute='.$attribute->id);
    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
            ->has('products.data', 1)
            ->where('products.data.0.id', $matchingProduct->id)
    );

    // Test Attribute Value filter
    $response = $this->loggedRequest->get('/admin/product?attribute_value='.$attributeValue->id);
    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
            ->has('products.data', 1)
            ->where('products.data.0.id', $matchingProduct->id)
    );
});
