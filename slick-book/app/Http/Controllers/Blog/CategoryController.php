<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;

/**
 * カテゴリ Controller class
 */
class CategoryController extends Controller
{
    /**
     * カテゴリ｜プレビュー
     *
     * @param string $slug
     * @return View
     */
    public function view($slug)
    {
        $site = app('CurrentSite');

        $category = Category::with('childrenRecursive')
            ->where('slug', $slug)
            ->where('site_id', $site->id)
            ->firstOrFail();

        // 対象カテゴリ + 子孫カテゴリすべてのID
        $categoryIds = $category->descendant_ids;

        // 投稿一覧（このカテゴリと子孫カテゴリ）
        $posts = Post::whereIn('category_id', $categoryIds)
            ->where('site_id', $site->id)
            ->published()
            ->latest('published_at')
            ->paginate(10);

        // 紐づくタグ一覧（重複なし）
        $categoryTags = Tag::whereHas('posts', function ($query) use ($categoryIds) {
            $query->whereIn('category_id', $categoryIds);
        })->distinct()->get();

        // SEO設定上書き（noindex）
        $seo = app(SeoService::class)->generateSeoForCurrentPage($site);
        if ($posts->isEmpty()) {
            $seo['noindex'] = true;
        }
        app(SeoService::class)->overrideSeo($seo);

        return view('blog.category', compact('category', 'posts', 'categoryTags'));
    }
}
