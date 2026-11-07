<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

afterEach(function () {
    Cache::forget('settings');
});

test("the site's index page returns a successful response", function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

function makeFeaturedCategoryWithProduct(): ProductCategory
{
    $category = ProductCategory::factory()->create([
        'name' => 'Electronics',
        'slug' => 'electronics',
        'featured' => true,
        'active' => true,
    ]);

    Product::factory()->create(['category_id' => $category->id]);

    return $category;
}

function setHomepageFlag(string $key, string $value): void
{
    Setting::updateOrCreate(
        ['group' => 'homepage', 'key' => $key],
        ['value' => $value, 'type' => 'boolean', 'label' => $key, 'is_public' => false, 'sort_order' => 1],
    );

    Cache::forget('settings');
}

test('the homepage renders featured category tiles and product sections by default', function () {
    makeFeaturedCategoryWithProduct();

    $response = $this->get('/');

    $response->assertOk();
    expect($response->getContent())->toMatch('/<h2\b[^>]*>\s*Featured Categories\s*<\/h2>/');
    $response->assertSee('Electronics');
    $response->assertSee('View All');
    // Image-less categories fall back to the icon placeholder.
    $response->assertSee('ri-folder-line');
    $response->assertSee('/shop/category/', false);
});

test('featured category tiles can be disabled independently of product sections', function () {
    makeFeaturedCategoryWithProduct();
    setHomepageFlag('show_featured_categories', '0');

    $response = $this->get('/');

    $response->assertOk();
    expect($response->getContent())->not->toMatch('/<h2\b[^>]*>\s*Featured Categories\s*<\/h2>/');
    $response->assertSee('View All');
});

test('featured category product sections can be disabled independently of tiles', function () {
    makeFeaturedCategoryWithProduct();
    setHomepageFlag('show_featured_category_products', '0');

    $response = $this->get('/');

    $response->assertOk();
    expect($response->getContent())->toMatch('/<h2\b[^>]*>\s*Featured Categories\s*<\/h2>/');
    $response->assertDontSee('View All');
});

test('categories without active products are excluded from the tile grid', function () {
    ProductCategory::factory()->create([
        'name' => 'Empty Category',
        'slug' => 'empty-category',
        'featured' => true,
        'active' => true,
    ]);

    $response = $this->get('/');

    $response->assertOk();
    expect($response->getContent())->not->toMatch('/<h2\b[^>]*>\s*Featured Categories\s*<\/h2>/');
    $response->assertDontSee('View All');
});
