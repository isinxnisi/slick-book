<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Post;
use Illuminate\Contracts\View\View;

/**
 * カテゴリ Controller class
 */
class TagController extends Controller
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

        $tag = Tag::forSite($site->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $tagGroups = $tag->tagGroupsForSite($site->id)->get();

        // 投稿一覧（このタグに紐づくもの）
        $posts = Post::whereHas('tags', fn ($q) => $q->where('slug', $slug))
            ->where('site_id', $site->id)
            ->published()
            ->latest('published_at')
            ->get();

        return view('blog.tag', compact('tag', 'tagGroups', 'posts'));
    }
}
