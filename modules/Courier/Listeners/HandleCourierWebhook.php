<?php

namespace Modules\Courier\Listeners;

use CourierHub\Events\CourierWebhookReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Courier\Models\CourierEvent;
use Modules\Courier\Services\CourierConfigHydrator;
use Modules\Courier\Services\SyncShipmentStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderShipment;

/**
 * Persists normalized courier webhook updates. Runs on the queue, so courier
 * config must be hydrated from settings inside the worker process.
 */
class HandleCourierWebhook implements ShouldQueue
{
    public function __construct(
        private SyncShipmentStatus $sync,
        private CourierConfigHydrator $hydrator,
    ) {}

    public function handle(CourierWebhookReceived $event): void
    {
        $this->hydrator->hydrate();

        $webhook = $event->webhook;

        $shipment = $this->resolveShipment($webhook);

        if ($shipment === null) {
            Log::warning('Courier webhook received for unknown shipment', [
                'courier' => $webhook->courier_name,
                'tracking_id' => $webhook->tracking_id,
                'merchant_order_id' => $webhook->merchant_order_id,
            ]);

            CourierEvent::create([
                'courier' => (string) $webhook->courier_name,
                'order_id' => null,
                'tracking_id' => $webhook->tracking_id,
                'status' => $webhook->status->value,
                'payload' => $webhook->raw_payload,
                'created_at' => now(),
            ]);

            return;
        }

        $this->sync->apply($shipment, $webhook->status, $webhook->raw_payload);
    }

    private function resolveShipment(object $webhook): ?OrderShipment
    {
        if ($webhook->tracking_id !== '') {
            $shipment = OrderShipment::where('tracking_number', $webhook->tracking_id)->first();

            if ($shipment !== null) {
                return $shipment;
            }
        }

        if (! empty($webhook->consignment_id)) {
            $shipment = OrderShipment::where('consignment_id', $webhook->consignment_id)->first();

            if ($shipment !== null) {
                return $shipment;
            }
        }

        if (! empty($webhook->merchant_order_id) && ctype_digit((string) $webhook->merchant_order_id)) {
            $order = Order::find((int) $webhook->merchant_order_id);

            return $order?->orderShipments()->first();
        }

        return null;
    }
}
