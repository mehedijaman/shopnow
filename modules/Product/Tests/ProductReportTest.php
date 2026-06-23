<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
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

    $this->productCategory = ProductCategory::factory()->create(['parent_id' => null]);
    $this->productBrand = ProductBrand::factory()->create(['parent_id' => null]);
});

test('product report can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product/report');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Product/ProductReport')
    );
});

test('product report shows summary data', function () {
    $activeProduct = Product::factory()->create(['active' => true, 'featured' => true]);
    $inactiveProduct = Product::factory()->create(['active' => false, 'featured' => false]);

    $response = $this->loggedRequest->get('/admin/product/report');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Product/ProductReport')
            ->has('summary', fn (Assert $page) => $page
                ->where('totalProducts', 2)
                ->where('activeProducts', 1)
                ->where('inactiveProducts', 1)
                ->where('featuredProducts', 1)
            )
            ->has('topSelling')
            ->has('lowStock')
            ->has('byCategory')
    );
});
