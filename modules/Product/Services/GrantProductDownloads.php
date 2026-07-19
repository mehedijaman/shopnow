<?php

namespace Modules\Product\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Product\Mail\ProductDownloadReadyMail;
use Modules\Product\Models\DownloadPermission;
use Modules\Product\Models\Product;
use Modules\Support\Events\OrderPaymentConfirmed;

class GrantProductDownloads
{
    /**
     * @param  array<int, array{product_id:int, order_product_id:int, is_downloadable:bool}>  $items
     */
    public function run(OrderPaymentConfirmed $event): void
    {
        $defaultExpiryDays = (int) setting('downloads.default_expiry_days', 30);
        $defaultLimit = (int) setting('downloads.default_limit', 5);

        $permissionsByEmail = [];

        foreach ($event->items as $item) {
            if (! ($item['is_downloadable'] ?? false)) {
                continue;
            }

            $product = Product::with('productFiles.media')->find($item['product_id']);

            if (! $product || $product->productFiles->isEmpty()) {
                continue;
            }

            foreach ($product->productFiles as $productFile) {
                $expiresAt = $defaultExpiryDays > 0
                    ? now()->addDays($defaultExpiryDays)
                    : null;

                $limit = $defaultLimit > 0 ? $defaultLimit : null;

                $permission = DownloadPermission::create([
                    'order_id' => $event->orderId,
                    'order_product_id' => $item['order_product_id'],
                    'customer_id' => $event->customerId,
                    'product_id' => $item['product_id'],
                    'product_file_id' => $productFile->id,
                    'download_token' => Str::random(64),
                    'download_limit' => $limit,
                    'download_count' => 0,
                    'expires_at' => $expiresAt,
                    'active' => true,
                ]);

                $permissionsByEmail[$event->customerEmail][] = $permission;
            }
        }

        foreach ($permissionsByEmail as $email => $permissions) {
            if (! empty($permissions)) {
                Mail::to($email)->queue(new ProductDownloadReadyMail($permissions));
            }
        }
    }
}
