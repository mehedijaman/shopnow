<?php

namespace Modules\Courier\Jobs;

use CourierHub\Exceptions\CourierDisabledException;
use CourierHub\Exceptions\CourierNotFoundException;
use CourierHub\Exceptions\InvalidConfigurationException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        } catch (CourierDisabledException|CourierNotFoundException|InvalidConfigurationException $e) {
            $shipment->update(['booking_error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            $shipment->update(['booking_error' => $e->getMessage()]);

            throw $e;
        }
    }
}
