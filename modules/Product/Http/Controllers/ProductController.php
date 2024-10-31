<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Product\Http\Requests\ProductValidate;
use Modules\Product\Models\Product;
use Modules\Product\Services\GetProductBrandOptions;
use Modules\Product\Services\GetProductCategoryOptions;
use Modules\Product\Services\GetProductTagOptions;
use Modules\Product\Services\SyncProductTags;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;
use Modules\Support\Traits\UploadFile;

class ProductController extends BackendController
{
    use EditorImage, UploadFile;

    protected string $uploadImagePath = 'storage/app/public/product';

    public function index(): Response
    {
        $products = Product::orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($product) => [
                'id' => $product->id,
                'image_url' => $product->image_url,
                'name' => $product->name,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'quantity' => $product->quantity,
                'unit' => $product->unit,
                'min_order' => $product->min_order,
                'active' => $product->active,
                'featured' => $product->featured,
            ]);

        return inertia('Product/ProductIndex', [
            'products' => $products,
        ]);
    }

    public function create(GetProductCategoryOptions $getProductCategoryOptions, GetProductTagOptions $getProductTagOptions, GetProductBrandOptions $getProductBrandOptions): Response
    {
        return inertia('Product/ProductForm', [
            'categories' => $getProductCategoryOptions->get(),
            'tags' => $getProductTagOptions->get(),
            // 'authors' => $getProductBrandOptions->get(),
        ]);
    }

    public function store(ProductValidate $request, SyncProductTags $syncProductTags): RedirectResponse
    {
        $productData = $request->validated();

        if ($request->hasFile('image')) {
            $productData = array_merge($productData, $this->uploadFile('image', 'product', 'originalUUID', 'public'));
        }

        $product = Product::create($productData);

        if (is_array($request->input('tags')) and count($request->input('tags'))) {
            $syncProductTags->sync($product, $request->input('tags'));
        }

        return redirect()->route('product.index')
            ->with('success', 'Product created.');
    }

    public function edit(GetProductCategoryOptions $getProductCategoryOptions, GetProductTagOptions $getProductTagOptions, GetProductBrandOptions $getProductBrandOptions, int $id): Response
    {
        return inertia('Product/ProductForm', [
            'product' => Product::with(['tags' => function ($query) {
                $query->select('product_tags.id', 'product_tags.name');
            }])->find($id),
            'categories' => $getProductCategoryOptions->get(),
            'tags' => $getProductTagOptions->get(),
            // 'authors' => $getProductBrandOptions->get(),
        ]);
    }

    public function update(ProductValidate $request, SyncProductTags $syncProductTags, int $id): RedirectResponse
    {

        $product = Product::findOrFail($id);

        $productData = $request->validated();

        if ($request->hasFile('image')) {
            $productData = array_merge($productData, $this->uploadFile('image', 'blog', 'originalUUID', 'public'));
        } elseif ($request->input('remove_previous_image')) {
            $productData['image'] = null;
        } else {
            unset($productData['image']);
        }

        $product->update($productData);

        if ($request->has('tagsHasChanged')) {
            $syncProductTags->sync($product, $request->input('tags'));
        }

        return redirect()->route('product.index')
            ->with('success', 'Product updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('product.index')
            ->with('success', 'Product deleted.');
    }

    public function recycleBin(): Response
    {
        $products = Product::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($product) => [
                'id' => $product->id,
                'name' => Str::limit($product->name, 50),
            ]);

        return inertia('Product/ProductRecycleBin', [
            'products' => $products,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Product::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('product.recycleBin.index')
            ->with('success', 'Product restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $product = Product::onlyTrashed()->findOrFail($id);

        $product->forceDelete();

        return redirect()->route('product.recycleBin.index')->with('success', 'Product deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $products = Product::onlyTrashed()->get();

        foreach ($products as $product) {
            $product->forceDelete();
        }

        return redirect()->route('product.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Product::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('product.recycleBin.index')
            ->with('success', 'Product restored.');
    }
}
