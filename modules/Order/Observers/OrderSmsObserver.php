<?php

namespace Modules\Order\Observers;

use Modules\Order\Models\Order;
use Modules\Order\Services\OrderSms;

class OrderSmsObserver
{
    public function updating(Order $order): void
    {
        if ($order->isDirty('status')) {
            $order->sms_status_version = ((int) $order->getRawOriginal('sms_status_version')) + 1;
        }
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && ! $order->smsPlacementInProgress) {
            app(OrderSms::class)->updated($order);
        }
    }
}
