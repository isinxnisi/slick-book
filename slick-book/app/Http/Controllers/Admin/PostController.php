<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\MarkdownService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * 記事一覧の表示
     */
    public function index()
    {
        $posts = Post::where('is_deleted', false)->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * 記事詳細の表示
     */
    public function show(Post $post)
    {
        if ($post->is_deleted) {
            abort(404);
        }
        return view('admin.posts.show', compact('post'));
    }

    /**
     * 新規記事作成フォーム表示
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * 記事の保存処理
     */
    public function store(Request $request, MarkdownService $markdown)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $htmlBody = $markdown->convertToHtml($validated['body']);
        $toc = $markdown->generateTOC($validated['body']);

        $post = Post::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title'], null, 'ja'),
            'body' => $validated['body'],
            'html_body' => $htmlBody,
            'toc' => $toc,
            'status' => $validated['status'],
            'created_user' => Auth::id(), // 認証ユーザーのIDを保存
            'created' => now(),
        ]);

        return redirect()->route('posts.show', $post);
    }

    /**
     * 記事編集フォームの表示
     */
    public function edit(Post $post)
    {
        if ($post->is_deleted) {
            abort(404);
        }
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * 記事の更新処理
     */
    public function update(Request $request, Post $post, MarkdownService $markdown)
    {
        if ($post->is_deleted) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $htmlBody = $markdown->convertToHtml($validated['body']);
        $toc = $markdown->generateTOC($validated['body']);

        $post->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title'], null, 'ja'),
            'body' => $validated['body'],
            'html_body' => $htmlBody,
            'toc' => $toc,
            'status' => $validated['status'],
            'updated_user' => Auth::id(),
            'updated' => now(),
        ]);

        return redirect()->route('posts.show', $post);
    }

    /**
     * 記事の削除処理
     */
    public function destroy(Post $post)
    {
        if ($post->is_deleted) {
            abort(404);
        }

        $post->update([
            'deleted_user' => Auth::id(),
            'deleted' => now(),
            'is_deleted' => true,
        ]);

        return redirect()->route('posts.index');
    }
}
