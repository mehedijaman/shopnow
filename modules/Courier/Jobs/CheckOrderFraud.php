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
use Modules\Courier\Services\PhoneNormalizer;
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

        $phone = PhoneNormalizer::normalize($order->phone);

        if (! preg_match('/^01[3-9][0-9]{8}$/', $phone)) {
            return;
        }

        $payload = $checker->check($phone);

        $answered = FraudChecker::answeredStats($payload);

        if ($answered === []) {
            Log::info('Fraud check skipped: no fraud source answered.', [
                'order_id' => $order->id,
            ]);

            return;
        }

        $minDeliveries = (int) setting('courier.fraud_min_deliveries', 5);
        $threshold = (float) setting('courier.fraud_cancel_ratio_threshold', 40);

        $risk = 'low';

        // Risk is decided per source: one clean source must never mask
        // another source's red flags.
        foreach ($answered as $stats) {
            $total = (int) ($stats['total'] ?? 0);

            if ($total < $minDeliveries) {
                continue;
            }

            $cancelRatio = round(((int) ($stats['cancel'] ?? 0)) / $total * 100, 2);

            if ($cancelRatio >= $threshold) {
                $risk = 'high';
            }
        }

        if ($this->reportedFraud($payload)) {
            $risk = 'high';
        }

        $order->update([
            'fraud_checked_at' => now(),
            'fraud_risk' => $risk,
            'fraud_details' => $payload,
        ]);
    }

    /**
     * True when any fraud source reported fraud reports, fraud categories,
     * or courier fraud reports against the phone, regardless of the
     * delivery/cancel thresholds.
     *
     * @param  array<string, mixed>  $payload
     */
    private function reportedFraud(array $payload): bool
    {
        foreach ($payload as $key => $stats) {
            if ($key === 'aggregate' || ! is_array($stats)) {
                continue;
            }

            if ((int) ($stats['total_reports'] ?? 0) > 0) {
                return true;
            }

            if (! empty($stats['fraud_categories']) && is_array($stats['fraud_categories'])) {
                return true;
            }

            if (! empty($stats['reports']) && is_array($stats['reports'])) {
                return true;
            }
        }

        return false;
    }
}
