<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function tree(Request $request)
    {
        $siteId = $request->input('site', Site::first()?->id);
        $sites = Site::all();
        $categories = Category::where('site_id', $siteId)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return view('admin.categories.tree', compact('sites', 'siteId', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'site_id' => 'required|exists:sites,id',
            'image_path' => 'nullable|string|max:1024',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'is_visible' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title'], null, 'ja');
        }

        // ✅ order を親カテゴリ内で最大値 +1 に設定
        $maxOrder = Category::where('parent_id', $validated['parent_id'] ?? null)
            ->where('site_id', $validated['site_id'])
            ->max('order');

        $validated['order'] = is_null($maxOrder) ? 1 : $maxOrder + 1;

        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:1024',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'is_visible' => 'nullable|boolean',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => '削除しました']);
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('hierarchy', []) as $node) {
            $this->updateCategoryOrder($node, null);
        }

        return response()->json(['message' => '並び順を更新しました']);
    }

    protected function updateCategoryOrder(array $node, $parentId)
    {
        static $order = 1;

        Category::where('id', $node['id'])->update([
            'parent_id' => $parentId,
            'order' => $order++,
        ]);

        foreach ($node['children'] ?? [] as $child) {
            $this->updateCategoryOrder($child, $node['id']);
        }
    }
}
