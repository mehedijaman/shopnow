<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Product\Models\ProductAttribute;
use Modules\Product\Models\ProductAttributeValue;
use Modules\Support\Http\Controllers\BackendController;

class ProductAttributeController extends BackendController
{
    public function index(): Response
    {
        $attributes = ProductAttribute::with('values')->orderBy('name')->get();

        return inertia('ProductAttribute/ProductAttributeIndex', [
            'attributes' => $attributes,
        ]);
    }

    public function create(): Response
    {
        return inertia('ProductAttribute/ProductAttributeForm', [
            'attribute' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name',
            'input_type' => 'required|in:select,color,image',
            'values' => 'nullable|array',
            'values.*.value' => 'required|string|max:255',
            'values.*.swatch' => 'nullable|string|max:255',
        ]);

        $attribute = ProductAttribute::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'input_type' => $validated['input_type'],
        ]);

        if (! empty($validated['values'])) {
            foreach ($validated['values'] as $i => $val) {
                $attribute->values()->create([
                    'value' => $val['value'],
                    'slug' => Str::slug($val['value']),
                    'swatch' => $val['swatch'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('productAttribute.index')
            ->with('success', 'Attribute "'.$attribute->name.'" created.');
    }

    public function edit(ProductAttribute $productAttribute): Response
    {
        $productAttribute->load('values');

        return inertia('ProductAttribute/ProductAttributeForm', [
            'attribute' => $productAttribute,
        ]);
    }

    public function update(Request $request, ProductAttribute $productAttribute): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name,'.$productAttribute->id,
            'input_type' => 'required|in:select,color,image',
            'values' => 'nullable|array',
            'values.*.id' => 'nullable|integer',
            'values.*.value' => 'required|string|max:255',
            'values.*.swatch' => 'nullable|string|max:255',
        ]);

        $productAttribute->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'input_type' => $validated['input_type'],
        ]);

        // Sync values: keep existing, add new, remove missing
        $submittedIds = collect($validated['values'] ?? [])
            ->pluck('id')
            ->filter()
            ->toArray();

        // Remove values not in the submitted list
        $productAttribute->values()
            ->whereNotIn('id', $submittedIds)
            ->delete();

        foreach ($validated['values'] ?? [] as $i => $val) {
            if (! empty($val['id'])) {
                // Update existing
                ProductAttributeValue::where('id', $val['id'])
                    ->where('product_attribute_id', $productAttribute->id)
                    ->update([
                        'value' => $val['value'],
                        'slug' => Str::slug($val['value']),
                        'swatch' => $val['swatch'] ?? null,
                        'sort_order' => $i,
                    ]);
            } else {
                // Create new
                $productAttribute->values()->create([
                    'value' => $val['value'],
                    'slug' => Str::slug($val['value']),
                    'swatch' => $val['swatch'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('productAttribute.index')
            ->with('success', 'Attribute "'.$productAttribute->name.'" updated.');
    }

    public function destroy(ProductAttribute $productAttribute): RedirectResponse
    {
        $productAttribute->delete();

        return redirect()->route('productAttribute.index')
            ->with('success', 'Attribute deleted.');
    }

    /**
     * AJAX endpoint: create an attribute inline from the product form.
     */
    public function storeAjax(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name',
            'input_type' => 'nullable|in:select,color,image',
            'values' => 'nullable|array',
            'values.*.value' => 'required|string|max:255',
            'values.*.swatch' => 'nullable|string|max:255',
        ]);

        $attribute = ProductAttribute::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'input_type' => $validated['input_type'] ?? 'select',
        ]);

        if (! empty($validated['values'])) {
            foreach ($validated['values'] as $i => $val) {
                $attribute->values()->create([
                    'value' => $val['value'],
                    'slug' => Str::slug($val['value']),
                    'swatch' => $val['swatch'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        return response()->json([
            'data' => $attribute->load('values'),
        ], 201);
    }

    /**
     * AJAX endpoint: list all attributes (for refreshing after inline creation).
     */
    public function indexAjax(): JsonResponse
    {
        $attributes = ProductAttribute::with('values')->orderBy('name')->get();

        return response()->json(['data' => $attributes]);
    }
}
