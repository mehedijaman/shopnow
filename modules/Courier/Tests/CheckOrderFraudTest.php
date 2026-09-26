<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Modules\Courier\Jobs\CheckOrderFraud;
use Modules\Courier\Services\FraudChecker;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

class FakeFraudChecker extends FraudChecker
{
    public function __construct(private array $fixture) {}

    public function check(string $phone): array
    {
        return $this->fixture;
    }
}

function fraudPayload(int $deliveries, float $cancelRatio): array
{
    $cancel = (int) round($deliveries * $cancelRatio / 100);

    return [
        'steadfast' => [
            'success' => $deliveries - $cancel,
            'cancel' => $cancel,
            'total' => $deliveries,
        ],
        'pathao' => null,
        'redx' => null,
        'paperfly' => null,
        'carrybee' => null,
        'aggregate' => [
            'total_success' => $deliveries - $cancel,
            'total_cancel' => $cancel,
            'total_deliveries' => $deliveries,
            'success_ratio' => round(100 - $cancelRatio, 2),
            'cancel_ratio' => $cancelRatio,
        ],
    ];
}

beforeEach(function () {
    Setting::where('group', 'courier')->where('key', 'fraud_enabled')->update(['value' => '1']);
    Setting::where('group', 'courier')->where('key', 'fraud_min_deliveries')->update(['value' => '5']);
    Setting::where('group', 'courier')->where('key', 'fraud_cancel_ratio_threshold')->update(['value' => '40']);
    Cache::forget('settings');

    $this->order = Order::create([
        'name' => 'Test Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Pending,
        'payment_method' => 'cod',
        'requires_shipping' => true,
    ]);

    $category = ProductCategory::create([
        'name' => 'Fraud Test Category',
        'slug' => 'fraud-test-category',
    ]);

    $this->physicalProduct = Product::create([
        'category_id' => $category->id,
        'name' => 'Physical Product',
        'slug' => 'fraud-physical-product',
        'price' => 99.99,
        'quantity' => '10',
        'active' => true,
        'is_virtual' => false,
        'is_downloadable' => false,
    ]);

    $this->runFraudJob = function (array $payload): void {
        app()->bind(FraudChecker::class, fn () => new FakeFraudChecker($payload));

        CheckOrderFraud::dispatchSync($this->order->id);
    };
});

afterEach(function () {
    Cache::forget('settings');
});

test('flags high risk when deliveries and cancel ratio meet the thresholds', function () {
    ($this->runFraudJob)(fraudPayload(10, 50));

    $order = $this->order->fresh();

    expect($order->fraud_risk)->toBe('high')
        ->and($order->fraud_checked_at)->not->toBeNull()
        ->and($order->fraud_details['aggregate']['total_deliveries'])->toBe(10);
});

test('flags low risk when the cancel ratio is below the threshold', function () {
    ($this->runFraudJob)(fraudPayload(10, 20));

    expect($this->order->fresh()->fraud_risk)->toBe('low');
});

test('flags low risk below the minimum delivery count', function () {
    ($this->runFraudJob)(fraudPayload(3, 100));

    expect($this->order->fresh()->fraud_risk)->toBe('low');
});

test('flags high risk when couriers report fraud against the phone', function () {
    $payload = fraudPayload(10, 10);
    $payload['steadfast']['total_reports'] = 2;
    $payload['steadfast']['fraud_categories'] = ['cod_abuse'];

    ($this->runFraudJob)($payload);

    expect($this->order->fresh()->fraud_risk)->toBe('high');
});

test('skips the check when fraud checks are disabled', function () {
    Setting::where('group', 'courier')->where('key', 'fraud_enabled')->update(['value' => '0']);
    Cache::forget('settings');

    ($this->runFraudJob)(fraudPayload(10, 50));

    expect($this->order->fresh()->fraud_checked_at)->toBeNull();
});

test('skips the check when no portal answered', function () {
    ($this->runFraudJob)([
        'steadfast' => ['error' => 'Not configured'],
        'pathao' => ['error' => 'Not configured'],
        'redx' => ['error' => 'Not configured'],
        'paperfly' => ['error' => 'Not configured'],
        'carrybee' => ['error' => 'Not configured'],
        'aggregate' => [
            'total_success' => 0,
            'total_cancel' => 0,
            'total_deliveries' => 0,
            'success_ratio' => 0,
            'cancel_ratio' => 0,
        ],
    ]);

    expect($this->order->fresh()->fraud_checked_at)->toBeNull();
});

test('skips the check for phone numbers the couriers cannot look up', function () {
    $this->order->update(['phone' => '12345']);

    ($this->runFraudJob)(fraudPayload(10, 50));

    expect($this->order->fresh()->fraud_checked_at)->toBeNull();
});

test('placing a cod order with fraud checks enabled queues the check', function () {
    Queue::fake();

    $response = $this->post('/site-order-store', [
        'name' => 'Cod Buyer',
        'phone' => '01712345678',
        'items' => [
            [
                'item' => ['id' => $this->physicalProduct->id, 'price' => 99.99],
                'quantity' => 1,
            ],
        ],
        'payment_method' => 'cod',
    ]);

    $response->assertStatus(201);

    Queue::assertPushed(CheckOrderFraud::class, 1);
});

test('placing an order with fraud checks disabled does not queue the check', function () {
    Setting::where('group', 'courier')->where('key', 'fraud_enabled')->update(['value' => '0']);
    Cache::forget('settings');

    Queue::fake();

    $response = $this->post('/site-order-store', [
        'name' => 'Cod Buyer',
        'phone' => '01712345678',
        'items' => [
            [
                'item' => ['id' => $this->physicalProduct->id, 'price' => 99.99],
                'quantity' => 1,
            ],
        ],
        'payment_method' => 'cod',
    ]);

    $response->assertStatus(201);

    Queue::assertNotPushed(CheckOrderFraud::class);
});
