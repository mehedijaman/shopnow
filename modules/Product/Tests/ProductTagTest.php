<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Product\Models\Product;
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

    $this->productTag = ProductTag::factory()->create();
});

test('product tag list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product-tag');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductTag/ProductTagIndex')
            ->has(
                'tags.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->productTag->id)
                    ->where('name', Str::limit($this->productTag->name, 50))
                    ->etc()
            )
    );
});

test('product tag create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product-tag/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductTag/ProductTagForm')
    );
});

test('product tag can be created', function () {
    $response = $this->loggedRequest->post('/admin/product-tag', [
        'name' => 'Test Tag',
    ]);

    $tags = ProductTag::all();

    $response->assertRedirect('/admin/product-tag');
    $this->assertCount(2, $tags);
    $this->assertEquals('Test Tag', $tags->last()->name);
});

test('product tag edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/product-tag/'.$this->productTag->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductTag/ProductTagForm')
            ->has(
                'tag',
                fn (Assert $page) => $page
                    ->where('id', $this->productTag->id)
                    ->where('name', $this->productTag->name)
                    ->etc()
            )
    );
});

test('product tag can be updated', function () {
    $response = $this->loggedRequest->put('/admin/product-tag/'.$this->productTag->id, [
        'name' => 'Updated Tag',
    ]);

    $response->assertRedirect('/admin/product-tag');

    $redirectResponse = $this->loggedRequest->get('/admin/product-tag');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('ProductTag/ProductTagIndex')
            ->has(
                'tags.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->productTag->id)
                    ->where('name', 'Updated Tag')
                    ->etc()
            )
    );
});

test('product tag can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/product-tag/'.$this->productTag->id);

    $response->assertRedirect('/admin/product-tag');

    $this->assertCount(0, ProductTag::all());
});

test('product tag cannot be deleted if it has products', function () {
    $product = Product::factory()->create();
    $product->tags()->attach($this->productTag->id);

    $response = $this->loggedRequest->delete('/admin/product-tag/'.$this->productTag->id);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Cannot delete tag that has products.');

    $this->assertCount(1, ProductTag::all());
});

test('product tag recycle bin can be rendered', function () {
    $this->loggedRequest->delete('/admin/product-tag/'.$this->productTag->id);

    $response = $this->loggedRequest->get('/admin/product-tag/recycle-bin');

    $response->assertStatus(200);
});

test('product tag can be restored from recycle bin', function () {
    $this->loggedRequest->delete('/admin/product-tag/'.$this->productTag->id);

    $this->assertCount(0, ProductTag::all());

    $response = $this->loggedRequest->get('/admin/product-tag/recycle-bin/'.$this->productTag->id.'/restore');

    $response->assertRedirect('/admin/product-tag/recycle-bin');

    $this->assertCount(1, ProductTag::all());
});
