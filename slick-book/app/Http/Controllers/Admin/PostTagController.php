<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Site;
use App\Models\TagGroup;
use App\Services\MarkdownService;
use App\Services\TagGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

/**
 * 投稿タグ Controller class
 */
class PostTagController extends Controller
{
    /**
     * 投稿: 公開タグ: 登録処理
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function toggle(Request $request)
    {
        $postId = $request->input('post_id');
        $tagId = $request->input('tag_id');

        if (!$tagId || !$postId) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $status = 0;
        $exists = DB::table('post_tag')
            ->where('post_id', $postId)
            ->where('tag_id', $tagId)
            ->exists();

        if ($exists) {
            DB::table('post_tag')
                ->where('post_id', $postId)
                ->where('tag_id', $tagId)
                ->delete();
            $status = 1;

        } else {
            $maxOrder = DB::table('post_tag')
                ->where('post_id', $postId)
                ->max('order') ?? 0;

            DB::table('post_tag')->insert([
                'post_id' => $postId,
                'tag_id' => $tagId,
                'order' => $maxOrder + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $status = 2;
        }

        return response()->json(['status' => $status]);
    }

    /**
     * 投稿: 公開タグ: 解除処理
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function unlink(Request $request)
    {
        $tagId = $request->input('tag_id');
        $postId = $request->input('post_id');

        if (!$tagId || !$postId) {
            return response()->json(['error' => 'パラメータ不足'], 400);
        }

        DB::table('post_tag')
            ->where('tag_id', $tagId)
            ->where('post_id', $postId)
            ->delete();

        return response()->json(['message' => '投稿との紐づけを解除しました']);
    }
}
