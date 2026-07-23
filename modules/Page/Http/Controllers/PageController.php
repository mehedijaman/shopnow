<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Page\Http\Requests\PageValidate;
use Modules\Page\Models\Page;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;
use Modules\Support\Traits\UploadFile;

class PageController extends BackendController
{
    use EditorImage, UploadFile;

    protected string $uploadImagePath = 'page';

    public function index(): Response
    {
        $pages = Page::orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($page) => [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->title,
                'image_url' => $page->image_url,
                'status' => $page->status,
                'published_at' => $page->published_at_formatted,
                'is_system' => $page->is_system,
            ]);

        return inertia('Page/PageIndex', [
            'pages' => $pages,
        ]);
    }

    public function create(): Response
    {
        return inertia('Page/PageForm');
    }

    public function store(PageValidate $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['image']);

        $page = Page::create($validated);

        if ($request->hasFile('image')) {
            $page->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()->route('page.edit', $page)
            ->with('success', 'page created.');
    }

    public function edit(int $id): Response
    {
        $page = Page::with(['parent'])->find($id);

        return inertia('Page/PageForm', [
            'page' => $page,
        ]);
    }

    public function update(PageValidate $request, int $id): RedirectResponse
    {
        $page = Page::findOrFail($id);

        $validated = $request->validated();
        unset($validated['image']);

        $page->update($validated);

        if ($request->hasFile('image')) {
            $page->addMediaFromRequest('image')->toMediaCollection('image');
        } elseif ($request->boolean('remove_previous_image')) {
            $page->clearMediaCollection('image');
        }

        return redirect()->route('page.index')
            ->with('success', 'page updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $page = Page::findOrFail($id);

        if ($page->is_system) {
            return redirect()->route('page.index')
                ->with('error', 'System pages cannot be deleted.');
        }

        $page->delete();

        return redirect()->route('page.index')
            ->with('success', 'Page deleted.');
    }

    public function recycleBin(): Response
    {
        $pages = Page::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($page) => [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->title,
                'image_url' => $page->image_url,
                'status' => $page->status,
                'published_at' => $page->published_at_formatted,
                'is_system' => $page->is_system,
                'deleted_at' => $page->deleted_at ? $page->deleted_at->format('d/m/Y') : null,
            ]);

        return inertia('Page/PageRecycleBin', [
            'pages' => $pages,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Page::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('page.recycleBin.index')
            ->with('success', 'Page Restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $page = Page::onlyTrashed()->findOrFail($id);

        $page->forceDelete();

        return redirect()->route('page.recycleBin.index')->with('success', 'Page deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $pages = Page::onlyTrashed()->get();

        foreach ($pages as $page) {
            $page->forceDelete();
        }

        return redirect()->route('page.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Page::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('page.recycleBin.index')
            ->with('success', 'Page Restored.');
    }
}
