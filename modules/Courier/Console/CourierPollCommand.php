<?php

namespace Modules\Courier\Console;

use CourierHub\Enums\CourierStatus;
use CourierHub\Facades\Courier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Courier\Services\CourierConfigHydrator;
use Modules\Courier\Services\SyncShipmentStatus;
use Modules\Order\Models\OrderShipment;

class CourierPollCommand extends Command
{
    protected $signature = 'courier:poll {--limit=50 : Maximum shipments to poll}';

    protected $description = 'Poll courier APIs for shipments that have not received a recent status update';

    /**
     * @var array<string, true> Courier statuses that no longer change
     */
    private const TERMINAL = [
        CourierStatus::Delivered->value => true,
        CourierStatus::Cancelled->value => true,
        CourierStatus::Returned->value => true,
        CourierStatus::Failed->value => true,
    ];

    public function handle(CourierConfigHydrator $hydrator, SyncShipmentStatus $sync): int
    {
        $hydrator->hydrate();

        $cutoff = now()->subMinutes(15);

        $shipments = OrderShipment::query()
            ->whereNotNull('carrier')
            ->whereNotNull('tracking_number')
            ->where(function ($query) {
                $query->whereNull('courier_status')
                    ->orWhereNotIn('courier_status', array_keys(self::TERMINAL));
            })
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_synced_at')
                    ->orWhere('last_synced_at', '<', $cutoff);
            })
            ->limit((int) $this->option('limit'))
            ->get();

        $synced = 0;
        $failed = 0;

        foreach ($shipments as $shipment) {
            try {
                if (! Courier::isEnabled($shipment->carrier)) {
                    continue;
                }

                $tracking = Courier::driver($shipment->carrier)->trackOrder($shipment->tracking_number);

                $sync->apply($shipment, $tracking->current_status, $tracking->raw_response, $tracking->estimated_delivery);

                $synced++;
            } catch (\Throwable $e) {
                $failed++;

                Log::warning('Courier status poll failed', [
                    'shipment_id' => $shipment->id,
                    'carrier' => $shipment->carrier,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Polled {$synced} shipments ({$failed} failed).");

        return self::SUCCESS;
    }
}
