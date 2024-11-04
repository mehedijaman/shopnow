<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Product\Http\Requests\ProductCategoryValidate;
use Modules\Product\Models\ProductCategory;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;
use Modules\Support\Traits\UploadFile;

class ProductCategoryController extends BackendController
{
    use EditorImage, UploadFile;

    protected string $uploadImagePath = 'storage/app/public/product';

    public function index(): Response
    {
        $categories = ProductCategory::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($category) => [
                'id' => $category->id,
                'image_url' => $category->image_url,
                'name' => Str::limit($category->name, 50),
                'active' => $category->active,
                'featured' => $category->featured,
            ]);

        return inertia('ProductCategory/ProductCategoryIndex', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return inertia('ProductCategory/ProductCategoryForm');
    }

    public function store(ProductCategoryValidate $request)
    {
        $categoryData = $request->validated();

        if ($request->hasFile('image')) {
            $categoryData = array_merge($categoryData, $this->uploadFile('image', 'product-category', 'originalUUID', 'public'));
        }

        ProductCategory::create($categoryData);

        return redirect()->route('productCategory.index')
            ->with('success', 'Product Category created.');
    }

    public function edit(int $id): Response
    {
        $category = ProductCategory::find($id);

        return inertia('ProductCategory/ProductCategoryForm', [
            'category' => $category,
        ]);
    }

    public function update(ProductCategoryValidate $request, int $id): RedirectResponse
    {
        $category = ProductCategory::findOrFail($id);

        $categoryData = $request->validated();

        if ($request->hasFile('image')) {
            $categoryData = array_merge($categoryData, $this->uploadFile('image', 'product-category', 'originalUUID', 'public'));
        } elseif ($request->input('remove_previous_image')) {
            $categoryData['image'] = null;
        } else {
            unset($categoryData['image']);
        }

        $category->update($categoryData);

        return redirect()->route('productCategory.index')
            ->with('success', 'Product Category updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        ProductCategory::findOrFail($id)->delete();

        return redirect()->route('productCategory.index')
            ->with('success', 'Product Category deleted.');
    }

    public function recycleBin(): Response
    {
        $categories = ProductCategory::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($category) => [
                'id' => $category->id,
                'image_url' => $category->image_url,
                'name' => Str::limit($category->name, 50),
            ]);

        return inertia('ProductCategory/ProductCategoryRecycleBin', [
            'categories' => $categories,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        ProductCategory::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('productCategory.recycleBin.index')
            ->with('success', 'Product Category restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $category = ProductCategory::onlyTrashed()->findOrFail($id);

        $category->forceDelete();

        return redirect()->route('productCategory.recycleBin.index')->with('success', 'Product Category deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $categories = ProductCategory::onlyTrashed()->get();

        foreach ($categories as $category) {
            $category->forceDelete();
        }

        return redirect()->route('productCategory.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        ProductCategory::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('productCategory.recycleBin.index')
            ->with('success', 'Product Category restored.');
    }
}
