<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaxonomyController extends Controller
{
    // 一覧はtreeにあるので不要
    public function index()
    {
        return redirect()->route('taxonomy-terms.tree');
    }

    public function show(Taxonomy $taxonomy)
    {
        return response()->json($taxonomy);
    }

    // 登録
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:taxonomies,slug',
            'type' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'is_hierarchical' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-', 'ja');
        }

        $taxonomy = Taxonomy::create($validated);

        return response()->json($taxonomy, 201);
    }

    // 更新
    public function update(Request $request, Taxonomy $taxonomy)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:taxonomies,slug,' . $taxonomy->id,
            'type' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'is_hierarchical' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-', 'ja');
        }

        $taxonomy->update($validated);

        return response()->json($taxonomy);
    }

    // 削除
    public function destroy(Taxonomy $taxonomy)
    {
        $taxonomy->delete();
        return response()->json(['message' => '削除しました']);
    }
}
