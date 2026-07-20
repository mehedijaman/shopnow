<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\AuthorController;
use Modules\Blog\Http\Controllers\CategoryController;
use Modules\Blog\Http\Controllers\PostController;
use Modules\Blog\Http\Controllers\TagController;

// Posts
Route::prefix('blog-post')->name('blogPost.')->group(function () {
    Route::post('upload-editor-image', [PostController::class, 'uploadEditorImage'])
        ->name('uploadEditorImage')
        ->can('Blog: Post - Create')
        ->can('Blog: Post - Edit');

    Route::get('/', [PostController::class, 'index'])
        ->name('index')
        ->can('Blog: Post - List');

    Route::get('create', [PostController::class, 'create'])
        ->name('create')
        ->can('Blog: Post - Create');

    Route::post('/', [PostController::class, 'store'])
        ->name('store')
        ->can('Blog: Post - Create');

    // Recycle Bin Routes
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [PostController::class, 'recycleBin'])
            ->name('index')
            ->can('Blog: Post - Recycle Bin List');

        Route::get('{id}/restore', [PostController::class, 'restore'])
            ->name('restore')
            ->can('Blog: Post - Recycle Bin Restore');

        Route::delete('{id}/destroy', [PostController::class, 'destroyForce'])
            ->name('destroyForce')
            ->can('Blog: Post - Recycle Bin Delete');

        Route::delete('empty', [PostController::class, 'emptyRecycleBin'])
            ->name('empty')
            ->can('Blog: Post - Recycle Bin Delete');

        Route::get('restore', [PostController::class, 'restoreRecycleBin'])
            ->name('restoreAll')
            ->can('Blog: Post - Recycle Bin Restore');
    });

    Route::get('{id}/edit', [PostController::class, 'edit'])
        ->name('edit')
        ->can('Blog: Post - Edit');

    Route::put('{id}', [PostController::class, 'update'])
        ->name('update')
        ->can('Blog: Post - Edit');

    Route::delete('{id}', [PostController::class, 'destroy'])
        ->name('destroy')
        ->can('Blog: Post - Delete');
});

// Categories
Route::prefix('blog-category')->name('blogCategory.')->group(function () {
    Route::post('upload-editor-image', [CategoryController::class, 'uploadEditorImage'])
        ->name('uploadEditorImage')
        ->can('Blog: Category - Create')
        ->can('Blog: Category - Edit');

    Route::get('/', [CategoryController::class, 'index'])
        ->name('index')
        ->can('Blog: Category - List');

    Route::get('create', [CategoryController::class, 'create'])
        ->name('create')
        ->can('Blog: Category - Create');

    Route::post('/', [CategoryController::class, 'store'])
        ->name('store')
        ->can('Blog: Category - Create');

    // Recycle Bin Routes
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [CategoryController::class, 'recycleBin'])
            ->name('index')
            ->can('Blog: Category - Recycle Bin List');

        Route::get('{id}/restore', [CategoryController::class, 'restore'])
            ->name('restore')
            ->can('Blog: Category - Recycle Bin Restore');

        Route::delete('{id}/destroy', [CategoryController::class, 'destroyForce'])
            ->name('destroyForce')
            ->can('Blog: Category - Recycle Bin Delete');

        Route::delete('empty', [CategoryController::class, 'emptyRecycleBin'])
            ->name('empty')
            ->can('Blog: Category - Recycle Bin Delete');

        Route::get('restore', [CategoryController::class, 'restoreRecycleBin'])
            ->name('restoreAll')
            ->can('Blog: Category - Recycle Bin Restore');
    });

    Route::get('{id}/edit', [CategoryController::class, 'edit'])
        ->name('edit')
        ->can('Blog: Category - Edit');

    Route::put('{id}', [CategoryController::class, 'update'])
        ->name('update')
        ->can('Blog: Category - Edit');

    Route::delete('{id}', [CategoryController::class, 'destroy'])
        ->name('destroy')
        ->can('Blog: Category - Delete');
});

// Tags
Route::prefix('blog-tag')->name('blogTag.')->group(function () {
    Route::get('/', [TagController::class, 'index'])
        ->name('index')
        ->can('Blog: Tag - List');

    Route::get('create', [TagController::class, 'create'])
        ->name('create')
        ->can('Blog: Tag - Create');

    Route::post('/', [TagController::class, 'store'])
        ->name('store')
        ->can('Blog: Tag - Create');

    // Recycle Bin Routes
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [TagController::class, 'recycleBin'])
            ->name('index')
            ->can('Blog: Tag - Recycle Bin List');

        Route::get('{id}/restore', [TagController::class, 'restore'])
            ->name('restore')
            ->can('Blog: Tag - Recycle Bin Restore');

        Route::delete('{id}/destroy', [TagController::class, 'destroyForce'])
            ->name('destroyForce')
            ->can('Blog: Tag - Recycle Bin Delete');

        Route::delete('empty', [TagController::class, 'emptyRecycleBin'])
            ->name('empty')
            ->can('Blog: Tag - Recycle Bin Delete');

        Route::get('restore', [TagController::class, 'restoreRecycleBin'])
            ->name('restoreAll')
            ->can('Blog: Tag - Recycle Bin Restore');
    });

    Route::get('{id}/edit', [TagController::class, 'edit'])
        ->name('edit')
        ->can('Blog: Tag - Edit');

    Route::put('{id}', [TagController::class, 'update'])
        ->name('update')
        ->can('Blog: Tag - Edit');

    Route::delete('{id}', [TagController::class, 'destroy'])
        ->name('destroy')
        ->can('Blog: Tag - Delete');
});

// Authors
Route::prefix('blog-author')->name('blogAuthor.')->group(function () {
    Route::get('/', [AuthorController::class, 'index'])
        ->name('index')
        ->can('Blog: Author - List');

    Route::get('create', [AuthorController::class, 'create'])
        ->name('create')
        ->can('Blog: Author - Create');

    Route::post('/', [AuthorController::class, 'store'])
        ->name('store')
        ->can('Blog: Author - Create');

    // Recycle Bin Routes
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [AuthorController::class, 'recycleBin'])
            ->name('index')
            ->can('Blog: Author - Recycle Bin List');

        Route::get('{id}/restore', [AuthorController::class, 'restore'])
            ->name('restore')
            ->can('Blog: Author - Recycle Bin Restore');

        Route::delete('{id}/destroy', [AuthorController::class, 'destroyForce'])
            ->name('destroyForce')
            ->can('Blog: Author - Recycle Bin Delete');

        Route::delete('empty', [AuthorController::class, 'emptyRecycleBin'])
            ->name('empty')
            ->can('Blog: Author - Recycle Bin Delete');

        Route::get('restore', [AuthorController::class, 'restoreRecycleBin'])
            ->name('restoreAll')
            ->can('Blog: Author - Recycle Bin Restore');
    });

    Route::get('{id}/edit', [AuthorController::class, 'edit'])
        ->name('edit')
        ->can('Blog: Author - Edit');

    Route::put('{id}', [AuthorController::class, 'update'])
        ->name('update')
        ->can('Blog: Author - Edit');

    Route::delete('{id}', [AuthorController::class, 'destroy'])
        ->name('destroy')
        ->can('Blog: Author - Delete');
});
