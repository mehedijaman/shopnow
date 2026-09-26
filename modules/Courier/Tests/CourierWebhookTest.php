<?php

use CourierHub\DTOs\WebhookEvent;
use CourierHub\Enums\CourierStatus;
use CourierHub\Events\CourierWebhookReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Modules\Courier\Listeners\HandleCourierWebhook;
use Modules\Courier\Models\CourierEvent;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderShipment;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Setting::where('group', 'courier')->whereIn('key', [
        'steadfast_enabled', 'steadfast_api_key', 'steadfast_secret_key', 'steadfast_webhook_secret',
    ])->update(['value' => '1']);
    Setting::where('group', 'courier')->where('key', 'steadfast_api_key')->update(['value' => 'test-api-key']);
    Setting::where('group', 'courier')->where('key', 'steadfast_secret_key')->update(['value' => 'test-secret-key']);
    Setting::where('group', 'courier')->where('key', 'steadfast_webhook_secret')->update(['value' => 'top-secret']);
    Cache::forget('settings');

    $this->order = Order::create([
        'name' => 'Test Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Pending,
    ]);

    $this->shipment = OrderShipment::create([
        'order_id' => $this->order->id,
        'shopment_status' => ShipmentStatus::Pending,
        'carrier' => 'steadfast',
        'tracking_number' => 'STF-123',
    ]);

    $this->postWebhook = function (array $payload, ?string $signature) {
        $content = json_encode($payload);

        $server = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];

        if ($signature !== null) {
            $server['HTTP_X_STEADFAST_SIGNATURE'] = $signature;
        }

        return $this->call('POST', '/webhooks/courier/steadfast', [], [], [], $server, $content);
    };
});

afterEach(function () {
    Cache::forget('settings');
});

test('webhook is rejected when the courier is disabled', function () {
    Setting::where('group', 'courier')->where('key', 'steadfast_enabled')->update(['value' => '0']);
    Cache::forget('settings');

    $content = json_encode(['tracking_code' => 'STF-123', 'status' => 'delivered']);
    $signature = hash_hmac('sha256', $content, 'top-secret');

    $response = ($this->postWebhook)(['tracking_code' => 'STF-123', 'status' => 'delivered'], $signature);

    expect($response->getStatusCode())->toBe(400);
});

test('webhook with an invalid signature is rejected and dispatches nothing', function () {
    Event::fake([CourierWebhookReceived::class]);

    $response = ($this->postWebhook)(['tracking_code' => 'STF-123', 'status' => 'delivered'], 'bad-signature');

    expect($response->getStatusCode())->toBe(403);
    Event::assertNotDispatched(CourierWebhookReceived::class);
});

test('webhook with a valid signature dispatches the parsed event', function () {
    Event::fake([CourierWebhookReceived::class]);

    $payload = [
        'invoice' => (string) $this->order->id,
        'tracking_code' => 'STF-123',
        'status' => 'delivered',
    ];
    $signature = hash_hmac('sha256', json_encode($payload), 'top-secret');

    $response = ($this->postWebhook)($payload, $signature);

    expect($response->getStatusCode())->toBe(200);

    Event::assertDispatched(
        CourierWebhookReceived::class,
        fn (CourierWebhookReceived $event) => $event->webhook->tracking_id === 'STF-123'
            && $event->webhook->status === CourierStatus::Delivered
            && $event->webhook->merchant_order_id === (string) $this->order->id,
    );
});

test('listener applies a webhook to the matching shipment', function () {
    $webhook = new WebhookEvent(
        courier_name: 'steadfast',
        tracking_id: 'STF-123',
        status: CourierStatus::Delivered,
        raw_payload: ['status' => 'delivered'],
        timestamp: now()->toIso8601String(),
        merchant_order_id: (string) $this->order->id,
    );

    app(HandleCourierWebhook::class)->handle(new CourierWebhookReceived($webhook));

    $shipment = $this->shipment->fresh();

    expect($shipment->shopment_status)->toBe(ShipmentStatus::Delivered)
        ->and($shipment->courier_status)->toBe(CourierStatus::Delivered->value)
        ->and($this->order->fresh()->status)->toBe(OrderStatus::Delivered);

    expect(CourierEvent::where('order_id', $this->order->id)->count())->toBe(1);
});

test('listener records an orphan event for an unknown tracking number', function () {
    $webhook = new WebhookEvent(
        courier_name: 'steadfast',
        tracking_id: 'UNKNOWN-1',
        status: CourierStatus::InTransit,
        raw_payload: ['status' => 'in_transit'],
        timestamp: now()->toIso8601String(),
    );

    app(HandleCourierWebhook::class)->handle(new CourierWebhookReceived($webhook));

    expect($this->shipment->fresh()->courier_status)->toBeNull()
        ->and(CourierEvent::whereNull('order_id')->where('tracking_id', 'UNKNOWN-1')->count())->toBe(1);
});
