<?php

use CourierHub\Enums\CourierStatus;
use Modules\Courier\Services\Steadfast\StatusMapper;
use Tests\TestCase;

uses(TestCase::class);

test('maps documented SteadFast delivery statuses onto the courier enum', function (string $raw, CourierStatus $expected) {
    expect(StatusMapper::map($raw))->toBe($expected);
})->with([
    'pending' => ['pending', CourierStatus::Pending],
    'in review (initial booking state)' => ['in review', CourierStatus::Pending],
    'hold' => ['hold', CourierStatus::OnHold],
    'delivered' => ['delivered', CourierStatus::Delivered],
    'partial_delivered (underscore)' => ['partial_delivered', CourierStatus::PartialDelivered],
    'partial delivered (legacy spacing)' => ['partial delivered', CourierStatus::PartialDelivered],
    'delivered_approval_pending is not final' => ['delivered_approval_pending', CourierStatus::OutForDelivery],
    'partial_delivered_approval_pending is not final' => ['partial_delivered_approval_pending', CourierStatus::OutForDelivery],
    'cancelled_approval_pending is not final' => ['cancelled_approval_pending', CourierStatus::InTransit],
    'unknown_approval_pending is not final' => ['unknown_approval_pending', CourierStatus::Pending],
    'cancelled' => ['cancelled', CourierStatus::Cancelled],
    'exceptional' => ['exceptional', CourierStatus::Failed],
    'unknown' => ['unknown', CourierStatus::Unknown],
    'approved (legacy)' => ['approved', CourierStatus::Confirmed],
    'in_transit (legacy)' => ['in_transit', CourierStatus::InTransit],
    'returned (legacy)' => ['returned', CourierStatus::Returned],
    'cancelled_return_processing' => ['cancelled_return_processing', CourierStatus::ReturnInTransit],
    'cancelled_return_rider_assigned' => ['cancelled_return_rider_assigned', CourierStatus::ReturnInTransit],
    'cancelled_return_received' => ['cancelled_return_received', CourierStatus::Returned],
    'partial_delivered_return_processing' => ['partial_delivered_return_processing', CourierStatus::ReturnInTransit],
    'partial_delivered_return_rider_assigned' => ['partial_delivered_return_rider_assigned', CourierStatus::ReturnInTransit],
    'partial_delivered_return_received' => ['partial_delivered_return_received', CourierStatus::Returned],
    'case and whitespace are tolerated' => ['  Delivered ', CourierStatus::Delivered],
    'unrecognized statuses fall back to unknown' => ['brand_new_status', CourierStatus::Unknown],
    'empty string falls back to unknown' => ['', CourierStatus::Unknown],
]);
