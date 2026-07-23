<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Slider\Models\Slider;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->slider = Slider::factory()->create(['active' => true]);
});

test('slider list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/slider');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Slider/SliderIndex')
            ->has(
                'sliders.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->slider->id)
                    ->where('title', $this->slider->title)
                    ->where('image_url', $this->slider->image_url)
                    ->where('bg_color', $this->slider->bg_color)
                    ->where('url', $this->slider->url)
                    ->where('button_text', $this->slider->button_text)
                    ->where('order', $this->slider->order)
                    ->where('active', (int) $this->slider->active)
                    ->etc()
            )
    );
});

test('slider create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/slider/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Slider/SliderForm')
    );
});

test('slider can be created', function () {
    $response = $this->loggedRequest->post('/admin/slider', [
        'title' => 'Slider Title',
        'description' => 'Slider Description',
        'url' => 'https://example.com',
        'button_text' => 'Click Here',
        'order' => 1,
        'active' => true,
    ]);

    $sliders = Slider::all();

    $response->assertRedirect('/admin/slider');
    $this->assertCount(2, $sliders);
    $this->assertEquals('Slider Title', $sliders->last()->title);
});

test('slider edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/slider/'.$this->slider->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Slider/SliderForm')
            ->has(
                'slider',
                fn (Assert $page) => $page
                    ->where('id', $this->slider->id)
                    ->where('title', $this->slider->title)
                    ->where('description', $this->slider->description)
                    ->where('image_url', $this->slider->image_url)
                    ->where('bg_color', $this->slider->bg_color)
                    ->where('url', $this->slider->url)
                    ->where('button_text', $this->slider->button_text)
                    ->where('order', $this->slider->order)
                    ->where('active', (int) $this->slider->active)
                    ->etc()
            )
    );
});

test('slider can be updated', function () {
    $response = $this->loggedRequest->put('/admin/slider/'.$this->slider->id, [
        'title' => 'New Title',
        'active' => true,
    ]);

    $response->assertRedirect('/admin/slider');

    $redirectResponse = $this->loggedRequest->get('/admin/slider');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('Slider/SliderIndex')
            ->has(
                'sliders.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->slider->id)
                    ->where('title', 'New Title')
                    ->where('image_url', $this->slider->image_url)
                    ->where('active', (int) $this->slider->active)
                    ->etc()
            )
    );
});

test('slider can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/slider/'.$this->slider->id);

    $response->assertRedirect('/admin/slider');

    $this->assertCount(0, Slider::all());
});

test('slider recycle bin can be rendered', function () {
    $this->loggedRequest->delete('/admin/slider/'.$this->slider->id);

    $response = $this->loggedRequest->get('/admin/slider/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Slider/SliderRecycleBin')
            ->has(
                'sliders.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->slider->id)
                    ->where('title', $this->slider->title)
                    ->where('active', (int) $this->slider->active)
                    ->etc()
            )
    );
});

test('slider can be restored', function () {
    $this->loggedRequest->delete('/admin/slider/'.$this->slider->id);

    $response = $this->loggedRequest->get('/admin/slider/recycle-bin/'.$this->slider->id.'/restore');

    $response->assertRedirect('/admin/slider/recycle-bin');

    $this->assertCount(1, Slider::all());
});
