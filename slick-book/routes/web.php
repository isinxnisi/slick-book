<?php

use App\Http\Controllers\SiteMediaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\HierarchyController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\SiteSeoSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostTagController;
use App\Http\Controllers\Admin\SiteBannerController;
use App\Http\Controllers\Admin\TagGroupController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\SiteTagGroupController;
use App\Http\Controllers\Admin\TagTagGroupController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\TaxonomyTermsController;
use App\Http\Controllers\Blog\HomeController as BlogHome;
use App\Http\Controllers\Blog\PostController as BlogPostController;
use App\Http\Controllers\Blog\CategoryController as BlogCategoryController;
use App\Http\Controllers\Blog\TagController as BlogTagController;
use App\Http\Controllers\Blog\TagGroupController as BlogTagGroupController;
use App\Http\Controllers\Develop\ContentController as DevelopContentController;
use App\Http\Controllers\SitemapController;

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
        // アップロード画像の参照用
        Route::get('/media/{site}/{path}', [SiteMediaController::class, 'admin'])
            ->where('path', '.*')
            ->name('admin.media');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // 記事
        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('posts', [PostController::class, 'store'])->name('posts.store');
        Route::post('posts/preview', [PostController::class, 'preview'])->name('posts.preview');
        Route::get('posts/tags', [PostController::class, 'tags'])->name('posts.tags');
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::patch('posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::post('posts/generateMetaKeywords', [PostController::class, 'generateMetaKeywords'])->name('posts.generateMetaKeywords');

        Route::resource('hierarchies', HierarchyController::class);
        Route::post('/hierarchies/reorder', [HierarchyController::class, 'reorder'])->name('hierarchies.reorder');

        // サイト管理
        Route::resource('sites', SiteController::class);
        Route::prefix('seo-settings')->name('site-seo-settings.')->group(function () {
            Route::get('/', [SiteSeoSettingController::class, 'edit'])->name('edit');
            Route::put('/', [SiteSeoSettingController::class, 'update'])->name('update');
        });

        // カテゴリ階層UIの表示
        Route::get('categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');
        // カテゴリCRUD（モーダル前提）
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::patch('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        // 並び順の更新
        Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

        // タクソノミー
        Route::resource('taxonomies', TaxonomyController::class)->except(['create', 'edit']);
        Route::get('taxonomies/{taxonomy}', [TaxonomyController::class, 'show'])->name('taxonomies.show');

        // タクソノミー・ターム
        Route::get('taxonomy-terms/tree', [TaxonomyTermsController::class, 'tree'])->name('taxonomy-terms.tree');
        // 静的ルートのあとに動的ルートを定義する
        Route::get('taxonomy-terms/{taxonomyTerm}', [TaxonomyTermsController::class, 'show'])->name('taxonomy-terms.show');
        Route::post('taxonomy-terms', [TaxonomyTermsController::class, 'store'])->name('taxonomy-terms.store');
        Route::patch('taxonomy-terms/{taxonomyTerm}', [TaxonomyTermsController::class, 'update'])->name('taxonomy-terms.update');
        Route::delete('taxonomy-terms/{taxonomyTerm}', [TaxonomyTermsController::class, 'destroy'])->name('taxonomy-terms.destroy');
        Route::post('taxonomy-terms/reorder', [TaxonomyTermsController::class, 'reorder'])->name('taxonomy-terms.reorder');


        Route::get('tag-groups', [TagGroupController::class, 'index'])->name('tag-groups.index');
        Route::post('tag-groups', [TagGroupController::class, 'store'])->name('tag-groups.store');
        Route::patch('tag-groups/{tagGroup}', [TagGroupController::class, 'update'])->name('tag-groups.update');
        Route::delete('tag-groups/{tagGroup}', [TagGroupController::class, 'destroy'])->name('tag-groups.destroy');
        Route::post('tag-groups/reorder', [TagGroupController::class, 'reorder'])->name('tag-groups.reorder');

        Route::get('tags/by-group/{group}', [TagController::class, 'indexByGroup'])->name('tags.by-group');
        Route::post('tags', [TagController::class, 'store'])->name('tags.store');
        Route::patch('tags/{tag}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
        Route::post('tags/reorder', [TagController::class, 'reorder'])->name('tags.reorder');

        Route::get('site-tag-groups', [SiteTagGroupController::class, 'index'])->name('site-tag-groups.index');
        Route::post('site-tag-groups', [SiteTagGroupController::class, 'store'])->name('site-tag-groups.store');
        Route::patch('site-tag-groups/{tagGroup}', [SiteTagGroupController::class, 'update'])->name('site-tag-groups.update');
        Route::delete('site-tag-groups/{tagGroup}', [SiteTagGroupController::class, 'destroy'])->name('site-tag-groups.destroy');
        Route::post('site-tag-groups/reorder', [SiteTagGroupController::class, 'reorder'])->name('site-tag-groups.reorder');

        Route::post('/tag-tag-groups/toggle', [TagTagGroupController::class, 'toggle']);
        Route::post('/tag-tag-groups/unlink', [TagTagGroupController::class, 'unlink']);
        Route::get('/site-tag-groups/master-tags', [SiteTagGroupController::class, 'getMasterTags'])->name('site-tag-groups.master-tags');
        Route::get('/tag-groups/{group}/tags', [TagGroupController::class, 'tags']);

        Route::post('/post-tags/toggle', [PostTagController::class, 'toggle']);
        Route::post('/post-tags/unlink', [PostTagController::class, 'unlink']);

        Route::prefix('site-banners')->name('site-banners.')->middleware('auth')->group(function () {
            Route::get('/', [SiteBannerController::class, 'index'])->name('index');
            Route::post('/', [SiteBannerController::class, 'store'])->name('store');
            Route::put('/{siteBanner}', [SiteBannerController::class, 'update'])->name('update');
            Route::delete('/{siteBanner}', [SiteBannerController::class, 'destroy'])->name('destroy');
        });
    });
});

// 公開サイト
Route::middleware(['load.site'])->group(function () {
    Route::get('/robots.txt', function () {
        $lines = [];
        if (app()->environment('production')) {
            $lines[] = 'User-agent: *';
            $lines[] = 'Disallow: /search';
            $lines[] = 'Disallow: /*?page=';
            $lines[] = 'Sitemap: ' . url('/sitemap.xml');
        } else {
            // 本番以外は全ブロック
            $lines[] = 'User-agent: *';
            $lines[] = 'Disallow: /';
        }
        return Response::make(implode(PHP_EOL, $lines), 200)
            ->header('Content-Type', 'text/plain');
    });
    // サイトマップ
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
    // アップロード画像の参照用
    Route::get('/media/{site}/{path}', [SiteMediaController::class, 'public'])
        ->where('path', '.*')
        ->name('secure.media');

    Route::get('/', [BlogHome::class, 'index'])->name('blog.home');
    Route::get('post/{post}', [BlogPostController::class, 'view'])->name('posts.view');

    Route::get('/category/{slug}', [BlogCategoryController::class, 'view'])
        ->name('blog.category');
    Route::get('/tag/{slug}', [BlogTagController::class, 'view'])
        ->name('blog.tag');
    Route::get('/tagGroup/{slug}', [BlogTagGroupController::class, 'view'])
        ->name('blog.tagGroup');
});

// 開発用サイト
Route::domain($domains['develop'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('content', [DevelopContentController::class, 'index'])->name('content.index');
    // Route::get('content/create', [PostController::class, 'create'])->name('content.create');
});

require __DIR__.'/auth.php';
