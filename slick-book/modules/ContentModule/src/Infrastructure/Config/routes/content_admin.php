<?php
// modules/ContentModule/src/Infrastructure/Config/routes/content_admin.php

use Illuminate\Support\Facades\Route;
use Modules\ContentModule\Infrastructure\Http\Controllers\Admin\ContentController as AdminContentController;

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
});
