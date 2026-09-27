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
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
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
        'bdcourier' => ['error' => 'Disabled'],
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

test('flags high risk when any single source meets the thresholds', function () {
    $payload = fraudPayload(10, 10);
    $payload['bdcourier'] = [
        'success' => 4,
        'cancel' => 6,
        'total' => 10,
        'success_ratio' => 40.0,
    ];
    $payload['aggregate'] = [
        'total_success' => 13,
        'total_cancel' => 7,
        'total_deliveries' => 20,
        'success_ratio' => 65.0,
        'cancel_ratio' => 35.0,
    ];

    ($this->runFraudJob)($payload);

    expect($this->order->fresh()->fraud_risk)->toBe('high');
});

test('flags high risk when bdcourier returns fraud reports', function () {
    $payload = fraudPayload(10, 10);
    $payload['bdcourier'] = [
        'success' => 9,
        'cancel' => 1,
        'total' => 10,
        'success_ratio' => 90.0,
        'reports' => [
            ['id' => 'abc123', 'name' => 'John Doe', 'details' => 'Fraud reported by merchant'],
        ],
    ];

    ($this->runFraudJob)($payload);

    expect($this->order->fresh()->fraud_risk)->toBe('high');
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

test('skips the check when no fraud source answered', function () {
    ($this->runFraudJob)([
        'steadfast' => ['error' => 'Not configured'],
        'bdcourier' => ['error' => 'Disabled'],
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

test('posting the fraud check endpoint runs the check and returns the verdict', function () {
    $user = User::factory()->create();
    Role::create(['name' => 'root']);
    $user->assignRole('root');
    $this->actingAs($user);

    app()->bind(FraudChecker::class, fn () => new FakeFraudChecker(fraudPayload(10, 50)));

    $this->post(route('order.fraudCheck', $this->order->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($this->order->fresh()->fraud_risk)->toBe('high');
});

test('posting the fraud check endpoint reports when no source answered', function () {
    $user = User::factory()->create();
    Role::create(['name' => 'root']);
    $user->assignRole('root');
    $this->actingAs($user);

    app()->bind(FraudChecker::class, fn () => new FakeFraudChecker([
        'steadfast' => ['error' => 'Not configured'],
        'bdcourier' => ['error' => 'Disabled'],
        'aggregate' => [
            'total_success' => 0,
            'total_cancel' => 0,
            'total_deliveries' => 0,
            'success_ratio' => 0,
            'cancel_ratio' => 0,
        ],
    ]));

    $this->post(route('order.fraudCheck', $this->order->id))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($this->order->fresh()->fraud_checked_at)->toBeNull();
});

test('posting the fraud check endpoint rejects when fraud checks are disabled', function () {
    $user = User::factory()->create();
    Role::create(['name' => 'root']);
    $user->assignRole('root');
    $this->actingAs($user);

    Setting::where('group', 'courier')->where('key', 'fraud_enabled')->update(['value' => '0']);
    Cache::forget('settings');

    $this->post(route('order.fraudCheck', $this->order->id))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($this->order->fresh()->fraud_checked_at)->toBeNull();
});

test('posting the fraud check endpoint rejects orders that do not require shipping', function () {
    $user = User::factory()->create();
    Role::create(['name' => 'root']);
    $user->assignRole('root');
    $this->actingAs($user);

    $digitalOrder = Order::create([
        'name' => 'Digital Buyer',
        'phone' => '01712345678',
        'status' => OrderStatus::Pending,
        'payment_method' => 'cod',
        'requires_shipping' => false,
    ]);

    $this->post(route('order.fraudCheck', $digitalOrder->id))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($digitalOrder->fresh()->fraud_checked_at)->toBeNull();
});
