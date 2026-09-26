<?php

namespace Modules\Courier\Services;

use CourierHub\Enums\CourierStatus;
use Illuminate\Support\Carbon;
use Modules\Courier\Models\CourierEvent;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderShipment;

/**
 * Maps normalized courier statuses onto the shop's shipment and order status,
 * advancing them only forward, and records a courier_events row whenever the
 * status actually changes.
 */
class SyncShipmentStatus
{
    /**
     * @var array<string, ShipmentStatus> CourierStatus value => ShipmentStatus
     */
    private const SHIPMENT_MAP = [
        CourierStatus::Pending->value => ShipmentStatus::Pending,
        CourierStatus::Confirmed->value => ShipmentStatus::Pending,
        CourierStatus::OnHold->value => ShipmentStatus::Processing,
        CourierStatus::PickedUp->value => ShipmentStatus::Shipped,
        CourierStatus::InTransit->value => ShipmentStatus::Shipped,
        CourierStatus::OutForDelivery->value => ShipmentStatus::Shipped,
        CourierStatus::PartialDelivered->value => ShipmentStatus::Shipped,
        CourierStatus::Delivered->value => ShipmentStatus::Delivered,
        CourierStatus::Cancelled->value => ShipmentStatus::Cancelled,
        CourierStatus::ReturnInTransit->value => ShipmentStatus::Cancelled,
        CourierStatus::Returned->value => ShipmentStatus::Cancelled,
        CourierStatus::Failed->value => ShipmentStatus::Cancelled,
    ];

    /**
     * @var array<string, int> ShipmentStatus value => progression rank
     */
    private const SHIPMENT_RANK = [
        ShipmentStatus::Pending->value => 0,
        ShipmentStatus::Processing->value => 1,
        ShipmentStatus::Shipped->value => 2,
        ShipmentStatus::Delivered->value => 3,
        ShipmentStatus::Cancelled->value => 4,
    ];

    /**
     * @var array<string, int> OrderStatus value => progression rank
     */
    private const ORDER_RANK = [
        OrderStatus::Pending->value => 0,
        OrderStatus::Processing->value => 1,
        OrderStatus::Shipped->value => 2,
        OrderStatus::Delivered->value => 3,
        OrderStatus::Completed->value => 4,
        OrderStatus::Cancelled->value => 5,
    ];

    /**
     * @param  array<string, mixed>  $payload  raw courier payload for the event log
     */
    public function apply(OrderShipment $shipment, CourierStatus $courierStatus, array $payload = [], ?string $estimatedDelivery = null): bool
    {
        $changed = $shipment->courier_status !== $courierStatus->value;
        $previousStatus = $shipment->shopment_status;

        $shipment->courier_status = $courierStatus->value;
        $shipment->last_synced_at = now();

        if ($estimatedDelivery !== null) {
            try {
                $shipment->estimated_delivery = Carbon::parse($estimatedDelivery);
            } catch (\Throwable) {
                // Keep the previous estimate when the courier sends an unparsable date.
            }
        }

        $mapped = self::SHIPMENT_MAP[$courierStatus->value] ?? null;

        if ($mapped !== null && $this->shipmentCanAdvance($previousStatus, $mapped)) {
            $shipment->shopment_status = $mapped;

            if ($mapped === ShipmentStatus::Shipped && $shipment->shipment_date === null) {
                $shipment->shipment_date = now();
            }

            if ($mapped === ShipmentStatus::Delivered && $shipment->actual_delivery === null) {
                $shipment->actual_delivery = now();
            }
        }

        $shipment->save();

        $this->advanceOrder($shipment->order, $shipment->shopment_status);

        // Log when either the normalized status or the raw courier status
        // changed. Distinct raw statuses can normalize to the same enum case
        // (e.g. "in review" and "pending" both become Pending), and those
        // transitions still belong in the audit trail.
        $rawStatus = self::extractRawStatus($payload);
        $rawChanged = $rawStatus !== null && $rawStatus !== $this->lastLoggedRawStatus($shipment);

        if ($changed || $rawChanged) {
            CourierEvent::create([
                'courier' => (string) $shipment->carrier,
                'order_id' => $shipment->order_id,
                'tracking_id' => $shipment->tracking_number,
                'status' => $courierStatus->value,
                'payload' => $payload === [] ? null : $payload,
                'created_at' => now(),
            ]);
        }

        return $changed;
    }

    private function shipmentCanAdvance(ShipmentStatus $current, ShipmentStatus $next): bool
    {
        if ($current === $next) {
            return false;
        }

        if ($current === ShipmentStatus::Delivered) {
            return false;
        }

        if ($current === ShipmentStatus::Cancelled) {
            return false;
        }

        if ($next === ShipmentStatus::Cancelled) {
            return true;
        }

        return self::SHIPMENT_RANK[$next->value] > self::SHIPMENT_RANK[$current->value];
    }

    /**
     * Raw courier status from a status response, webhook payload or booking
     * payload, normalized for comparison. Null when the payload carries none.
     *
     * @param  array<string, mixed>  $payload
     */
    private static function extractRawStatus(array $payload): ?string
    {
        $deliveryStatus = $payload['delivery_status'] ?? null;

        if (is_scalar($deliveryStatus) && $deliveryStatus !== '') {
            return strtolower(trim((string) $deliveryStatus));
        }

        $status = $payload['status'] ?? null;

        // Booking responses reuse "status" for the HTTP-ish code (200), so
        // only string-like values count as a courier status there.
        if (is_scalar($status) && $status !== '' && ! is_numeric($status)) {
            return strtolower(trim((string) $status));
        }

        $consignment = $payload['consignment'] ?? null;

        if (is_array($consignment) && isset($consignment['status']) && is_scalar($consignment['status']) && $consignment['status'] !== '') {
            return strtolower(trim((string) $consignment['status']));
        }

        return null;
    }

    private function lastLoggedRawStatus(OrderShipment $shipment): ?string
    {
        if ($shipment->tracking_number === null) {
            return null;
        }

        $last = CourierEvent::query()
            ->where('order_id', $shipment->order_id)
            ->where('tracking_id', $shipment->tracking_number)
            ->latest('id')
            ->first();

        return $last === null ? null : self::extractRawStatus((array) $last->payload);
    }

    private function advanceOrder(?Order $order, ShipmentStatus $shipmentStatus): void
    {
        if ($order === null) {
            return;
        }

        $target = match ($shipmentStatus) {
            ShipmentStatus::Processing => OrderStatus::Processing,
            ShipmentStatus::Shipped => OrderStatus::Shipped,
            ShipmentStatus::Delivered => OrderStatus::Delivered,
            ShipmentStatus::Cancelled => OrderStatus::Cancelled,
            default => null,
        };

        if ($target === null || $order->status === $target) {
            return;
        }

        $currentRank = self::ORDER_RANK[$order->status->value];
        $targetRank = self::ORDER_RANK[$target->value];

        $canAdvance = match (true) {
            $target === OrderStatus::Cancelled => ! in_array($order->status, [OrderStatus::Delivered, OrderStatus::Completed, OrderStatus::Cancelled], true),
            $order->status === OrderStatus::Cancelled => false,
            default => $targetRank > $currentRank,
        };

        if ($canAdvance) {
            $order->update(['status' => $target]);
        }
    }
}
