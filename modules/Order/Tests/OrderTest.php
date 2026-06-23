<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Order\Models\Order;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->order = Order::create([
        'name' => 'Test Order',
        'phone' => '01712345678',
    ])->fresh();
});

test('order list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/order');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderIndex')
            ->has(
                'orders.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->order->id)
                    ->where('name', $this->order->name)
                    ->where('status', $this->order->status)
                    ->where('payment_status', $this->order->payment_status)
                    ->where('total', $this->order->total)
                    ->etc()
            )
    );
});

test('order create page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/order/create');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderForm')
    );
});

test('order edit page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/order/'.$this->order->id.'/edit');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderForm')
            ->has(
                'order',
                fn (Assert $page) => $page
                    ->where('id', $this->order->id)
                    ->where('name', $this->order->name)
                    ->etc()
            )
    );
});

test('order can be updated', function () {
    $response = $this->loggedRequest->put('/admin/order/'.$this->order->id, [
        'name' => 'Updated Order',
        'phone' => '01712345678',
    ]);

    $response->assertRedirect('/admin/order');

    $redirectResponse = $this->loggedRequest->get('/admin/order');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderIndex')
            ->has(
                'orders.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->order->id)
                    ->where('name', 'Updated Order')
                    ->etc()
            )
    );
});

test('order can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/order/'.$this->order->id);

    $response->assertRedirect('/admin/order');

    $this->assertCount(0, Order::all());
});

test('order show page can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/order/'.$this->order->id.'/show');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Order/OrderShow')
            ->has(
                'order',
                fn (Assert $page) => $page
                    ->where('id', $this->order->id)
                    ->where('name', $this->order->name)
                    ->etc()
            )
    );
});

test('order status can be updated', function () {
    $response = $this->loggedRequest->from('/admin/order')->patch('/admin/order/'.$this->order->id.'/status', [
        'status' => 'processing',
    ]);

    $response->assertRedirect('/admin/order');

    $this->assertEquals('processing', Order::find($this->order->id)->status);
});
