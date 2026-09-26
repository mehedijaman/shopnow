<?php

namespace Modules\Cart\Services;

use Modules\Cart\Models\Cart;
use Modules\PromoCode\Services\CalculatePromoDiscount;

class GetCartTotals
{
    public function __construct(private CalculatePromoDiscount $calculatePromoDiscount) {}

    public function run(Cart $cart): array
    {
        $items = $cart->items()->with(['product', 'productVariation.attributeValues.attribute'])->get();

        $subtotal = 0;
        $totalQuantity = 0;
        $requiresShipping = false;
        $isDownloadable = false;

        $formattedItems = $items->map(function ($item) use (&$subtotal, &$totalQuantity, &$requiresShipping, &$isDownloadable) {
            $unitPrice = $item->unit_price;
            $lineTotal = $item->total_price;

            $subtotal += $lineTotal;
            $totalQuantity += $item->quantity;

            if ($item->product->requiresShipping()) {
                $requiresShipping = true;
            }

            if ($item->product->is_downloadable) {
                $isDownloadable = true;
            }

            $variationLabel = null;
            if ($item->productVariation) {
                $labels = [];
                foreach ($item->productVariation->attributeValues as $av) {
                    $attrName = $av->attribute?->name ?? 'Option';
                    $labels[] = $attrName.': '.$av->value;
                }
                $variationLabel = implode(', ', $labels);
            }

            $regularPrice = (float) ($item->productVariation?->price ?? $item->product->price);
            $rawSalePrice = $item->productVariation?->sale_price ?? $item->product->sale_price;
            $salePrice = ($rawSalePrice !== null && (float) $rawSalePrice > 0 && (float) $rawSalePrice < $regularPrice)
                ? (float) $rawSalePrice
                : null;

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_variation_id' => $item->product_variation_id,
                'quantity' => $item->quantity,
                'unit_price' => $unitPrice,
                'regular_price' => $regularPrice,
                'sale_price' => $salePrice,
                'total_price' => $lineTotal,
                'bundle_selection' => $item->bundle_selection,
                'variation_label' => $variationLabel,
                'item' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'price' => $regularPrice,
                    'sale_price' => $salePrice,
                    'image_url' => $item->product->image_url,
                    'quantity' => $item->product->quantity,
                    'unit' => $item->product->unit,
                    'is_virtual' => $item->product->is_virtual,
                    'is_downloadable' => $item->product->is_downloadable,
                    'product_variation_id' => $item->product_variation_id,
                    'variation_label' => $variationLabel,
                ],
            ];
        });

        [$discount, $coupon] = $this->resolveCoupon($cart, (float) $subtotal);

        return [
            'items' => $formattedItems,
            'totalItems' => $formattedItems->count(),
            'totalQuantity' => $totalQuantity,
            'subtotal' => $subtotal,
            'tax' => 0,
            'discount' => $discount,
            'coupon' => $coupon,
            'requiresShipping' => $requiresShipping,
            'is_downloadable' => $isDownloadable,
        ];
    }

    /**
     * Detach codes that are no longer valid (changed cart, expired, exhausted, …)
     * and expose the applied code + discount for the checkout summary.
     *
     * @return array{0: float, 1: array<string, mixed>|null}
     */
    private function resolveCoupon(Cart $cart, float $subtotal): array
    {
        if (! $cart->coupon_code) {
            return [0, null];
        }

        $promoCode = $cart->coupon()
            ->where('code', $cart->coupon_code)
            ->first();

        if (! $promoCode || $promoCode->validationError($subtotal, $cart->customer_id) !== null) {
            $cart->updateQuietly(['coupon_code' => null]);

            return [0, null];
        }

        $discount = $this->calculatePromoDiscount->run($promoCode, $subtotal);

        return [$discount, [
            'code' => $promoCode->code,
            'discount_type' => $promoCode->discount_type->value,
            'discount' => $discount,
            'waives_shipping' => $promoCode->discount_type->isFreeShipping(),
            'minimum_order_amount' => (float) $promoCode->minimum_order_amount,
        ]];
    }
}
