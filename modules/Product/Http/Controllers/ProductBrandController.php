<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Product\Http\Requests\ProductBrandValidate;
use Modules\Product\Models\ProductBrand;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;
use Modules\Support\Traits\UploadFile;

class ProductBrandController extends BackendController
{
    use EditorImage, UploadFile;

    protected string $uploadImagePath = 'storage/app/public/product';

    public function index(): Response
    {
        $brands = ProductBrand::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($brand) => [
                'id' => $brand->id,
                'image_url' => $brand->image_url,
                'name' => Str::limit($brand->name, 50),
                'active' => $brand->active,
                'featured' => $brand->featured,
            ]);

        return inertia('ProductBrand/ProductBrandIndex', [
            'brands' => $brands,
        ]);
    }

    public function create(): Response
    {
        return inertia('ProductBrand/ProductBrandForm');
    }

    public function store(ProductBrandValidate $request)
    {
        $brandData = $request->validated();

        if ($request->hasFile('image')) {
            $brandData = array_merge($brandData, $this->uploadFile('image', 'product-brand', 'originalUUID', 'public'));
        }

        ProductBrand::create($brandData);

        return redirect()->route('productBrand.index')
            ->with('success', 'Product Brand created.');
    }

    public function edit(int $id): Response
    {
        $brand = ProductBrand::find($id);

        return inertia('ProductBrand/ProductBrandForm', [
            'brand' => $brand,
        ]);
    }

    public function update(ProductBrandValidate $request, int $id): RedirectResponse
    {
        $brand = ProductBrand::findOrFail($id);

        $brandData = $request->validated();

        if ($request->hasFile('image')) {
            $brandData = array_merge($brandData, $this->uploadFile('image', 'product-brand', 'originalUUID', 'public'));
        } elseif ($request->input('remove_previous_image')) {
            $brandData['image'] = null;
        } else {
            unset($brandData['image']);
        }

        $brand->update($brandData);

        return redirect()->route('productBrand.index')
            ->with('success', 'Product Brand updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        ProductBrand::findOrFail($id)->delete();

        return redirect()->route('productBrand.index')
            ->with('success', 'Product Brand deleted.');
    }

    public function recycleBin(): Response
    {
        $brands = ProductBrand::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($brand) => [
                'id' => $brand->id,
                'image_url' => $brand->image_url,
                'name' => Str::limit($brand->name, 50),
            ]);

        return inertia('ProductBrand/ProductBrandRecycleBin', [
            'brands' => $brands,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        ProductBrand::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('productBrand.recycleBin.index')
            ->with('success', 'Product Brand restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $brand = ProductBrand::onlyTrashed()->findOrFail($id);

        $brand->forceDelete();

        return redirect()->route('productBrand.recycleBin.index')->with('success', 'Product Brand deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $brands = ProductBrand::onlyTrashed()->get();

        foreach ($brands as $brand) {
            $brand->forceDelete();
        }

        return redirect()->route('productBrand.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        ProductBrand::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('productBrand.recycleBin.index')
            ->with('success', 'Product Brand restored.');
    }
}
