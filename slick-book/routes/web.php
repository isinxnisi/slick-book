<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\HierarchyController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagGroupController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Site1\HomeController as Site1Home;
use App\Http\Controllers\Site2\HomeController as Site2Home;

// 環境設定からドメインを取得
$domains = config('multisite');

// 管理サイト
Route::domain($domains['admin'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');
});

Route::domain($domains['admin'])->middleware(['auth'])->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::resource('posts', PostController::class);

        Route::resource('hierarchies', HierarchyController::class);
        Route::post('/hierarchies/reorder', [HierarchyController::class, 'reorder'])->name('hierarchies.reorder');

        // サイト管理
        Route::resource('sites', SiteController::class);

        // カテゴリ階層UIの表示
        Route::get('categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');
        // カテゴリCRUD（モーダル前提）
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::patch('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        // 並び順の更新
        Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

        Route::get('tag-groups', [TagGroupController::class, 'index'])->name('tag-groups.index');
        Route::post('tag-groups', [TagGroupController::class, 'store'])->name('tag-groups.store');
        Route::patch('tag-groups/{tagGroup}', [TagGroupController::class, 'update'])->name('tag-groups.update');
        Route::delete('tag-groups/{tagGroup}', [TagGroupController::class, 'destroy'])->name('tag-groups.destroy');
        Route::post('tag-groups/reorder', [TagGroupController::class, 'reorder'])->name('tag-groups.reorder');

        Route::get('tags/by-group/{group}', [TagController::class, 'indexByGroup'])->name('tags.by-group');
        Route::post('tags', [TagController::class, 'store'])->name('tags.store');
        Route::patch('tags/{tag}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
    });
});

// 公開サイト1
Route::domain($domains['site1'])->group(function () {
    Route::get('/', [Site1Home::class, 'index'])->name('site1.home');
});

// 公開サイト2
Route::domain($domains['site2'])->group(function () {
    Route::get('/', [Site2Home::class, 'index'])->name('site2.home');
});
require __DIR__.'/auth.php';
