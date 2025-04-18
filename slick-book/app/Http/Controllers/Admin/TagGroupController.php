<?php

namespace App\Http\Controllers\Admin;

use App\Services\TagGroupService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagGroupRequest;
use App\Http\Requests\UpdateTagGroupRequest;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

/**
 * マスタ: タグ・グループ管理 Controller class
 */
class TagGroupController extends Controller
{
    public function __construct(protected TagGroupService $tagGroupService) {}

    /**
     * マスタ: タグ・グループ管理: 入力画面
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $purpose = $request->get('purpose', 'public');

        $groups = TagGroup::where('purpose', $purpose)
            ->whereNull('parent_id')
            ->whereNotIn('id', function ($query) {
                $query->select('tag_group_id')->from('site_tag_group');
            })
            ->with([
                'tags' => fn($q) => $q->orderBy('name'),
                'children.tags',
                'children.children.tags',
            ])
            ->orderBy('order')
            ->get();

        // 未分類タグ（マスタ・タググループに属していないタグ）
        $ungroupingTags = Tag::whereNotIn('id', function($query) {
                $query->select('tag_id')->from('tag_tag_group')
                    ->whereNotIn('tag_group_id', function($q) {
                        // サイトタググループ以外 = マスタタググループ
                        $q->select('tag_group_id')->from('site_tag_group');
                    });
            })
            ->get();

        $this->tagGroupService->injectPurposeIntoTags($groups);

        return view('admin.tag-groups.index', compact('groups', 'ungroupingTags', 'purpose'));
    }

    /* ////////////////////////////////
        Ajax
    //////////////////////////////// */

    /**
     * Ajax: マスタ: タグ・グループ登録処理
     *
     * @param StoreTagGroupRequest $request
     * @return JsonResponse
     */
    public function store(StoreTagGroupRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-', 'ja');
        }

        $maxOrder = TagGroup::where('parent_id', $validated['parent_id'] ?? null)->max('order');
        $validated['order'] = is_null($maxOrder) ? 1 : $maxOrder + 1;
        $validated['icon'] = $validated['icon'] ?? config('icons.default');

        TagGroup::create($validated);

        return response()->json(['message' => '作成しました'], 201);
    }

    /**
     * Ajax: マスタ: タグ・グループ更新処理
     *
     * @param UpdateTagGroupRequest $request
     * @param TagGroup $tagGroup
     * @return JsonResponse
     */
    public function update(UpdateTagGroupRequest $request, TagGroup $tagGroup)
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-', 'ja');
        }

        $validated['icon'] = $validated['icon'] ?? config('icons.default');

        $tagGroup->update($validated);

        return response()->json(['message' => '更新しました']);
    }

    /**
     * Ajax: マスタ: タグ・グループ削除処理
     *
     * @param TagGroup $tagGroup
     * @return JsonResponse
     */
    public function destroy(TagGroup $tagGroup)
    {
        $tagGroup->delete();
        return response()->json(['message' => '削除しました']);
    }

    /**
     * Ajax: マスタ: タグリスト取得処理
     *
     * @param TagGroup $group
     * @return JsonResponse
     */
    public function tags(TagGroup $group)
    {
        return response()->json($group->tags()->orderBy('name')->get());
    }

    /**
     * Ajax: マスタ: タググループ: ソート処理
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reorder(Request $request)
    {
        foreach ($request->input('hierarchy', []) as $node) {
            $this->updateGroupOrder($node, null);
        }

        return response()->json(['message' => '並び順を更新しました']);
    }

    /* ////////////////////////////////
        Protected
    //////////////////////////////// */

    /**
     * マスタ: タググループ並べ替え（再帰処理）
     *
     * @param array $node
     * @param int $parentId
     * @return void
     */
    protected function updateGroupOrder(array $node, $parentId)
    {
        static $order = 1;

        TagGroup::where('id', $node['id'])->update([
            'parent_id' => $parentId,
            'order' => $order++,
        ]);

        foreach ($node['children'] ?? [] as $child) {
            $this->updateGroupOrder($child, $node['id']);
        }
    }
}
