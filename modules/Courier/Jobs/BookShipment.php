<?php

namespace Modules\Courier\Jobs;

use CourierHub\Exceptions\CourierApiException;
use CourierHub\Exceptions\CourierDisabledException;
use CourierHub\Exceptions\CourierNotFoundException;
use CourierHub\Exceptions\InvalidConfigurationException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Courier\Exceptions\InvalidShipmentDataException;
use Modules\Courier\Services\BookCourierShipment;
use Modules\Courier\Services\CourierConfigHydrator;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;

/**
 * Books a single order shipment with the configured courier. Configuration
 * errors are recorded on the shipment without retrying; transient API errors
 * are rethrown so the queue can back off and retry.
 */
class BookShipment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [30, 90];

    public function __construct(
        private int $orderId,
        private bool $force = false,
    ) {}

    public function handle(CourierConfigHydrator $hydrator, BookCourierShipment $booker): void
    {
        $hydrator->hydrate();

        $order = Order::find($this->orderId);

        if ($order === null || ! $order->requires_shipping) {
            return;
        }

        if (in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Completed], true)) {
            return;
        }

        $shipment = $order->orderShipments()->first();

        if ($shipment === null || filled($shipment->tracking_number)) {
            return;
        }

        if (! $this->force && $order->fraud_risk === 'high') {
            $shipment->update([
                'booking_error' => 'Blocked by fraud check. Review the order, then confirm booking to override.',
            ]);

            return;
        }

        try {
            $booker->book($shipment);
        } catch (CourierDisabledException|CourierNotFoundException|InvalidConfigurationException|InvalidShipmentDataException $e) {
            $shipment->update(['booking_error' => $e->getMessage()]);
        } catch (CourierApiException $e) {
            $shipment->update(['booking_error' => $this->apiErrorMessage($e)]);

            // Auth failures and lockouts must not be retried: each attempt
            // adds to the courier's auth-failure budget (10 in 5 minutes
            // locks the key out for an hour), so record and stop.
            if (! in_array($e->getCode(), [401, 403, 429], true)) {
                throw $e;
            }
        } catch (\Throwable $e) {
            $shipment->update(['booking_error' => $e->getMessage()]);

            throw $e;
        }
    }

    private function apiErrorMessage(CourierApiException $e): string
    {
        return match (true) {
            in_array($e->getCode(), [401, 403], true) => 'Courier authentication failed. Check the API keys in Settings → Courier.',
            $e->getCode() === 429 => 'Courier rejected the request (rate limited or locked out). Wait for the lockout to clear, then retry.',
            default => $e->getMessage(),
        };
    }
}
