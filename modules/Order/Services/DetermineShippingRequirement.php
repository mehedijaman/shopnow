<?php

namespace Modules\Order\Services;

use Illuminate\Support\Collection;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;

class DetermineShippingRequirement
{
    public function run(Cart|Order|Collection|array $source): bool
    {
        return match (true) {
            $source instanceof Cart => $this->fromCart($source),
            $source instanceof Order => $this->fromOrder($source),
            $source instanceof Collection => $this->fromItems($source),
            is_array($source) => $this->fromArray($source),
            default => true,
        };
    }

    private function fromCart(Cart $cart): bool
    {
        return $this->fromItems($cart->items()->with('product')->get());
    }

    private function fromOrder(Order $order): bool
    {
        return $this->fromItems($order->orderProducts()->with('product')->get());
    }

    private function fromItems(Collection $items): bool
    {
        foreach ($items as $item) {
            $product = $item->product ?? null;

            if ($product && $product->requiresShipping()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array  $data  items array from request payload
     */
    private function fromArray(array $data): bool
    {
        foreach ($data as $entry) {
            $itemData = $entry['item'] ?? $entry;

            // If the payload already carries the flag, use it
            if (array_key_exists('is_virtual', $itemData)) {
                if (! $itemData['is_virtual']) {
                    return true;
                }

                continue;
            }

            // Otherwise look up the product
            $productId = $itemData['id'] ?? $itemData['product_id'] ?? null;

            if ($productId) {
                $product = Product::find($productId);

                if ($product && $product->requiresShipping()) {
                    return true;
                }
            }
        }

        return false;
    }
}
