<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Services\MarkdownService;
use Illuminate\Http\Request;
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
        $posts = \App\Models\Post::whereIn('category_id', $categoryIds)
            ->where('site_id', $site->id)
            ->published()
            ->latest('published_at')
            ->get();

        // 紐づくタグ一覧（重複なし）
        $categoryTags = \App\Models\Tag::whereHas('posts', function ($query) use ($categoryIds) {
            $query->whereIn('category_id', $categoryIds);
        })->distinct()->get();

        return view('blog.category', compact('category', 'posts', 'categoryTags'));
    }
}
