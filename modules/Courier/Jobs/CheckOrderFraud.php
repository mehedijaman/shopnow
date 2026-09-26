<?php

namespace Modules\Courier\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Courier\Services\CourierConfigHydrator;
use Modules\Courier\Services\FraudChecker;
use Modules\Order\Models\Order;

/**
 * Runs the courier fraud check for a COD order and stores the risk verdict
 * on the order. Booking for high-risk orders requires an explicit override.
 */
class CheckOrderFraud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    /** @var array<int, int> */
    public array $backoff = [60];

    public function __construct(private int $orderId) {}

    public function handle(CourierConfigHydrator $hydrator, FraudChecker $checker): void
    {
        $hydrator->hydrate();

        if (! setting('courier.fraud_enabled')) {
            return;
        }

        $order = Order::find($this->orderId);

        if ($order === null || ! $order->requires_shipping) {
            return;
        }

        $phone = preg_replace('/[^0-9]/', '', (string) $order->phone);

        if (! preg_match('/^01[3-9][0-9]{8}$/', $phone)) {
            return;
        }

        $payload = $checker->check($phone);

        if (FraudChecker::answeredPortals($payload) === 0) {
            Log::info('Fraud check skipped: no courier portal answered.', [
                'order_id' => $order->id,
            ]);

            return;
        }

        $aggregate = $payload['aggregate'];

        $risk = 'low';

        if ($aggregate['total_deliveries'] >= (int) setting('courier.fraud_min_deliveries', 5)
            && $aggregate['cancel_ratio'] >= (float) setting('courier.fraud_cancel_ratio_threshold', 40)) {
            $risk = 'high';
        }

        $order->update([
            'fraud_checked_at' => now(),
            'fraud_risk' => $risk,
            'fraud_details' => $payload,
        ]);
    }
}
