<?php

namespace Modules\Product\Http\Controllers;

use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Modules\Support\Traits\UploadFile;
use Modules\Support\Traits\EditorImage;
use Modules\Product\Models\ProductCategory;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Produdct\Http\Requests\ProductCategoryValidate;
use Modules\Product\Http\Requests\ProductProductCategoryValidate;

class ProductCategoryController extends BackendController
{
    use EditorImage, UploadFile;

    public function index(): Response
    {
        $categories = ProductCategory::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn($category) => [
                'id' => $category->id,
                'image_url' => $category->image_url,
                'name' => Str::limit($category->name, 50),
                'active' => $category->active,
            ]);

        return inertia('Product/ProductCategoryIndex', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return inertia('Product/ProductCategoryForm');
    }

    public function store(ProductCategoryValidate $request): RedirectResponse
    {

        $categoryData = $request->validated();

        if ($request->hasFile('image')) {
            $categoryData = array_merge($categoryData, $this->uploadFile('image', 'blog', 'originalUUID', 'public'));
        }

        ProductCategory::create($categoryData);

        return redirect()->route('blogCategory.index')
            ->with('success', 'Category created.');
    }

    public function edit(int $id): Response
    {
        $category = ProductCategory::find($id);

        return inertia('Product/ProductCategoryForm', [
            'category' => $category,
        ]);
    }

    public function update(ProductCategoryValidate $request, int $id): RedirectResponse
    {
        $category = ProductCategory::findOrFail($id);

        $categoryData = $request->validated();

        if ($request->hasFile('image')) {
            $categoryData = array_merge($categoryData, $this->uploadFile('image', 'blog', 'originalUUID', 'public'));
        } elseif ($request->input('remove_previous_image')) {
            $categoryData['image'] = null;
        } else {
            unset($categoryData['image']);
        }

        $category->update($categoryData);

        return redirect()->route('blogCategory.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        ProductCategory::findOrFail($id)->delete();

        return redirect()->route('blogCategory.index')
            ->with('success', 'Category deleted.');
    }

    public function recycleBin(): Response
    {
        $categories = ProductCategory::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn($category) => [
                'id' => $category->id,
                'image_url' => $category->image_url,
                'name' => Str::limit($category->name, 50),
            ]);

        return inertia('Product/ProductCategoryRecycleBin', [
            'categories' => $categories,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        ProductCategory::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('productCategory.recycleBin.index')
            ->with('success', 'Category restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $category = ProductCategory::onlyTrashed()->findOrFail($id);

        $category->forceDelete();

        return redirect()->route('productCategory.recycleBin.index')->with('success', 'Category deleted.');
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
            ->with('success', 'Category restored.');
    }
}
