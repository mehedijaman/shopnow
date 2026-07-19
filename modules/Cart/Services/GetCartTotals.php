<?php

namespace Modules\Cart\Services;

use Modules\Cart\Models\Cart;

class GetCartTotals
{
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

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_variation_id' => $item->product_variation_id,
                'quantity' => $item->quantity,
                'unit_price' => $unitPrice,
                'total_price' => $lineTotal,
                'bundle_selection' => $item->bundle_selection,
                'variation_label' => $variationLabel,
                'item' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'price' => $item->product->price,
                    'sale_price' => $item->product->sale_price,
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

        return [
            'items' => $formattedItems,
            'totalItems' => $formattedItems->count(),
            'totalQuantity' => $totalQuantity,
            'subtotal' => $subtotal,
            'tax' => 0,
            'requiresShipping' => $requiresShipping,
            'is_downloadable' => $isDownloadable,
        ];
    }
}
