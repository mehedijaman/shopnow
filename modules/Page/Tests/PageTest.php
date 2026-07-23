<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Page\Models\Page;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->page = Page::factory()->create([
        'is_system' => false,
    ]);
});

test('page list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/page');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Page/PageIndex')
            ->has(
                'pages.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->page->id)
                    ->where('slug', $this->page->slug)
                    ->where('title', $this->page->title)
                    ->where('image_url', $this->page->image_url)
                    ->where('status', $this->page->status)
                    ->where('is_system', false)
                    ->etc()
            )
    );
});

test('page create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/page/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Page/PageForm')
    );
});

test('page can be created', function () {
    $response = $this->loggedRequest->post('/admin/page', [
        'title' => 'Page Title',
        'content' => 'Page Content',
    ]);

    $pages = Page::all();

    $this->assertCount(2, $pages);
    $response->assertRedirect('/admin/page/'.$pages->last()->id.'/edit');
    $this->assertEquals('Page Title', $pages->last()->title);
});

test('page edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/page/'.$this->page->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Page/PageForm')
            ->has(
                'page',
                fn (Assert $page) => $page
                    ->where('id', $this->page->id)
                    ->where('title', $this->page->title)
                    ->where('slug', $this->page->slug)
                    ->where('content', $this->page->content)
                    ->where('image_url', $this->page->image_url)
                    ->where('meta_tag_title', $this->page->meta_tag_title)
                    ->where('meta_tag_description', $this->page->meta_tag_description)
                    ->where('is_system', false)
                    ->where('published_at', $this->page->published_at->format('Y-m-d\TH:i:s.u\Z'))
                    ->etc()
            )
    );
});

test('page can be updated', function () {
    $response = $this->loggedRequest->put('/admin/page/'.$this->page->id, [
        'title' => 'New Page Title',
        'content' => 'Updated Content',
    ]);

    $response->assertRedirect('/admin/page');

    $redirectResponse = $this->loggedRequest->get('/admin/page');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('Page/PageIndex')
            ->has(
                'pages.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->page->id)
                    ->where('title', 'New Page Title')
                    ->where('slug', $this->page->slug)
                    ->where('image_url', $this->page->image_url)
                    ->where('status', $this->page->status)
                    ->where('is_system', false)
                    ->etc()
            )
    );
});

test('page can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/page/'.$this->page->id);

    $response->assertRedirect('/admin/page');

    $this->assertCount(0, Page::all());
});

test('page recycle bin can be rendered', function () {
    $this->loggedRequest->delete('/admin/page/'.$this->page->id);

    $response = $this->loggedRequest->get('/admin/page/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Page/PageRecycleBin')
            ->has(
                'pages.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->page->id)
                    ->where('title', $this->page->title)
                    ->where('slug', $this->page->slug)
                    ->where('image_url', $this->page->image_url)
                    ->where('status', $this->page->status)
                    ->etc()
            )
    );
});

test('page can be restored', function () {
    $this->loggedRequest->delete('/admin/page/'.$this->page->id);

    $response = $this->loggedRequest->get('/admin/page/recycle-bin/'.$this->page->id.'/restore');

    $response->assertRedirect('/admin/page/recycle-bin');

    $this->assertCount(1, Page::all());
});

test('page can be force deleted', function () {
    $this->loggedRequest->delete('/admin/page/'.$this->page->id);

    $response = $this->loggedRequest->delete('/admin/page/recycle-bin/'.$this->page->id.'/destroy');

    $response->assertRedirect('/admin/page/recycle-bin');

    $this->assertCount(0, Page::withTrashed()->get());
});
