<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Blog\Models\Post;
use Modules\Blog\Models\Tag;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->tag = Tag::factory()->create();
});

test('tag list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/blog-tag');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('BlogTag/TagIndex')
            ->has(
                'tags.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->tag->id)
                    ->where('name', $this->tag->name)
                    ->etc()
            )
    );
});

test('tag create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/blog-tag/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('BlogTag/TagForm')
    );
});

test('tag can be created', function () {
    $response = $this->loggedRequest->post('/admin/blog-tag', [
        'name' => 'New Tag',
    ]);

    $tags = Tag::all();

    $response->assertRedirect('/admin/blog-tag');
    $this->assertCount(2, $tags);
    $this->assertEquals('New Tag', $tags->last()->name);
});

test('tag edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/blog-tag/'.$this->tag->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('BlogTag/TagForm')
            ->has(
                'tag',
                fn (Assert $page) => $page
                    ->where('id', $this->tag->id)
                    ->where('name', $this->tag->name)
                    ->etc()
            )
    );
});

test('tag can be updated', function () {
    $response = $this->loggedRequest->put('/admin/blog-tag/'.$this->tag->id, [
        'name' => 'Updated Tag',
    ]);

    $response->assertRedirect('/admin/blog-tag');

    $redirectResponse = $this->loggedRequest->get('/admin/blog-tag');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('BlogTag/TagIndex')
            ->has(
                'tags.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->tag->id)
                    ->where('name', 'Updated Tag')
                    ->etc()
            )
    );
});

test('tag can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/blog-tag/'.$this->tag->id);

    $response->assertRedirect('/admin/blog-tag');

    $this->assertCount(0, Tag::all());
});

test('tag recycle bin can be rendered', function () {
    $this->tag->delete();

    $response = $this->loggedRequest->get('/admin/blog-tag/recycle-bin');

    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('BlogTag/TagRecycleBin')
            ->has(
                'tags.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->tag->id)
                    ->where('name', $this->tag->name)
                    ->etc()
            )
    );
});

test('tag can be restored from recycle bin', function () {
    $this->tag->delete();

    $response = $this->loggedRequest->get('/admin/blog-tag/recycle-bin/'.$this->tag->id.'/restore');

    $response->assertRedirect('/admin/blog-tag/recycle-bin');
    $this->assertCount(1, Tag::all());
});

test('tag can be force deleted from recycle bin', function () {
    $this->tag->delete();

    $response = $this->loggedRequest->delete('/admin/blog-tag/recycle-bin/'.$this->tag->id.'/destroy');

    $response->assertRedirect('/admin/blog-tag/recycle-bin');
    $this->assertCount(0, Tag::withTrashed()->get());
});

test('tag cannot be deleted if it has posts', function () {
    $post = Post::factory()->create();
    $post->tags()->attach($this->tag->id);

    $response = $this->loggedRequest->delete('/admin/blog-tag/'.$this->tag->id);

    $response->assertRedirect('/admin/blog-tag');
    $this->assertCount(1, Tag::all());
});
