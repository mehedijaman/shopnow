<?php

namespace Modules\Cart\Services;

use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Product\Enums\ProductType;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariation;
use Modules\Product\Services\CheckBundleStock;

class AddItemToCart
{
    public function run(Cart $cart, array $data): CartItem
    {
        $product = Product::findOrFail($data['product_id']);

        abort_unless($product->active, 422, 'Product is not available.');

        if ($product->type === ProductType::Bundle) {
            $stockCheck = app(CheckBundleStock::class)->run($product);
            abort_unless($stockCheck['in_stock'], 422, 'Bundle stock is not available.');
        }

        $variation = null;
        if (! empty($data['product_variation_id'])) {
            $variation = ProductVariation::where('product_id', $product->id)
                ->where('id', $data['product_variation_id'])
                ->where('active', true)
                ->firstOrFail();

            abort_unless($variation->quantity >= ($data['quantity'] ?? 1), 422, 'Selected variation is out of stock.');
        }

        $existingItem = $cart->items()
            ->where('product_id', $data['product_id'])
            ->where('product_variation_id', $data['product_variation_id'] ?? null)
            ->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $data['quantity'];
            $maxQty = $variation ? $variation->quantity : $product->quantity;
            abort_if($newQty > $maxQty, 422, 'Requested quantity exceeds available stock.');

            $existingItem->increment('quantity', $data['quantity']);

            return $existingItem->fresh();
        }

        return $cart->items()->create([
            'product_id' => $data['product_id'],
            'product_variation_id' => $data['product_variation_id'] ?? null,
            'quantity' => $data['quantity'],
            'bundle_selection' => $data['bundle_selection'] ?? null,
        ]);
    }
}
