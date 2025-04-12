<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\TagGroup;
use App\Services\MarkdownService;
use Illuminate\Http\Request;

/**
 * 記事管理 Controller class
 */
class PostController extends Controller
{
    /**
     * 記事管理｜プレビュー
     *
     * @param Request $request
     * @param MarkdownService $markdown
     * @return void
     */
    public function view(Request $request, Post $post)
    {
        $site = app('CurrentSite');
        if ($post->site_id != $site->id) {
            abort(404);
        }

        $toc = $post->toc;

        // 投稿一覧（このタグに紐づくもの）
        $recommendPosts = Post::whereHas('tags', fn ($q) => $q->where('purpose', 'public')->whereIn('tags.id', $post->tags->pluck('id')))
            ->where('site_id', $site->id)
            ->published()
            ->latest('published_at')
            ->limit(10)
            ->get();

        return view('blog.post-view', [
            'post' => $post,
            'toc' => $toc,
            'recommendPosts' => $recommendPosts,
        ]);
    }
}
