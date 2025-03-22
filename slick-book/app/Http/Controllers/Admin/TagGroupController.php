<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Site;

class TagGroupController extends Controller
{
    public function index()
    {
        // サイトに紐づかないマスタグループのみ表示
        $groups = TagGroup::whereNull('parent_id')
            ->whereNotIn('id', function ($query) {
                $query->select('tag_group_id')->from('site_tag_group');
            })
            ->with('children')
            ->orderBy('order')
            ->get();
    
        return view('admin.tag-groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tag_groups',
            'purpose' => 'required|string|max:50',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:tag_groups,id',
        ]);
    
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-', 'ja');
        }
    
        $maxOrder = TagGroup::where('parent_id', $validated['parent_id'] ?? null)->max('order');
        $validated['order'] = is_null($maxOrder) ? 1 : $maxOrder + 1;
    
        TagGroup::create($validated);
    
        return response()->json(['message' => '作成しました'], 201);
    }

    public function update(Request $request, TagGroup $tagGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tag_groups,slug,' . $tagGroup->id,
            'purpose' => 'required|string|max:50',
        ]);

        $tagGroup->update($validated);
        return response()->json($tagGroup);
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
            'order' => $order++
        ]);

        foreach ($node['children'] ?? [] as $child) {
            $this->updateGroupOrder($child, $node['id']);
        }
    }
}