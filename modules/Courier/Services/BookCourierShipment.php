<?php

namespace Modules\Courier\Services;

use CourierHub\DTOs\OrderData;
use CourierHub\DTOs\OrderResponse;
use CourierHub\Exceptions\CourierDisabledException;
use CourierHub\Facades\Courier;
use Illuminate\Support\Str;
use Modules\Courier\Models\CourierEvent;
use Modules\Order\Enums\PaymentMethod;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderShipment;

/**
 * Books an order shipment with the configured courier and persists the
 * returned tracking details onto the shipment.
 */
class BookCourierShipment
{
    public function __construct(private CourierConfigHydrator $hydrator) {}

    /**
     * @throws CourierDisabledException when the configured courier is off
     * @throws \Throwable when the courier API call fails or returns no tracking ID
     */
    public function book(OrderShipment $shipment): OrderResponse
    {
        $this->hydrator->hydrate();

        $order = $shipment->order;

        if ($order === null) {
            throw new \RuntimeException('Shipment has no parent order.');
        }

        $provider = $this->provider();

        if (! Courier::isEnabled($provider)) {
            throw new CourierDisabledException("Courier [{$provider}] is not enabled. Enable it in Settings → Courier.");
        }

        $response = Courier::driver($provider)->createOrder($this->orderData($order));

        if ($response->tracking_id === '') {
            throw new \RuntimeException('The courier did not return a tracking ID.');
        }

        $shipment->update([
            'carrier' => $provider,
            'tracking_number' => $response->tracking_id,
            'consignment_id' => $response->consignment_id,
            'courier_status' => $response->status->value,
            'tracking_url' => $this->trackingUrl($provider, $response->tracking_id),
            'booked_at' => now(),
            'shipment_date' => now(),
            'last_synced_at' => now(),
            'booking_error' => null,
        ]);

        CourierEvent::create([
            'courier' => $provider,
            'order_id' => $shipment->order_id,
            'tracking_id' => $response->tracking_id,
            'status' => $response->status->value,
            'payload' => $response->raw_response ?: null,
            'created_at' => now(),
        ]);

        return $response;
    }

    private function provider(): string
    {
        return (string) (setting('courier.default_courier') ?: config('courierhub.default'));
    }

    private function orderData(Order $order): OrderData
    {
        $address = implode(', ', array_filter([
            $order->address,
            $order->upazila,
            $order->district,
            $order->division,
        ]));

        return OrderData::from([
            'merchant_order_id' => (string) $order->id,
            'recipient_name' => $order->name,
            'recipient_phone' => $order->phone,
            'recipient_address' => $address,
            'amount_to_collect' => $order->payment_method === PaymentMethod::Cod->value
                ? (float) $order->due
                : 0.0,
            'weight' => (float) (setting('courier.default_weight_kg') ?: 1),
            'item_description' => "Order #{$order->id}",
            'item_quantity' => max(1, (int) $order->orderProducts()->sum('quantity')),
            'special_instruction' => $order->notes ? Str::limit($order->notes, 200, '') : null,
        ]);
    }

    private function trackingUrl(string $provider, string $trackingId): ?string
    {
        $template = setting("courier.{$provider}_tracking_url");

        if (blank($template)) {
            return null;
        }

        return str_replace('{tracking}', rawurlencode($trackingId), (string) $template);
    }
}
