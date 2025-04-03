<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * マスタ: タグ所属グループ管理 Controller class
 */
class TagTagGroupController extends Controller
{
    /**
     * Ajax: マスタ: タグとグループとの紐づけを登録
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function toggle(Request $request)
    {
        $tagId = $request->input('tag_id');
        $groupId = $request->input('tag_group_id');

        if (!$tagId || !$groupId) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $exists = DB::table('tag_tag_group')
            ->where('tag_id', $tagId)
            ->where('tag_group_id', $groupId)
            ->exists();

        if ($exists) {
            DB::table('tag_tag_group')
                ->where('tag_id', $tagId)
                ->where('tag_group_id', $groupId)
                ->delete();
        } else {
            $maxOrder = DB::table('tag_tag_group')
                ->where('tag_group_id', $groupId)
                ->max('order') ?? 0;

            DB::table('tag_tag_group')->insert([
                'tag_id' => $tagId,
                'tag_group_id' => $groupId,
                'order' => $maxOrder + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Ajax: マスタ: タグとグループとの紐づけを解除
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function unlink(Request $request)
    {
        $tagId = $request->input('tag_id');
        $groupId = $request->input('tag_group_id');

        if (!$tagId || !$groupId) {
            return response()->json(['error' => 'パラメータ不足'], 400);
        }

        DB::table('tag_tag_group')
            ->where('tag_id', $tagId)
            ->where('tag_group_id', $groupId)
            ->delete();

        return response()->json(['message' => 'グループとの紐づけを解除しました']);
    }
}
