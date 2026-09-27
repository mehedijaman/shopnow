<?php

namespace Modules\Courier\Http\Controllers;

use CourierHub\Exceptions\CourierApiException;
use CourierHub\Facades\Courier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Modules\Courier\Enums\CourierProvider;
use Modules\Courier\Jobs\BookShipment as BookShipmentJob;
use Modules\Courier\Services\CourierConfigHydrator;
use Modules\Courier\Services\CourierTrackingId;
use Modules\Courier\Services\SyncShipmentStatus;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Support\Http\Controllers\BackendController;

class BookShipmentController extends BackendController
{
    public function book(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'force' => 'sometimes|boolean',
        ]);

        $order = Order::with('orderShipments')->findOrFail($id);
        $shipment = $order->orderShipments->first();

        if (! $order->requires_shipping || $shipment === null) {
            return back()->with('error', 'This order has no shipment to book.');
        }

        if (filled($shipment->tracking_number)) {
            return back()->with('success', 'This shipment is already booked with the courier.');
        }

        if (in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Completed], true)) {
            return back()->with('error', 'Cancelled or completed orders cannot be booked.');
        }

        $force = (bool) $request->boolean('force');

        if ($order->fraud_risk === 'high' && ! $force) {
            return back()->with('error', 'High fraud risk — confirm booking to override.');
        }

        // Call the courier API directly — dispatchNow runs the handler in
        // this request without touching the queue — so the admin sees the
        // tracking number or the exact error immediately after the click.
        try {
            Bus::dispatchNow(new BookShipmentJob($order->id, $force));
        } catch (CourierApiException $e) {
            return back()->with('error', 'Booking failed: '.$this->apiErrorMessage($e));
        } catch (\Throwable $e) {
            return back()->with('error', 'Booking failed: '.$e->getMessage());
        }

        $shipment->refresh();

        if (filled($shipment->tracking_number)) {
            $courier = CourierProvider::tryFrom((string) $shipment->carrier)?->label() ?? 'the courier';

            return back()->with('success', 'Booked with '.$courier.' — tracking number '.$shipment->tracking_number.'.');
        }

        return back()->with('error', $shipment->booking_error ?? 'Booking did not complete. Check the courier settings and try again.');
    }

    public function refresh(int $id, CourierConfigHydrator $hydrator, SyncShipmentStatus $sync): RedirectResponse
    {
        $order = Order::with('orderShipments')->findOrFail($id);
        $shipment = $order->orderShipments->first();

        if ($shipment === null || blank($shipment->tracking_number) || blank($shipment->carrier)) {
            return back()->with('error', 'No tracking number to refresh yet.');
        }

        $hydrator->hydrate();

        try {
            if (! Courier::isEnabled($shipment->carrier)) {
                return back()->with('error', 'The courier is disabled in settings.');
            }

            $tracking = Courier::driver($shipment->carrier)->trackOrder(CourierTrackingId::for($shipment));

            $sync->apply($shipment, $tracking->current_status, $tracking->raw_response, $tracking->estimated_delivery);

            return back()->with('success', 'Status refreshed: '.str_replace('_', ' ', $tracking->current_status->value));
        } catch (CourierApiException $e) {
            return back()->with('error', 'Status refresh failed: '.$this->apiErrorMessage($e));
        } catch (\Throwable $e) {
            return back()->with('error', 'Status refresh failed: '.$e->getMessage());
        }
    }

    /**
     * Human-friendly wording for the responses that must never be retried
     * blindly (bad credentials and lockouts burn the courier's auth budget).
     */
    private function apiErrorMessage(CourierApiException $e): string
    {
        return match (true) {
            in_array($e->getCode(), [401, 403], true) => 'courier authentication failed — check the API keys in Settings → Courier.',
            $e->getCode() === 429 => 'courier rejected the request (rate limited or locked out) — try again later.',
            default => $e->getMessage(),
        };
    }
}
