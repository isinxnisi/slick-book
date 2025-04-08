<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
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

        $toc = $post->toc;

        return view('blog.post-view', [
            'post' => $post,
            'toc' => $toc,
        ]);
    }
}
