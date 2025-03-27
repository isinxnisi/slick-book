<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Site;
use App\Models\TagGroup;
use App\Services\CategoryService;
use App\Services\MarkdownService;
use App\Services\TagGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct(protected TagGroupService $tagGroupService, protected CategoryService $categoryService) {}

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
    public function create(Request $request)
    {
        $siteId = $request->input('site', Site::first()?->id);
        $sites = Site::all();
    
        // 既存のドラフト記事があるか確認
        $post = Post::where('status', 'draft')
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
                'status' => 'draft',
                'created_user' => auth()->id(),
                'created' => now(),
            ]);
        }
    
        // タグ選択用のデータを作成
        $selectedTagIdsByPurpose = collect(config('tags.purposes'))->mapWithKeys(function ($label, $purpose) {
            return [$purpose => []];
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
     * 記事の保存処理
     */
    public function store(Request $request, MarkdownService $markdown)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'required|in:draft,published',
            'selected_tag_ids' => 'nullable|array', // 追加
        ]);
    
        $htmlBody = $markdown->convertToHtml($validated['body']);
        $toc = $markdown->generateTOC($validated['body']);
    
        $post = Post::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title'], '-', 'ja'),
            'body' => $validated['body'],
            'html_body' => $htmlBody,
            'toc' => $toc,
            'status' => $validated['status'],
            'created_user' => Auth::id(),
            'created' => now(),
        ]);
    
        // タグの紐付け処理追加
        $tagIds = collect($validated['selected_tag_ids'])->flatten()->unique()->toArray();
        $post->tags()->sync($tagIds);
    
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

    public function tags(Request $request)
    {
        $siteId = $request->input('site');
        $purpose = $request->input('purpose', 'public');

        // 仮データ or 実データ：site_id & purpose に応じてタググループとタグを取得
        // $tagGroups = TagGroup::with(['tags' => function ($query) {
        //         $query->select('tags.id', 'tags.name');
        //     }])
        //     ->where('purpose', $purpose)
        //     ->whereHas('siteTagGroups', function ($q) use ($siteId) {
        //         $q->where('site_id', $siteId);
        //     })
        //     ->get()
        //     ->map(function ($group) {
        //         return [
        //             'name' => $group->name,
        //             'tags' => $group->tags->map(fn($tag) => [
        //                 'id' => $tag->id,
        //                 'name' => $tag->name,
        //             ]),
        //         ];
        //     });
        // $tagGroups = TagGroup::with(['tags' => function ($query) {
        //         $query->select('tags.id', 'tags.name');
        //     }])
        //     ->whereHas('siteTagGroups', function ($q) use ($siteId) {
        //         $q->where('site_id', $siteId);
        //     })
        //     ->get();
        // $this->tagGroupService->injectPurposeIntoTags($tagGroups);
        $tagGroups = new TagGroup()->getSiteTagGroupTree($siteId, $purpose);
        $tagGroups = $this->tagGroupService->flattenGroups($tagGroups);
        // dd(array_column($tagGroups->toArray(), 'name'));die;
        // 選択状態の保持
        $selectedTagIds = TagGroup::with(['tags:id'])
            ->whereHas('siteTagGroups', function ($q) use ($siteId) {
                $q->where('site_id', $siteId);
            })
            ->get()
            ->mapWithKeys(function ($group) {
                return [$group->id => $group->tags->pluck('id')->toArray()];
            });

        return view('components.admin.tags.tag-selection', [
            'groups' => $tagGroups,
            'selectedTagIds' => $selectedTagIds,
        ]);
    }
}
