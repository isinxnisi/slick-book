<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

/**
 * サイトカテゴリ管理 Controller class
 */
class CategoryController extends Controller
{
    /**
     * サイトカテゴリ管理: 登録・更新
     *
     * @param Request $request
     * @return View
     */
    public function tree(Request $request)
    {
        $siteId = $request->input('site', Site::orderBy('id')->first()?->id);
        $sites = Site::orderBy('id')->get();
        $categories = Category::where('site_id', $siteId)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return view('admin.categories.tree', compact('sites', 'siteId', 'categories'));
    }

    /* ////////////////////////////////
        Ajax
    //////////////////////////////// */

    /**
     * Ajax: サイトカテゴリ管理: 登録処理
     *
     * @param Request $request
     * @return JsonResponse
     */
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

        // 画像保存（カテゴリ単位）
        if ($request->hasFile('images.thumbnail')) {
            $file = $request->file('images.thumbnail');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // 👇 secure.media でそのまま渡す path をここで決定
            $path = "categories/{$category->id}/thumbnail/{$filename}";
            $storagePath = "sites/{$category->site_id}/{$path}";

            $file->storeAs($storagePath, '', 'public');

            // 👇 secure.media で使えるように path のみ保存
            $category->update(['image_path' => $path]);
        }

        return response()->json($category, 201);
    }

    /**
     * Ajax: サイトカテゴリ管理: 更新処理
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
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

        // 画像保存（カテゴリ単位）
        if ($request->hasFile('images.thumbnail')) {
            $file = $request->file('images.thumbnail');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // 👇 secure.media でそのまま渡す path をここで決定
            $path = "categories/{$category->id}/thumbnail/{$filename}";
            $storagePath = "sites/{$category->site_id}/{$path}";

            $file->storeAs($storagePath, '', 'public');

            // 👇 secure.media で使えるように path のみ保存
            $category->update(['image_path' => $path]);
        }

        return response()->json($category);
    }

    /**
     * Ajax: サイトカテゴリ: 削除処理
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => '削除しました']);
    }

    /**
     * Ajax: サイトカテゴリ: ソート処理
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reorder(Request $request)
    {
        foreach ($request->input('hierarchy', []) as $node) {
            $this->updateCategoryOrder($node, null);
        }

        return response()->json(['message' => '並び順を更新しました']);
    }

    /* ////////////////////////////////
        Protected
    //////////////////////////////// */

    /**
     * サイトカテゴリ並べ替え（再帰処理）
     *
     * @param array $node
     * @param int $parentId
     * @return void
     */
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
