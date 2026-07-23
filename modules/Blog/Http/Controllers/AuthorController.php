<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Blog\Http\Requests\AuthorValidate;
use Modules\Blog\Models\Author;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\UploadFile;

class AuthorController extends BackendController
{
    use UploadFile;

    public function index(): Response
    {
        $authors = Author::withCount('posts')
            ->orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($author) => [
                'id' => $author->id,
                'image_url' => $author->image_url,
                'name' => Str::limit($author->name, 50),
                'email' => $author->email,
                'github_handle' => $author->github_handle,
                'twitter_handle' => $author->twitter_handle,
                'posts_count' => $author->posts_count,
            ]);

        return inertia('BlogAuthor/AuthorIndex', [
            'authors' => $authors,
        ]);
    }

    public function create(): Response
    {
        return inertia('BlogAuthor/AuthorForm');
    }

    public function store(AuthorValidate $request): RedirectResponse
    {
        $authorData = $request->validated();
        unset($authorData['image']);

        $author = Author::create($authorData);

        if ($request->hasFile('image')) {
            $author->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()->route('blogAuthor.index')
            ->with('success', 'Author created.');
    }

    public function edit(int $id): Response
    {
        $author = Author::find($id);

        return inertia('BlogAuthor/AuthorForm', [
            'author' => $author,
        ]);
    }

    public function update(AuthorValidate $request, int $id): RedirectResponse
    {
        $author = Author::findOrFail($id);

        $authorData = $request->validated();
        unset($authorData['image']);

        $author->update($authorData);

        if ($request->hasFile('image')) {
            $author->addMediaFromRequest('image')->toMediaCollection('image');
        } elseif ($request->boolean('remove_previous_image')) {
            $author->clearMediaCollection('image');
        }

        return redirect()->route('blogAuthor.index')
            ->with('success', 'Author updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $author = Author::findOrFail($id);

        if ($author->posts()->count() > 0) {
            return redirect()->route('blogAuthor.index')
                ->with('error', 'Cannot delete author that has posts.');
        }

        $author->delete();

        return redirect()->route('blogAuthor.index')
            ->with('success', 'Author deleted.');
    }

    public function recycleBin(): Response
    {
        $authors = Author::onlyTrashed()
            ->orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($author) => [
                'id' => $author->id,
                'image_url' => $author->image_url,
                'name' => Str::limit($author->name, 50),
                'email' => $author->email,
                'github_handle' => $author->github_handle,
                'twitter_handle' => $author->twitter_handle,
                'deleted_at' => $author->deleted_at ? $author->deleted_at->format('d/m/Y') : null,
            ]);

        return inertia('BlogAuthor/AuthorRecycleBin', [
            'authors' => $authors,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Author::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('blogAuthor.recycleBin.index')
            ->with('success', 'Author restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $author = Author::onlyTrashed()->findOrFail($id);
        $author->forceDelete();

        return redirect()->route('blogAuthor.recycleBin.index')
            ->with('success', 'Author deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $authors = Author::onlyTrashed()->get();

        foreach ($authors as $author) {
            $author->forceDelete();
        }

        return redirect()->route('blogAuthor.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Author::onlyTrashed()->restore();

        return redirect()->route('blogAuthor.recycleBin.index')
            ->with('success', 'Authors restored.');
    }
}
