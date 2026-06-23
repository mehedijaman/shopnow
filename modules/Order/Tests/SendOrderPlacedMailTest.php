<?php

use App\Jobs\SendOrderPlacedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Modules\Order\Models\Order;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('send order placed mail job can be dispatched', function () {
    $order = Order::create([
        'name' => 'Test Order',
        'phone' => '01712345678',
    ]);

    Queue::fake();

    SendOrderPlacedMail::dispatch($order->id, 'admin@example.com');

    Queue::assertPushed(SendOrderPlacedMail::class, function ($job) use ($order) {
        return $job->orderId === $order->id && $job->adminEmail === 'admin@example.com';
    });
});
