<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Blog\Http\Requests\CategoryValidate;
use Modules\Blog\Models\Category;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;
use Modules\Support\Traits\UploadFile;

class CategoryController extends BackendController
{
    use EditorImage, UploadFile;

    public function index(): Response
    {
        $categories = Category::withCount('posts')
            ->orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($category) => [
                'id' => $category->id,
                'image_url' => $category->image_url,
                'name' => Str::limit($category->name, 50),
                'is_visible' => $category->is_visible,
                'posts_count' => $category->posts_count,
            ]);

        return inertia('BlogCategory/CategoryIndex', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return inertia('BlogCategory/CategoryForm');
    }

    public function store(CategoryValidate $request): RedirectResponse
    {
        $categoryData = $request->validated();
        unset($categoryData['image']);

        $category = Category::create($categoryData);

        if ($request->hasFile('image')) {
            $category->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()->route('blogCategory.index')
            ->with('success', 'Category created.');
    }

    public function edit(int $id): Response
    {
        $category = Category::find($id);

        return inertia('BlogCategory/CategoryForm', [
            'category' => $category,
        ]);
    }

    public function update(CategoryValidate $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $categoryData = $request->validated();
        unset($categoryData['image']);

        $category->update($categoryData);

        if ($request->hasFile('image')) {
            $category->addMediaFromRequest('image')->toMediaCollection('image');
        } elseif ($request->boolean('remove_previous_image')) {
            $category->clearMediaCollection('image');
        }

        return redirect()->route('blogCategory.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        if ($category->posts()->count() > 0) {
            return redirect()->route('blogCategory.index')
                ->with('error', 'Cannot delete category that has posts.');
        }

        $category->delete();

        return redirect()->route('blogCategory.index')
            ->with('success', 'Category deleted.');
    }

    public function recycleBin(): Response
    {
        $categories = Category::onlyTrashed()
            ->orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($category) => [
                'id' => $category->id,
                'image_url' => $category->image_url,
                'name' => Str::limit($category->name, 50),
                'is_visible' => $category->is_visible,
                'deleted_at' => $category->deleted_at ? $category->deleted_at->format('d/m/Y') : null,
            ]);

        return inertia('BlogCategory/CategoryRecycleBin', [
            'categories' => $categories,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Category::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('blogCategory.recycleBin.index')
            ->with('success', 'Category restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();

        return redirect()->route('blogCategory.recycleBin.index')
            ->with('success', 'Category deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $categories = Category::onlyTrashed()->get();

        foreach ($categories as $category) {
            $category->forceDelete();
        }

        return redirect()->route('blogCategory.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Category::onlyTrashed()->restore();

        return redirect()->route('blogCategory.recycleBin.index')
            ->with('success', 'Categories restored.');
    }
}
