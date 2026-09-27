<?php

namespace Modules\Courier\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Bus;
use Modules\Courier\Jobs\CheckOrderFraud;
use Modules\Order\Models\Order;
use Modules\Support\Http\Controllers\BackendController;

class FraudCheckController extends BackendController
{
    /**
     * Runs the fraud check for an order synchronously so the admin sees
     * the verdict (or the reason nothing was found) right away.
     */
    public function run(int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        if (! $order->requires_shipping) {
            return back()->with('error', 'Fraud checks only apply to orders that require shipping.');
        }

        if (! setting('courier.fraud_enabled')) {
            return back()->with('error', 'Fraud checks are disabled in Settings → Courier.');
        }

        try {
            Bus::dispatchNow(new CheckOrderFraud($order->id));
        } catch (\Throwable $e) {
            return back()->with('error', 'Fraud check failed: '.$e->getMessage());
        }

        $order->refresh();

        if ($order->fraud_checked_at === null) {
            return back()->with('error', 'No fraud source answered — check the fraud source settings.');
        }

        return back()->with('success', 'Fraud check completed — risk level: '.ucfirst((string) $order->fraud_risk).'.');
    }
}
