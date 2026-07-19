<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Requests\ProductBundleItemValidate;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBundleItem;
use Modules\Product\Services\CalculateBundlePrice;
use Modules\Product\Services\ValidateBundleComposition;
use Modules\Support\Http\Controllers\BackendController;

class ProductBundleController extends BackendController
{
    public function config(Product $product): JsonResponse
    {
        $config = $product->bundleConfig;
        $items = $product->bundleItems()->with('childProduct')->orderBy('sort_order')->get();
        $allProducts = Product::where('id', '!=', $product->id)
            ->where('type', '!=', 'bundle')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'sale_price', 'type', 'quantity']);

        $pricePreview = null;
        if ($config) {
            $pricePreview = app(CalculateBundlePrice::class)->run($product);
        }

        return response()->json([
            'config' => $config ? [
                'id' => $config->id,
                'pricing_type' => $config->pricing_type,
                'discount_type' => $config->discount_type,
                'discount_value' => (float) $config->discount_value,
                'fixed_price' => (float) $config->fixed_price,
            ] : null,
            'items' => $items->map(fn ($i) => [
                'id' => $i->id,
                'child_product_id' => $i->child_product_id,
                'child_product_name' => $i->childProduct?->name ?? "Product #{$i->child_product_id}",
                'child_product_price' => $i->childProduct?->sale_price ?? $i->childProduct?->price ?? 0,
                'quantity' => $i->quantity,
                'is_optional' => $i->is_optional,
                'price_override' => (float) $i->price_override,
                'sort_order' => $i->sort_order,
            ]),
            'allProducts' => $allProducts,
            'price_preview' => $pricePreview,
        ]);
    }

    public function saveConfig(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'pricing_type' => 'required|in:calculated,fixed',
            'discount_type' => 'nullable|in:none,percentage,fixed_amount',
            'discount_value' => 'nullable|numeric|min:0',
            'fixed_price' => 'nullable|numeric|min:0',
        ]);

        $product->bundleConfig()->updateOrCreate(
            ['product_id' => $product->id],
            $validated
        );

        $pricePreview = app(CalculateBundlePrice::class)->run($product);

        return response()->json([
            'success' => true,
            'price_preview' => $pricePreview,
        ]);
    }

    public function addItem(ProductBundleItemValidate $request, Product $product, ValidateBundleComposition $validate): JsonResponse
    {
        $validate->run($product, [$request->input('child_product_id')]);

        $maxSort = $product->bundleItems()->max('sort_order') ?? 0;

        $item = $product->bundleItems()->create([
            'child_product_id' => $request->input('child_product_id'),
            'quantity' => $request->input('quantity', 1),
            'is_optional' => $request->boolean('is_optional', false),
            'price_override' => $request->input('price_override'),
            'sort_order' => $request->input('sort_order', $maxSort + 1),
        ]);

        $item->load('childProduct');

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'child_product_id' => $item->child_product_id,
                'child_product_name' => $item->childProduct?->name ?? "Product #{$item->child_product_id}",
                'child_product_price' => $item->childProduct?->sale_price ?? $item->childProduct?->price ?? 0,
                'quantity' => $item->quantity,
                'is_optional' => $item->is_optional,
                'price_override' => (float) $item->price_override,
                'sort_order' => $item->sort_order,
            ],
            'price_preview' => app(CalculateBundlePrice::class)->run($product),
        ], 201);
    }

    public function updateItem(ProductBundleItemValidate $request, Product $product, ProductBundleItem $item): JsonResponse
    {
        if ($item->bundle_product_id !== $product->id) {
            abort(404);
        }

        $item->update([
            'quantity' => $request->input('quantity', $item->quantity),
            'is_optional' => $request->boolean('is_optional', $item->is_optional),
            'price_override' => $request->input('price_override'),
            'sort_order' => $request->input('sort_order', $item->sort_order),
        ]);

        return response()->json([
            'success' => true,
            'item' => $item->fresh()->load('childProduct'),
            'price_preview' => app(CalculateBundlePrice::class)->run($product),
        ]);
    }

    public function removeItem(Product $product, ProductBundleItem $item): JsonResponse
    {
        if ($item->bundle_product_id !== $product->id) {
            abort(404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'price_preview' => app(CalculateBundlePrice::class)->run($product),
        ]);
    }

    public function reorder(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'integer|exists:product_bundle_items,id',
        ]);

        foreach ($validated['item_ids'] as $index => $itemId) {
            ProductBundleItem::where('id', $itemId)
                ->where('bundle_product_id', $product->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
