<?php

namespace Modules\Courier\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Modules\Courier\Jobs\RefreshShipmentStatus;
use Modules\Courier\Models\CourierEvent;
use Modules\Courier\Services\PhoneNormalizer;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderShipment;
use Modules\Support\Http\Controllers\SiteController;

class TrackController extends SiteController
{
    public function show(Request $request): View
    {
        return view('courier::track', [
            'tracking' => (string) $request->query('tracking', ''),
            'prefillPhone' => (string) (auth('customer')->user()?->phone ?? ''),
            'seo' => [
                'description' => 'Track your ShopNow order in real time. Enter your tracking or order number and the phone number you used at checkout.',
            ],
        ]);
    }

    public function result(Request $request): View
    {
        $validated = $request->validate([
            'tracking' => ['required', 'string', 'max:64'],
            'phone' => ['required', 'string', 'regex:/^01[3-9][0-9]{8}$/'],
        ], [
            'phone.regex' => 'Enter a valid Bangladeshi mobile number (e.g. 01712345678).',
        ]);

        $tracking = trim($validated['tracking']);
        $phone = PhoneNormalizer::normalize($validated['phone']);

        $shipment = $this->findShipment($tracking);
        $order = $shipment?->order ?? $this->findOrder($tracking);

        $matches = $order !== null
            && PhoneNormalizer::normalize($order->phone) === $phone;

        if (! $matches) {
            return view('courier::track', [
                'tracking' => $tracking,
                'phone' => $validated['phone'],
                'notFound' => true,
            ]);
        }

        // No shipment yet, or the shipment row exists from checkout but the
        // courier booking has not produced a tracking number yet.
        if ($shipment === null || $shipment->tracking_number === null) {
            return view('courier::track', [
                'tracking' => $tracking,
                'phone' => $validated['phone'],
                'order' => $order,
                'awaitingShipment' => true,
            ]);
        }

        return view('courier::track', [
            'tracking' => $tracking,
            'phone' => $validated['phone'],
            'shipment' => $shipment,
            'order' => $order,
            'events' => CourierEvent::where('order_id', $order->id)
                ->orderBy('created_at')
                ->get(),
            'refreshing' => $this->requestLiveRefresh($shipment),
        ]);
    }

    /**
     * Accepts a tracking number first, then falls back to an order number so
     * customers can track before the courier booking produces a tracking code.
     */
    private function findShipment(string $input): ?OrderShipment
    {
        $shipment = OrderShipment::with('order')->where('tracking_number', $input)->first();

        if ($shipment !== null) {
            return $shipment;
        }

        if (ctype_digit($input)) {
            return OrderShipment::with('order')
                ->where('order_id', (int) $input)
                ->latest('id')
                ->first();
        }

        return null;
    }

    private function findOrder(string $input): ?Order
    {
        return ctype_digit($input) ? Order::find((int) $input) : null;
    }

    /**
     * Dispatches a queued status refresh when the stored status is stale.
     * A per-shipment cache lock keeps the public endpoint from driving
     * unbounded courier API traffic (auth failures risk a 60-minute lockout);
     * the route's throttle covers the per-IP layer.
     */
    private function requestLiveRefresh(OrderShipment $shipment): bool
    {
        if (! RefreshShipmentStatus::isRefreshable($shipment)) {
            return false;
        }

        if (! Cache::add('courier:track-sync:'.$shipment->id, 1, 600)) {
            return false;
        }

        RefreshShipmentStatus::dispatch($shipment->id);

        return true;
    }
}
