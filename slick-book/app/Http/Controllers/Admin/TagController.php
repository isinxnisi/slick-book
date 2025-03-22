<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Http\Request;
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
        $tag->delete();
        return response()->json(['message' => '削除しました']);
    }
}
