<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Modules\Product\Http\Requests\ProductFileValidate;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductFile;
use Modules\Support\Http\Controllers\BackendController;

class ProductFileController extends BackendController
{
    public function store(ProductFileValidate $request, Product $product): RedirectResponse
    {
        $media = $product->addMedia($request->file('file'))
            ->toMediaCollection('downloads');

        ProductFile::create([
            'product_id' => $product->id,
            'media_id' => $media->id,
            'name' => $request->input('name'),
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return redirect()->route('product.edit', $product->id)
            ->with('success', 'Downloadable file added.');
    }

    public function destroy(Product $product, ProductFile $file): RedirectResponse
    {
        if ($file->media) {
            $file->media->delete();
        }

        $file->delete();

        return redirect()->route('product.edit', $product->id)
            ->with('success', 'Downloadable file removed.');
    }
}
