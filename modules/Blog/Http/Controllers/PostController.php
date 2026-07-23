<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Blog\Http\Requests\PostValidate;
use Modules\Blog\Models\Post;
use Modules\Blog\Services\GetAuthorOptions;
use Modules\Blog\Services\GetCategoryOptions;
use Modules\Blog\Services\GetTagOptions;
use Modules\Blog\Services\SyncPostTags;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;
use Modules\Support\Traits\UploadFile;

class PostController extends BackendController
{
    use EditorImage, UploadFile;

    protected string $uploadImagePath = 'storage/app/public/blog';

    public function index(): Response
    {
        $posts = Post::orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($post) => [
                'id' => $post->id,
                'image_url' => $post->image_url,
                'title' => $post->title,
                'status' => $post->status,
            ]);

        return inertia('BlogPost/PostIndex', [
            'posts' => $posts,
        ]);
    }

    public function create(GetCategoryOptions $getCategoryOptions, GetTagOptions $getTagOptions, GetAuthorOptions $getAuthorOptions): Response
    {
        return inertia('BlogPost/PostForm', [
            'categories' => $getCategoryOptions->get(),
            'tags' => $getTagOptions->get(),
            'authors' => $getAuthorOptions->get(),
        ]);
    }

    public function store(PostValidate $request, SyncPostTags $syncPostTags): RedirectResponse
    {
        $postData = $request->validated();
        unset($postData['image']);

        $post = Post::create($postData);

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('image');
        }

        if (is_array($request->input('tags')) and count($request->input('tags'))) {
            $syncPostTags->sync($post, $request->input('tags'));
        }

        return redirect()->route('blogPost.index')
            ->with('success', 'Post created.');
    }

    public function edit(GetCategoryOptions $getCategoryOptions, GetTagOptions $getTagOptions, GetAuthorOptions $getAuthorOptions, int $id): Response
    {
        return inertia('BlogPost/PostForm', [
            'post' => Post::with(['tags' => function ($query) {
                $query->select('blog_tags.id', 'blog_tags.name');
            }])->find($id),
            'categories' => $getCategoryOptions->get(),
            'tags' => $getTagOptions->get(),
            'authors' => $getAuthorOptions->get(),
        ]);
    }

    public function update(PostValidate $request, SyncPostTags $syncPostTags, int $id): RedirectResponse
    {
        $post = Post::findOrFail($id);

        $postData = $request->validated();
        unset($postData['image']);

        $post->update($postData);

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('image');
        } elseif ($request->boolean('remove_previous_image')) {
            $post->clearMediaCollection('image');
        }

        if ($request->has('tagsHasChanged')) {
            $syncPostTags->sync($post, $request->input('tags'));
        }

        return redirect()->route('blogPost.index')
            ->with('success', 'Post updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Post::findOrFail($id)->delete();

        return redirect()->route('blogPost.index')
            ->with('success', 'Post deleted.');
    }

    public function recycleBin(): Response
    {
        $posts = Post::onlyTrashed()
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($post) => [
                'id' => $post->id,
                'image_url' => $post->image_url,
                'title' => $post->title,
                'status' => $post->status,
                'deleted_at' => $post->deleted_at ? $post->deleted_at->format('d/m/Y') : null,
            ]);

        return inertia('BlogPost/PostRecycleBin', [
            'posts' => $posts,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Post::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('blogPost.recycleBin.index')
            ->with('success', 'Post restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->forceDelete();

        return redirect()->route('blogPost.recycleBin.index')
            ->with('success', 'Post deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $posts = Post::onlyTrashed()->get();

        foreach ($posts as $post) {
            $post->forceDelete();
        }

        return redirect()->route('blogPost.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Post::onlyTrashed()->restore();

        return redirect()->route('blogPost.recycleBin.index')
            ->with('success', 'Posts restored.');
    }
}
