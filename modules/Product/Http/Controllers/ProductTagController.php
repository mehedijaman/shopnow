<?php

namespace Modules\Product\Http\Controllers;

use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Modules\Product\Models\ProductTag;
use Modules\Support\Traits\UploadFile;
use Modules\Support\Traits\EditorImage;
use Modules\Product\Http\Requests\ProductTagValidate;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Product\Http\Requests\ProductProductTagValidate;

class ProductTagController extends BackendController
{
    use EditorImage, UploadFile;

    public function index(): Response
    {
        $categories = ProductTag::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn($category) => [
                'id' => $category->id,
                'name' => Str::limit($category->name, 50),
                'active' => $category->active,
            ]);

        return inertia('Product/ProductTagIndex', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return inertia('Product/ProductTagForm');
    }

    public function store(ProductTagValidate $request): RedirectResponse
    {

        $categoryData = $request->validated();

        if ($request->hasFile('image')) {
            $categoryData = array_merge($categoryData, $this->uploadFile('image', 'blog', 'originalUUID', 'public'));
        }

        ProductTag::create($categoryData);

        return redirect()->route('blogCategory.index')
            ->with('success', 'Category created.');
    }

    public function edit(int $id): Response
    {
        $category = ProductTag::find($id);

        return inertia('Product/ProductTagForm', [
            'category' => $category,
        ]);
    }

    public function update(ProductTagValidate $request, int $id): RedirectResponse
    {
        $category = ProductTag::findOrFail($id);

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
        ProductTag::findOrFail($id)->delete();

        return redirect()->route('blogCategory.index')
            ->with('success', 'Category deleted.');
    }

    public function recycleBin(): Response
    {
        $categories = ProductTag::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn($category) => [
                'id' => $category->id,
                'name' => Str::limit($category->name, 50),
            ]);

        return inertia('Product/ProductTagRecycleBin', [
            'categories' => $categories,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        ProductTag::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('productTag.recycleBin.index')
            ->with('success', 'Category restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $category = ProductTag::onlyTrashed()->findOrFail($id);

        $category->forceDelete();

        return redirect()->route('productTag.recycleBin.index')->with('success', 'Category deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $categories = ProductTag::onlyTrashed()->get();

        foreach ($categories as $category) {
            $category->forceDelete();
        }

        return redirect()->route('productTag.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        ProductTag::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('productTag.recycleBin.index')
            ->with('success', 'Category restored.');
    }
}
