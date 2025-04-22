<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Site;
use App\Models\SiteImage;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

/**
 * タクソノミー管理 Controller class
 */
class TaxonomyTermsController extends Controller
{
    public function tree(Request $request)
    {
        $siteId = $request->input('site', Site::orderBy('id')->first()?->id);
        $sites = Site::orderBy('id')->get();

        // taxonomy を取得 (存在しない可能性を考慮)
        $taxonomyId = $request->input(
            'taxonomy',
            Taxonomy::where('site_id', $siteId)->orderBy('id')->value('id')
        );

        $taxonomies = Taxonomy::where('site_id', $siteId)->orderBy('id')->get();

        // taxonomyが存在しない場合は空のCollection
        $terms = collect();
        $taxonomy = null;

        if ($taxonomyId) {
            $taxonomy = Taxonomy::find($taxonomyId);

            if ($taxonomy) {
                $terms = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)
                    ->whereNull('parent_id')
                    ->with(['children' => fn($q) => $q->orderBy('order')])
                    ->orderBy('order')
                    ->get();
            }
        }

        return view('admin.taxonomy-terms.tree', compact(
            'sites',
            'siteId',
            'taxonomy',
            'taxonomies',
            'terms'
        ));
    }

    public function show(TaxonomyTerm $taxonomyTerm)
    {
        return response()->json($taxonomyTerm);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'taxonomy_id' => 'required|exists:taxonomies,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:taxonomy_terms,id',
            'is_public' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], null, 'ja');
        }

        $maxOrder = TaxonomyTerm::where('parent_id', $validated['parent_id'] ?? null)
            ->where('taxonomy_id', $validated['taxonomy_id'])
            ->max('order');

        $validated['order'] = is_null($maxOrder) ? 1 : $maxOrder + 1;

        $term = TaxonomyTerm::create($validated);

        // サムネイル保存
        if ($request->hasFile('images.thumbnail')) {
            $file = $request->file('images.thumbnail');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $siteId = $term->taxonomy->site_id; // taxonomy 経由で site_id を取得
            $path = "taxonomy_terms/{$term->id}/thumbnail/{$filename}";
            $storagePath = "sites/{$siteId}/{$path}";
            $file->storeAs($storagePath, '', 'public');

            SiteImage::create([
                'site_id' => $siteId,
                'taxonomy_term_id' => $term->id,
                'type' => 'taxonomy_term_thumbnail',
                'path' => $path,
                'title' => $term->name,
                'is_public' => true,
                'order' => 1,
            ]);
        }

        return response()->json($term, 201);
    }

    public function update(Request $request, TaxonomyTerm $term)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:taxonomy_terms,slug,' . $term->id,
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);

        $term->update($validated);

        if ($request->hasFile('images.thumbnail')) {
            $file = $request->file('images.thumbnail');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $siteId = $term->taxonomy->site_id;
            $path = "taxonomy_terms/{$term->id}/thumbnail/{$filename}";
            $storagePath = "sites/{$siteId}/{$path}";
            $file->storeAs($storagePath, '', 'public');

            // 前の画像削除
            $term->thumbnail()->delete();

            SiteImage::create([
                'site_id' => $siteId,
                'taxonomy_term_id' => $term->id,
                'type' => 'taxonomy_term_thumbnail',
                'path' => $path,
                'title' => $term->name,
                'is_public' => true,
                'order' => 1,
            ]);
        }

        return response()->json($term);
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('hierarchy', []) as $node) {
            $this->updateOrderRecursive($node, null);
        }

        return response()->json(['message' => '並び順を更新しました']);
    }

    protected function updateOrderRecursive(array $node, $parentId)
    {
        static $order = 1;

        TaxonomyTerm::where('id', $node['id'])->update([
            'parent_id' => $parentId,
            'order' => $order++,
        ]);

        foreach ($node['children'] ?? [] as $child) {
            $this->updateOrderRecursive($child, $node['id']);
        }
    }
}
