<?php

namespace App\Jobs;

use App\Mail\OrderPlacedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Models\Order;

class SendOrderPlacedMail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $orderId,
        public readonly string $adminEmail,
    ) {}

    public function handle(): void
    {
        $order = Order::with('orderProducts.product')->find($this->orderId);

        if (! $order) {
            return;
        }

        Mail::to($this->adminEmail)->send(new OrderPlacedMail($order));
    }
}
