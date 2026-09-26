<?php

namespace Modules\Courier\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Courier\Models\CourierEvent;
use Modules\Order\Models\OrderShipment;
use Modules\Support\Http\Controllers\SiteController;

class TrackController extends SiteController
{
    public function show(Request $request): View
    {
        return view('courier::track', [
            'tracking' => (string) $request->query('tracking', ''),
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
        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);

        $shipment = OrderShipment::with('order')->where('tracking_number', $tracking)->first();
        $order = $shipment?->order;

        $matches = $shipment !== null
            && $order !== null
            && preg_replace('/[^0-9]/', '', (string) $order->phone) === $phone;

        if (! $matches) {
            return view('courier::track', [
                'tracking' => $tracking,
                'phone' => $validated['phone'],
                'notFound' => true,
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
        ]);
    }
}
