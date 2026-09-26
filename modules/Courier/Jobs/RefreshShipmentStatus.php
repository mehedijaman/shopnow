<?php

namespace Modules\Courier\Jobs;

use CourierHub\Enums\CourierStatus;
use CourierHub\Facades\Courier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Courier\Services\CourierConfigHydrator;
use Modules\Courier\Services\CourierTrackingId;
use Modules\Courier\Services\SyncShipmentStatus;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Models\OrderShipment;

/**
 * Refreshes one shipment's status from the courier API on demand, triggered
 * by a public tracking page view. All failures are swallowed so the queue
 * never retries: the trigger is public, and retrying auth failures
 * (401/403/429) could deepen the courier's auth-failure budget and lock the
 * API keys out for 60 minutes. The scheduled courier:poll command retries
 * stale shipments anyway.
 */
class RefreshShipmentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array<string, true> Courier statuses that no longer change
     */
    private const TERMINAL = [
        CourierStatus::Delivered->value => true,
        CourierStatus::Cancelled->value => true,
        CourierStatus::Returned->value => true,
        CourierStatus::Failed->value => true,
    ];

    public function __construct(
        public int $shipmentId,
    ) {}

    /**
     * Whether the public tracking page may request a live refresh for this
     * shipment. Mirrors the courier:poll staleness window (10 minutes) and
     * skips shipments that are unbooked, terminal, or already fresh.
     */
    public static function isRefreshable(OrderShipment $shipment): bool
    {
        if ($shipment->carrier === null || $shipment->tracking_number === null) {
            return false;
        }

        if (isset(self::TERMINAL[(string) $shipment->courier_status])) {
            return false;
        }

        if (in_array($shipment->shopment_status, [ShipmentStatus::Delivered, ShipmentStatus::Cancelled], true)) {
            return false;
        }

        return $shipment->last_synced_at === null
            || $shipment->last_synced_at->lt(now()->subMinutes(10));
    }

    public function handle(CourierConfigHydrator $hydrator, SyncShipmentStatus $sync): void
    {
        $hydrator->hydrate();

        $shipment = OrderShipment::find($this->shipmentId);

        if ($shipment === null || ! self::isRefreshable($shipment)) {
            return;
        }

        try {
            if (! Courier::isEnabled($shipment->carrier)) {
                return;
            }

            $tracking = Courier::driver($shipment->carrier)->trackOrder(CourierTrackingId::for($shipment));

            $sync->apply($shipment, $tracking->current_status, $tracking->raw_response, $tracking->estimated_delivery);
        } catch (\Throwable $e) {
            Log::warning('On-demand shipment refresh failed', [
                'shipment_id' => $this->shipmentId,
                'http_code' => $e->getCode(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
