<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function indexByGroup(TagGroup $group)
    {
        $tags = $group->tags()->orderBy('name')->get();
        return response()->json($tags);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags,slug',
            'description' => 'nullable|string',
            'tag_group_id' => 'required|exists:tag_groups,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-', 'ja');
        }

        $tag = Tag::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
        ]);

        // 👇 ここで中間テーブルに紐づけ
        $tag->tagGroups()->attach($validated['tag_group_id']);

        return response()->json($tag, 201);
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags,slug,' . $tag->id,
            'description' => 'nullable|string',
        ]);

        $tag->update($validated);
        return response()->json($tag);
    }

    public function destroy(Tag $tag)
    {
        // タグとタググループの関連を削除（中間テーブル）
        $tag->tagGroups()->detach();

        // タグ自体を削除
        $tag->delete();

        return response()->json(['message' => '削除しました']);
    }

    public function reorder(Request $request)
    {
        $groupId = $request->input('tag_group_id');
        $tagIds = $request->input('tags', []);
    
        // 現在そのグループに属しているtag_id一覧
        $currentTagIds = DB::table('tag_tag_group')
            ->where('tag_group_id', $groupId)
            ->pluck('tag_id')
            ->toArray();
    
        // 必要なレコードだけ updateOrInsert（並び順含む）
        foreach ($tagIds as $index => $tagId) {
            DB::table('tag_tag_group')->updateOrInsert(
                ['tag_id' => $tagId, 'tag_group_id' => $groupId],
                ['order' => $index + 1, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    
        // POSTされなかったタグは削除
        $toDetach = array_diff($currentTagIds, $tagIds);
        if (!empty($toDetach)) {
            DB::table('tag_tag_group')
                ->where('tag_group_id', $groupId)
                ->whereIn('tag_id', $toDetach)
                ->delete();
        }
    
        return response()->json(['message' => '並び順・所属を更新しました']);
    }
}
