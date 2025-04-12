<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\TagGroup;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

/**
 * カテゴリ Controller class
 */
class TagGroupController extends Controller
{
    /**
     * カテゴリ｜プレビュー
     *
     * @param string $slug
     * @return View
     */
    public function view($slug)
    {
        $site = app('CurrentSite');

        $tagGroup = TagGroup::forSite($site->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $groupTags = $tagGroup->tags()->forPurpose('public')->get();

        // 投稿一覧（このタグに紐づくもの）
        $posts = Post::whereHas('tags', fn ($q) => $q->where('purpose', 'public')->whereIn('tags.id', $groupTags->pluck('id')))
            ->where('site_id', $site->id)
            ->published()
            ->latest('published_at')
            ->get();

        return view('blog.tag-group', compact('tagGroup', 'posts', 'groupTags'));
    }
}
