<?php

namespace Modules\Courier\Services;

use Modules\Courier\Enums\CourierProvider;
use Modules\Order\Models\OrderShipment;

/**
 * Resolves the identifier a status lookup should use for a booked shipment.
 *
 * SteadFast's documented status endpoint (/status_by_cid) expects the numeric
 * consignment id; the tracking code only works on the separate
 * /status_by_trackingcode endpoint. Other couriers keep using their tracking
 * number as before.
 */
class CourierTrackingId
{
    public static function for(OrderShipment $shipment): string
    {
        if ($shipment->carrier === CourierProvider::Steadfast->value && filled($shipment->consignment_id)) {
            return (string) $shipment->consignment_id;
        }

        return (string) $shipment->tracking_number;
    }
}
