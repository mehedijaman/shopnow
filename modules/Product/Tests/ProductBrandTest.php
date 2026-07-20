<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->productBrand = ProductBrand::factory()->create(['parent_id' => null, 'active' => true, 'featured' => false]);
});

test('product brand list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product-brand');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductBrand/ProductBrandIndex')
            ->has(
                'brands.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->productBrand->id)
                    ->where('image_url', $this->productBrand->image_url)
                    ->where('name', Str::limit($this->productBrand->name, 50))
                    ->where('active', true)
                    ->where('featured', false)
                    ->etc()
            )
    );
});

test('product brand create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product-brand/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductBrand/ProductBrandForm')
    );
});

test('product brand can be created', function () {
    $response = $this->loggedRequest->post('/admin/product-brand', [
        'name' => 'Test Brand',
        'active' => true,
        'featured' => false,
    ]);

    $brands = ProductBrand::all();

    $response->assertRedirect('/admin/product-brand');
    $this->assertCount(2, $brands);
    $this->assertEquals('Test Brand', $brands->last()->name);
});

test('product brand edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product-brand/'.$this->productBrand->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductBrand/ProductBrandForm')
            ->has(
                'brand',
                fn (Assert $page) => $page
                    ->where('id', $this->productBrand->id)
                    ->where('name', $this->productBrand->name)
                    ->where('description', $this->productBrand->description)
                    ->where('image', $this->productBrand->image)
                    ->where('image_url', $this->productBrand->image_url)
                    ->where('active', true)
                    ->where('featured', false)
                    ->where('slug', $this->productBrand->slug)
                    ->where('meta_tag_title', $this->productBrand->meta_tag_title)
                    ->where('meta_tag_description', $this->productBrand->meta_tag_description)
                    ->etc()
            )
    );
});

test('product brand can be updated', function () {
    $response = $this->loggedRequest->put('/admin/product-brand/'.$this->productBrand->id, [
        'name' => 'Updated Brand',
        'active' => true,
        'featured' => false,
    ]);

    $response->assertRedirect('/admin/product-brand');

    $redirectResponse = $this->loggedRequest->get('/admin/product-brand');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductBrand/ProductBrandIndex')
            ->has(
                'brands.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->productBrand->id)
                    ->where('name', 'Updated Brand')
                    ->where('image_url', $this->productBrand->image_url)
                    ->where('active', true)
                    ->where('featured', false)
                    ->etc()
            )
    );
});

test('product brand can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/product-brand/'.$this->productBrand->id);

    $response->assertRedirect('/admin/product-brand');

    $this->assertCount(0, ProductBrand::all());
});

test('product brand cannot be deleted if it has products', function () {
    Product::factory()->create([
        'brand_id' => $this->productBrand->id,
    ]);

    $response = $this->loggedRequest->delete('/admin/product-brand/'.$this->productBrand->id);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Cannot delete brand that has products.');

    $this->assertCount(1, ProductBrand::all());
});

test('product brand recycle bin can be rendered', function () {
    $this->loggedRequest->delete('/admin/product-brand/'.$this->productBrand->id);

    $response = $this->loggedRequest->get('/admin/product-brand/recycle-bin');

    $response->assertStatus(200);
});

test('product brand can be restored from recycle bin', function () {
    $this->loggedRequest->delete('/admin/product-brand/'.$this->productBrand->id);

    $this->assertCount(0, ProductBrand::all());

    $response = $this->loggedRequest->get('/admin/product-brand/recycle-bin/'.$this->productBrand->id.'/restore');

    $response->assertRedirect('/admin/product-brand/recycle-bin');

    $this->assertCount(1, ProductBrand::all());
});
