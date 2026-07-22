<?php

namespace App\Jobs;

use App\Mail\CustomerOrderConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Models\Order;

class SendCustomerOrderConfirmationMail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $orderId,
    ) {}

    public function handle(): void
    {
        $order = Order::with([
            'orderProducts.product',
            'orderProducts.productVariation',
            'orderProducts.bundleItems',
        ])->find($this->orderId);

        if (! $order || ! $order->email) {
            return;
        }

        if (! $this->isValidRealEmail($order->email)) {
            Log::warning("SendCustomerOrderConfirmationMail skipped: '{$order->email}' is an invalid or placeholder email address for order #{$order->id}.");

            return;
        }

        Mail::to($order->email)->send(new CustomerOrderConfirmationMail($order));
    }

    private function isValidRealEmail(?string $email): bool
    {
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return true;
        }

        $domain = strtolower(substr(strrchr($email, '@'), 1));
        $dummyDomains = ['example.com', 'example.org', 'example.net', 'localhost', 'test.com'];

        return ! in_array($domain, $dummyDomains, true);
    }
}
