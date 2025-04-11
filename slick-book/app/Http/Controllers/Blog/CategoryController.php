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

        $category = Category::where('slug', $slug)->where('site_id', $site->id)->firstOrFail();

        // カテゴリの記事一覧などを取得
        $posts = $category->posts()->where('site_id', $site->id)->published()->latest()->get();

        return view('blog.category', compact('category', 'posts'));
    }
}
