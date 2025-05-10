<?php

use Illuminate\Support\Facades\Route;
use Modules\ContentModule\Samples\Http\Controllers\Admin\ContentController as AdminContentController;
use Modules\ContentModule\Samples\Http\Controllers\Admin\ItemController;
use Modules\ContentModule\Samples\Http\Controllers\Front\ContentFrontController;

Route::prefix('admin/contents')->group(function() {
    // /admin/contents/
    Route::get('/', [AdminContentController::class, 'index'])
         ->name('admin.contents.index');

    // /admin/contents/create
    Route::get('create', [AdminContentController::class, 'create'])
         ->name('admin.contents.create');

    // POST /admin/contents
    Route::post('/', [AdminContentController::class, 'store'])
         ->name('admin.contents.store');

    // /admin/contents/{id}/edit
    Route::get('{id}/edit', [AdminContentController::class, 'edit'])
         ->name('admin.contents.edit');

    // PUT /admin/contents/{id}
    Route::put('{id}', [AdminContentController::class, 'update'])
         ->name('admin.contents.update');

    // DELETE /admin/contents/{id}
    Route::delete('{id}', [AdminContentController::class, 'destroy'])
         ->name('admin.contents.destroy');

    // GET /admin/contents/form-fields?type=…&kind=…
    Route::get('form-fields', [AdminContentController::class, 'formFields'])
         ->name('admin.contents.form-fields');

    // レビュー申請
    Route::post('{id}/to-review', [AdminContentController::class, 'toReview'])
            ->name('admin.contents.to-review');
    // 公開
    Route::post('{id}/publish', [AdminContentController::class, 'publish'])
            ->name('admin.contents.publish');
    // アーカイブ
    Route::post('{id}/archive', [AdminContentController::class, 'archive'])
            ->name('admin.contents.archive');

    Route::get('items', [ItemController::class, 'items'])
            ->name('admin.contents.items');
});

Route::get('/content/{slug}', [ContentFrontController::class, 'show'])
     ->name('content.show');
