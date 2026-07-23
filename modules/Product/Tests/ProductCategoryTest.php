<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Product\Models\Product;
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

    $this->productCategory = ProductCategory::factory()->create(['parent_id' => null, 'active' => true, 'featured' => false])->fresh();
});

test('product category list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product-category');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductCategory/ProductCategoryIndex')
            ->has(
                'categories.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->productCategory->id)
                    ->where('image_url', $this->productCategory->image_url)
                    ->where('name', Str::limit($this->productCategory->name, 50))
                    ->where('active', true)
                    ->where('featured', false)
                    ->where('sort_order', $this->productCategory->sort_order)
                    ->etc()
            )
    );
});

test('product category create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product-category/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductCategory/ProductCategoryForm')
    );
});

test('product category can be created', function () {
    $response = $this->loggedRequest->post('/admin/product-category', [
        'name' => 'Test Category',
        'active' => true,
        'featured' => false,
    ]);

    $categories = ProductCategory::all();

    $response->assertRedirect('/admin/product-category');
    $this->assertCount(2, $categories);
    $this->assertEquals('Test Category', $categories->last()->name);
});

test('product category edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product-category/'.$this->productCategory->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductCategory/ProductCategoryForm')
            ->has(
                'category',
                fn (Assert $page) => $page
                    ->where('id', $this->productCategory->id)
                    ->where('name', $this->productCategory->name)
                    ->where('description', $this->productCategory->description)
                    ->where('image_url', $this->productCategory->image_url)
                    ->where('active', true)
                    ->where('featured', false)
                    ->where('slug', $this->productCategory->slug)
                    ->where('meta_tag_title', $this->productCategory->meta_tag_title)
                    ->where('meta_tag_description', $this->productCategory->meta_tag_description)
                    ->etc()
            )
    );
});

test('product category can be updated', function () {
    $response = $this->loggedRequest->put('/admin/product-category/'.$this->productCategory->id, [
        'name' => 'Updated Category',
        'active' => true,
        'featured' => false,
    ]);

    $response->assertRedirect('/admin/product-category');

    $redirectResponse = $this->loggedRequest->get('/admin/product-category');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductCategory/ProductCategoryIndex')
            ->has(
                'categories.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->productCategory->id)
                    ->where('name', 'Updated Category')
                    ->where('image_url', $this->productCategory->image_url)
                    ->where('active', true)
                    ->where('featured', false)
                    ->etc()
            )
    );
});

test('product category can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/product-category/'.$this->productCategory->id);

    $response->assertRedirect('/admin/product-category');

    $this->assertCount(0, ProductCategory::all());
});

test('product category cannot be deleted if it has products', function () {
    Product::factory()->create([
        'category_id' => $this->productCategory->id,
    ]);

    $response = $this->loggedRequest->delete('/admin/product-category/'.$this->productCategory->id);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Cannot delete category that has products.');

    $this->assertCount(1, ProductCategory::all());
});

test('product category recycle bin can be rendered', function () {
    $this->loggedRequest->delete('/admin/product-category/'.$this->productCategory->id);

    $response = $this->loggedRequest->get('/admin/product-category/recycle-bin');

    $response->assertStatus(200);
});

test('product category can be restored from recycle bin', function () {
    $this->loggedRequest->delete('/admin/product-category/'.$this->productCategory->id);

    $this->assertCount(0, ProductCategory::all());

    $response = $this->loggedRequest->get('/admin/product-category/recycle-bin/'.$this->productCategory->id.'/restore');

    $response->assertRedirect('/admin/product-category/recycle-bin');

    $this->assertCount(1, ProductCategory::all());
});
