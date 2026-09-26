<?php

namespace Modules\Courier\Services\Steadfast;

use CourierHub\Drivers\Steadfast\SteadfastDriver as BaseSteadfastDriver;
use CourierHub\DTOs\OrderData;
use CourierHub\DTOs\OrderResponse;
use CourierHub\DTOs\TrackingResponse;
use CourierHub\DTOs\WebhookEvent;
use Illuminate\Http\Request;

/**
 * SteadFast driver variant that follows the current portal API documentation
 * instead of the package defaults:
 *
 * - booking sends the parcel details (item_description, total_lot);
 * - status lookups run through the documented status vocabulary via
 *   Modules\Courier\Services\Steadfast\StatusMapper (the package mapper does
 *   not know "in review", approval-pending or return statuses);
 * - parseWebhook applies the same vocabulary to incoming webhooks.
 *
 * The status lookup itself still hits /status_by_cid, which requires the
 * numeric consignment id — callers resolve that via CourierTrackingId.
 */
class SteadfastDriver extends BaseSteadfastDriver
{
    public function createOrder(OrderData $order): OrderResponse
    {
        $payload = [
            'invoice' => $order->merchant_order_id,
            'recipient_name' => $order->recipient_name,
            'recipient_phone' => $order->recipient_phone,
            'recipient_address' => $order->recipient_address,
            'cod_amount' => $order->amount_to_collect,
            'item_description' => $order->item_description,
            'total_lot' => max(1, (int) $order->item_quantity),
            'note' => $order->special_instruction ?? $order->item_description,
        ];

        $response = $this->client->post('/create_order', $payload);
        $consignment = $response['consignment'] ?? [];

        return new OrderResponse(
            tracking_id: (string) ($consignment['tracking_code'] ?? $consignment['consignment_id'] ?? ''),
            courier_name: 'steadfast',
            status: StatusMapper::map((string) ($consignment['status'] ?? 'pending')),
            consignment_id: isset($consignment['consignment_id']) ? (string) $consignment['consignment_id'] : null,
            raw_response: $response,
        );
    }

    public function trackOrder(string $trackingId): TrackingResponse
    {
        $response = $this->client->get('/status_by_cid/'.$trackingId);

        return new TrackingResponse(
            tracking_id: (string) ($response['tracking_code'] ?? $trackingId),
            current_status: StatusMapper::map((string) ($response['delivery_status'] ?? '')),
            history: [],
            raw_response: $response,
        );
    }

    public function parseWebhook(Request $request): WebhookEvent
    {
        $payload = $request->all();

        return new WebhookEvent(
            courier_name: 'steadfast',
            tracking_id: (string) ($payload['tracking_code'] ?? $payload['consignment_id'] ?? ''),
            status: StatusMapper::map((string) ($payload['status'] ?? $payload['delivery_status'] ?? '')),
            raw_payload: $payload,
            timestamp: now()->toIso8601String(),
            merchant_order_id: isset($payload['invoice']) ? (string) $payload['invoice'] : null,
            consignment_id: isset($payload['consignment_id']) ? (string) $payload['consignment_id'] : null,
        );
    }
}
