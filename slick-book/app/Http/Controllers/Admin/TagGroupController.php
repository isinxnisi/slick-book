<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagGroupRequest;
use App\Http\Requests\UpdateTagGroupRequest;
use App\Models\TagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagGroupController extends Controller
{
    public function index()
    {
        // サイトに紐づかないマスタグループのみ表示（階層＋タグ付き）
        $groups = TagGroup::whereNull('parent_id')
            ->whereNotIn('id', function ($query) {
                $query->select('tag_group_id')->from('site_tag_group');
            })
            ->with([
                'tags' => fn ($q) => $q->orderBy('name'),
                'children.tags',
                'children.children.tags', // 深さがある場合はさらにネストしてもOK
            ])
            ->orderBy('order')
            ->get();

        return view('admin.tag-groups.index', compact('groups'));
    }

    public function store(StoreTagGroupRequest $request)
    {
        $validated = $request->validated();
    
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-', 'ja');
        }
    
        $maxOrder = TagGroup::where('parent_id', $validated['parent_id'] ?? null)->max('order');
        $validated['order'] = is_null($maxOrder) ? 1 : $maxOrder + 1;
    
        TagGroup::create($validated);
    
        return response()->json(['message' => '作成しました'], 201);
    }
    
    public function update(UpdateTagGroupRequest $request, TagGroup $tagGroup)
    {
        $validated = $request->validated();
    
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-', 'ja');
        }
    
        $tagGroup->update($validated);
    
        return response()->json(['message' => '更新しました']);
    }

    public function destroy(TagGroup $tagGroup)
    {
        $tagGroup->delete();
        return response()->json(['message' => '削除しました']);
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('hierarchy', []) as $node) {
            $this->updateGroupOrder($node, null);
        }

        return response()->json(['message' => '並び順を更新しました']);
    }

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
