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

class PostTagController extends Controller
{
    // public function __construct(protected TagGroupService $tagGroupService) {}

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
