<?php

namespace Modules\Courier\Services\Steadfast;

use CourierHub\Enums\CourierStatus;

/**
 * Maps SteadFast delivery statuses (portal API v1 documentation) onto the
 * shared courier status enum.
 *
 * Approval-pending variants are explicitly non-final per the API docs, so they
 * map onto non-terminal enum cases: they must never settle an order early.
 * Unknown values become CourierStatus::Unknown instead of guessing.
 */
class StatusMapper
{
    public static function map(string $status): CourierStatus
    {
        return match (strtolower(trim($status))) {
            'pending', 'in review', 'unknown_approval_pending' => CourierStatus::Pending,
            'approved' => CourierStatus::Confirmed,
            'in_transit' => CourierStatus::InTransit,
            'hold' => CourierStatus::OnHold,
            'delivered' => CourierStatus::Delivered,
            'partial_delivered', 'partial delivered' => CourierStatus::PartialDelivered,
            'delivered_approval_pending', 'partial_delivered_approval_pending' => CourierStatus::OutForDelivery,
            'cancelled' => CourierStatus::Cancelled,
            'cancelled_approval_pending' => CourierStatus::InTransit,
            'exceptional' => CourierStatus::Failed,
            'returned' => CourierStatus::Returned,
            'cancelled_return_processing',
            'cancelled_return_rider_assigned',
            'partial_delivered_return_processing',
            'partial_delivered_return_rider_assigned' => CourierStatus::ReturnInTransit,
            'cancelled_return_received',
            'partial_delivered_return_received' => CourierStatus::Returned,
            default => CourierStatus::Unknown,
        };
    }
}
