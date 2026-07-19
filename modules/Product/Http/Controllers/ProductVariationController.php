<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Requests\ProductVariationValidate;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductAttribute;
use Modules\Product\Models\ProductVariation;
use Modules\Product\Services\GenerateProductVariations;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\UploadFile;

class ProductVariationController extends BackendController
{
    use UploadFile;

    protected string $uploadImagePath = 'storage/app/public/product';

    public function index(): JsonResponse
    {
        $attributes = ProductAttribute::with('values')->orderBy('name')->get();

        return response()->json(['data' => $attributes]);
    }

    public function storeAttribute(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'input_type' => 'nullable|in:select,color,image',
            'values' => 'nullable|array',
            'values.*.value' => 'required|string|max:255',
            'values.*.swatch' => 'nullable|string|max:255',
        ]);

        $attribute = ProductAttribute::create([
            'name' => $validated['name'],
            'input_type' => $validated['input_type'] ?? 'select',
        ]);

        if (! empty($validated['values'])) {
            foreach ($validated['values'] as $i => $val) {
                $attribute->values()->create([
                    'value' => $val['value'],
                    'swatch' => $val['swatch'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        return response()->json(['data' => $attribute->load('values')], 201);
    }

    public function showAttribute(ProductAttribute $attribute): JsonResponse
    {
        return response()->json(['data' => $attribute->load('values')]);
    }

    public function updateAttribute(Request $request, ProductAttribute $attribute): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'input_type' => 'nullable|in:select,color,image',
        ]);

        $attribute->update($validated);

        return response()->json(['data' => $attribute->fresh()]);
    }

    public function destroyAttribute(ProductAttribute $attribute): JsonResponse
    {
        $attribute->delete();

        return response()->json(['success' => true]);
    }

    public function attributes(Product $product): JsonResponse
    {
        $attributes = ProductAttribute::with(['values' => function ($q) use ($product) {
            $q->whereIn('id', $product->attributeValues()->pluck('product_attribute_values.id'));
        }])->whereHas('values', function ($q) use ($product) {
            $q->whereIn('id', $product->attributeValues()->pluck('product_attribute_values.id'));
        })->get()->map(fn ($attr) => [
            'id' => $attr->id,
            'name' => $attr->name,
            'input_type' => $attr->input_type,
            'values' => $attr->values->map(fn ($v) => [
                'id' => $v->id,
                'value' => $v->value,
                'swatch' => $v->swatch,
            ]),
        ]);

        $variations = $product->variations()->with('attributeValues')->get()->map(fn ($v) => [
            'id' => $v->id,
            'sku' => $v->sku,
            'price' => (float) $v->price,
            'sale_price' => (float) $v->sale_price,
            'quantity' => $v->quantity,
            'active' => $v->active,
            'variation_key' => $v->variation_key,
            'attribute_value_ids' => $v->attributeValues->pluck('id'),
            'image_url' => $v->getFirstMediaUrl('image'),
        ]);

        return response()->json([
            'attributes' => $attributes,
            'variations' => $variations,
        ]);
    }

    public function generate(Request $request, Product $product, GenerateProductVariations $generate): JsonResponse
    {
        $validated = $request->validate([
            'attribute_value_ids' => 'required|array',
            'attribute_value_ids.*' => 'required|array',
            'attribute_value_ids.*.*' => 'integer|exists:product_attribute_values,id',
        ]);

        $generate->run($product, $validated['attribute_value_ids']);

        return $this->attributes($product);
    }

    public function update(
        ProductVariationValidate $request,
        Product $product,
        ProductVariation $variation,
    ): JsonResponse {
        $data = $request->validated();

        if ($variation->product_id !== $product->id) {
            abort(404);
        }

        if ($request->hasFile('image')) {
            $variation->addMedia($request->file('image'))->toMediaCollection('image');
        } elseif ($request->input('remove_image')) {
            $variation->clearMediaCollection('image');
        }

        unset($data['image'], $data['remove_image']);
        $variation->update($data);
        $variation->refresh();

        return response()->json([
            'success' => true,
            'variation' => [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'price' => (float) $variation->price,
                'sale_price' => (float) $variation->sale_price,
                'quantity' => $variation->quantity,
                'active' => $variation->active,
                'variation_key' => $variation->variation_key,
                'attribute_value_ids' => $variation->attributeValues->pluck('id'),
                'image_url' => $variation->getFirstMediaUrl('image'),
            ],
        ]);
    }

    public function destroy(Product $product, ProductVariation $variation): JsonResponse
    {
        if ($variation->product_id !== $product->id) {
            abort(404);
        }

        $variation->delete();

        return response()->json(['success' => true]);
    }
}
