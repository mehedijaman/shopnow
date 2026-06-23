<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->productCategory = ProductCategory::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'active' => true,
    ]);

    $this->productBrand = ProductBrand::create([
        'name' => 'Test Brand',
        'slug' => 'test-brand',
        'active' => true,
        'featured' => false,
    ]);

    $this->product = Product::create([
        'active' => true,
        'category_id' => $this->productCategory->id,
        'brand_id' => $this->productBrand->id,
        'name' => 'Test Product',
        'slug' => 'test-product',
        'price' => 99.99,
        'quantity' => 10,
    ]);
});

test('shop index page loads', function () {
    $response = $this->get('/shop');

    $response->assertStatus(200);

    $response->assertViewIs('product::shop');
});

test('product detail page loads', function () {
    $response = $this->get('/shop/product/'.$this->product->id.'/'.$this->product->slug);

    $response->assertStatus(200);

    $response->assertViewIs('product::product-show');
});
