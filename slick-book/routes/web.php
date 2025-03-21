<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Site1\HomeController as Site1Home;
use App\Http\Controllers\Site2\HomeController as Site2Home;

// 環境設定からドメインを取得
$domains = config('multisite');

// 管理サイト
Route::domain($domains['admin'])->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::resource('posts', PostController::class);
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
