<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hierarchy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HierarchyController extends Controller
{

    // 全階層を取得（ルート + 子孫を含めたツリー構造）
    public function index()
    {
        $hierarchies = Hierarchy::roots(); // ルート階層
        return view('admin.hierarchies.index', compact('hierarchies'));
    }

    public function reorder(Request $request)
    {
        $data = $request->input('hierarchy'); // JSON配列を受け取る

        DB::transaction(function () use ($data) {
            $this->updateHierarchyOrder($data, null);
        });

        return response()->json(['message' => '階層を更新しました']);
    }

    private function updateHierarchyOrder(array $nodes, ?int $parentId = null)
    {
        foreach ($nodes as $index => $node) {
            Hierarchy::where('id', $node['id'])->update([
                'parent_id' => $parentId,
                'order' => $index
            ]);

            if (!empty($node['children'])) {
                $this->updateHierarchyOrder($node['children'], $node['id']);
            }
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['title' => 'required|string']);
    
        $parentId = $request->input('parent_id');
    
        // 同じ親を持つノードの中で最大の order を取得
        $maxOrder = Hierarchy::where('parent_id', $parentId)->max('order') ?? -1;
    
        $hierarchy = Hierarchy::create([
            'title'        => $validated['title'],
            'parent_id'    => $parentId,
            'order'        => $maxOrder + 1,
            'created_user' => Auth::id(),
        ]);
    
        return response()->json(['message' => '追加しました', 'id' => $hierarchy->id]);
    }
    
    public function update(Request $request, Hierarchy $hierarchy)
    {
        $validated = $request->validate(['title' => 'required|string']);
        $hierarchy->update([
            'title' => $validated['title'],
            'updated_user' => Auth::id()
        ]);
        return response()->json(['message' => '更新しました']);
    }
    
    public function destroy(Hierarchy $hierarchy)
    {
        $hierarchy->delete(); // ソフトデリート対応でもOK
        return response()->json(['message' => '削除しました']);
    }
}
