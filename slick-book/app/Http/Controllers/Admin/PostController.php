<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Site;
use App\Models\TagGroup;
use App\Models\PostTag;
use App\Models\Tag;
use App\Services\CategoryService;
use App\Services\MarkdownService;
use App\Services\TagGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

/**
 * 記事管理 Controller class
 */
class PostController extends Controller
{
    /**
     * コンストラクタ
     *
     * @param TagGroupService $tagGroupService
     * @param CategoryService $categoryService
     */
    public function __construct(protected TagGroupService $tagGroupService, protected CategoryService $categoryService) {}

    /**
     * 記事管理｜記事一覧
     *
     * @return View
     */
    public function index()
    {
        $posts = Post::where('status', '!=', 'temp')
            ->where('is_deleted', false)
            ->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * 記事管理｜記事詳細
     *
     * @param Post $post
     * @return View
     */
    public function show(Post $post)
    {
        if ($post->is_deleted) {
            abort(404);
        }
        return view('admin.posts.show', compact('post'));
    }

    /**
     * 記事管理｜新規記事作成フォーム
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request)
    {
        $siteId = $request->input('site', Site::first()?->id);
        $sites = Site::all();

        // 既存のドラフト記事があるか確認
        $post = Post::where('status', 'temp')
            ->where('created_user', Auth::id())
            ->orderBy('created', 'desc')
            ->first();

        // なければ仮レコードを作成
        if (!$post) {
            $post = Post::create([
                'title' => '',
                'slug' => Str::uuid(), // 仮スラッグ（後で上書き可）
                'body' => '',
                'html_body' => '',
                'toc' => '',
                'status' => 'temp',
                'created_user' => Auth::id(),
                'created' => now(),
            ]);
        }

        // タグ選択用のデータを作成
        $selectedTagIdsByPurpose = collect(config('tags.purposes'))->mapWithKeys(function ($label, $purpose) use ($post) {
            return [
                $purpose => Tag::where('purpose', $purpose)
                    ->whereIn('id', function ($query) use ($post) {
                        $query->select('tag_id')->from('post_tag')->where('post_id', $post->id);
                    })
                    ->orderBy('order')
                    ->get(),
            ];
        });

        // カテゴリ
        $categories = Category::where('site_id', $siteId)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
        $categories = $this->categoryService->flatten($categories);

        return view('admin.posts.create', compact('sites', 'siteId', 'selectedTagIdsByPurpose', 'post', 'categories'));
    }

    /**
     * 記事管理｜記事の保存処理
     *
     * @param Request $request
     * @param MarkdownService $markdown
     * @return RedirectResponse
     */
    public function store(Request $request, MarkdownService $markdown)
    {
        $postId = $request->input('id');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'required|in:draft,published',
            'category_id' => 'nullable|exists:categories,id',
            'selected_tag_ids' => 'nullable|array', // 追加
        ]);

        $htmlBody = $markdown->convertToHtml($validated['body']);
        $toc = $markdown->generateTOC($validated['body']);

        $post = Post::where('id', $postId)->first();
        $post->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title'], null, 'ja'),
            'body' => $validated['body'],
            'html_body' => $htmlBody,
            'toc' => $toc,
            'status' => $validated['status'],
            'category_id' => $validated['category_id'],
            'updated_user' => Auth::id(),
            'updated' => now(),
        ]);

        // タグの紐付け処理追加
        $tagIds = [];
        foreach ($validated['selected_tag_ids'] as $key => $tagList) {
            $tagList = json_decode($tagList);
            $tagList = array_column($tagList, 'id');
            $tagIds = [...$tagIds, ...$tagList];
        }
        $post->tags()->sync($tagIds);

        return redirect()->route('posts.edit', $post);
    }

    /**
     * 記事管理｜記事編集フォーム
     *
     * @param Request $request
     * @param Post $post
     * @return View
     */
    public function edit(Request $request, Post $post)
    {
        if ($post->is_deleted) {
            abort(404);
        }
        $siteId = $request->input('site', Site::first()?->id);
        $sites = Site::all();

        // タグ選択用のデータを作成
        $selectedTagIdsByPurpose = collect(config('tags.purposes'))->mapWithKeys(function ($label, $purpose) use ($post) {
            return [
                $purpose => Tag::where('purpose', $purpose)
                    ->whereIn('id', function ($query) use ($post) {
                        $query->select('tag_id')->from('post_tag')->where('post_id', $post->id);
                    })
                    ->orderBy('order')
                    ->get(),
            ];
        });

        // カテゴリ
        $categories = Category::where('site_id', $siteId)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
        $categories = $this->categoryService->flatten($categories);

        return view('admin.posts.edit', compact('sites', 'siteId', 'selectedTagIdsByPurpose', 'post', 'categories'));
    }

    /**
     * 記事管理｜記事の更新処理
     *
     * @param Request $request
     * @param Post $post
     * @param MarkdownService $markdown
     * @return RedirectResponse
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
            'category_id' => 'nullable|exists:categories,id',
            'selected_tag_ids' => 'nullable|array', // 追加
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
            'category_id' => $validated['category_id'],
            'updated_user' => Auth::id(),
            'updated' => now(),
        ]);

        // タグの紐付け処理追加
        $tagIds = [];
        foreach ($validated['selected_tag_ids'] as $key => $tagList) {
            $tagList = json_decode($tagList);
            $tagIds = [...$tagIds, ...$tagList];
        }
        $post->tags()->sync($tagIds);

        return redirect()->route('posts.edit', $post);
    }

    /**
     * 記事管理｜記事の削除処理
     *
     * @param Post $post
     * @return RedirectResponse
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

    /**
     * 記事管理｜投稿タグ管理UI
     *
     * @param Request $request
     * @return View
     */
    public function tags(Request $request)
    {
        $siteId = $request->input('site');
        $postId = $request->input('post');
        $purpose = $request->input('purpose', 'public');

        $tagGroups = new TagGroup()->getSiteTagGroupTree($siteId, $purpose);
        $tagGroups = $this->tagGroupService->flattenGroups($tagGroups);

        // 選択状態
        $post = Post::where('id', $postId)->first();
        $selectedTagIdsByPurpose = collect(config('tags.purposes'))->mapWithKeys(function ($label, $purpose) use ($post) {
            return [
                $purpose => Tag::where('purpose', $purpose)
                    ->whereIn('id', function ($query) use ($post) {
                        $query->select('tag_id')->from('post_tag')->where('post_id', $post->id);
                    })
                    ->orderBy('order')
                    ->get(),
            ];
        });

        return view('components.admin.tags.post-tag-selection', [
            'groups' => $tagGroups,
            'selectedTagIds' => $selectedTagIdsByPurpose[$purpose],
        ]);
    }

    /**
     * 記事管理｜プレビュー
     *
     * @param Request $request
     * @param MarkdownService $markdown
     * @return void
     */
    public function preview(Request $request, MarkdownService $markdown)
    {
        $postId = $request->input('post_id');
        $title = $request->input('title') ?? '';
        $body = $request->input('body') ?? '';
        $selectedTagIds = json_decode($request->input('selected_tags') ?? '[]');

        $post = Post::where('id', $postId)->first();

        $toc = $markdown->generateTOC($body);
        $post->title = htmlspecialchars($title);
        $post->html_body = $markdown->convertToHtml($body);
        $post->tags = Tag::whereIn('id', $selectedTagIds)->get();

        // return $htmlBody;
        return view('components.admin.posts.preview', [
            'post' => $post,
            'toc' => $toc,
        ]);
    }
}
