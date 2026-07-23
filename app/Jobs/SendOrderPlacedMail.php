<?php

namespace App\Jobs;

use App\Mail\OrderPlacedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Models\Order;
use Modules\Settings\SettingsServiceProvider;

class SendOrderPlacedMail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $orderId,
        public readonly string $adminEmail,
    ) {}

    public function handle(): void
    {
        SettingsServiceProvider::configureMailFromSettings();

        $order = Order::with([
            'orderProducts.product',
            'orderProducts.productVariation',
            'orderProducts.bundleItems',
        ])->find($this->orderId);

        if (! $order) {
            return;
        }

        if (! $this->isValidRealEmail($this->adminEmail)) {
            Log::warning("SendOrderPlacedMail skipped: '{$this->adminEmail}' is an invalid or placeholder email address for order #{$order->id}.");

            return;
        }

        Mail::to($this->adminEmail)->send(new OrderPlacedMail($order));
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
        $dummyDomains = [];

        return ! in_array($domain, $dummyDomains, true);
    }
}
