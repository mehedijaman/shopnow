<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Product\Http\Requests\ProductTagValidate;
use Modules\Product\Models\ProductTag;
use Modules\Support\Http\Controllers\BackendController;

class ProductTagController extends BackendController
{
    public function index(): Response
    {
        $tags = ProductTag::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($tag) => [
                'id' => $tag->id,
                'name' => Str::limit($tag->name, 50),
            ]);

        return inertia('ProductTag/ProductTagIndex', [
            'tags' => $tags,
        ]);
    }

    public function create(): Response
    {
        return inertia('ProductTag/ProductTagForm');
    }

    public function store(ProductTagValidate $request): RedirectResponse
    {
        ProductTag::create($request->validated());

        return redirect()->route('blogTag.index')
            ->with('success', 'Tag created.');
    }

    public function edit(int $id): Response
    {
        $tag = ProductTag::find($id);

        return inertia('ProductTag/ProductTagForm', [
            'tag' => $tag,
        ]);
    }

    public function update(ProductTagValidate $request, int $id): RedirectResponse
    {
        $tag = ProductTag::findOrFail($id);

        $tag->update($request->validated());

        return redirect()->route('blogTag.index')
            ->with('success', 'Tag updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        ProductTag::findOrFail($id)->delete();

        return redirect()->route('blogTag.index')
            ->with('success', 'Tag deleted.');
    }

    public function recycleBin(): Response
    {
        $tags = ProductTag::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($tag) => [
                'id' => $tag->id,
                'name' => Str::limit($tag->name, 50),
            ]);

        return inertia('Product/ProductTagRecycleBin', [
            'tags' => $tags,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        ProductTag::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('productTag.recycleBin.index')
            ->with('success', 'tag restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $tag = ProductTag::onlyTrashed()->findOrFail($id);

        $tag->forceDelete();

        return redirect()->route('productTag.recycleBin.index')->with('success', 'tag deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $tags = ProductTag::onlyTrashed()->get();

        foreach ($tags as $tag) {
            $tag->forceDelete();
        }

        return redirect()->route('productTag.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        ProductTag::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('productTag.recycleBin.index')
            ->with('success', 'tag restored.');
    }
}
