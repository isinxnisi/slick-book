<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Post;
use App\Models\Tag;
use App\Models\TagGroup;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index(Request $request)
    {
        $site = app('CurrentSite'); // 公開サイト側ではこれで取得

        // 公開済み記事
        $posts = $site->posts()->published()->get();

        // 投稿が存在するカテゴリ
        $categories = $site->categories()
            ->whereHas('posts', fn($q) => $q->published())
            ->get();

        // 投稿と紐づくタグ（siteのタググループに属するもの）
        $tags = Tag::whereHas('posts', fn($q) => $q->where('site_id', $site->id)->published())
            ->forSite($site->id)
            ->get();

        // 投稿とタグがあるタググループ（siteに属する）
        $tagGroups = TagGroup::forSite($site->id)
            ->whereHas('tags.posts', fn($q) => $q->where('site_id', $site->id)->published())
            ->get();

        return response()
            ->view('blog.sitemap.xml', compact('site', 'posts', 'categories', 'tags', 'tagGroups'))
            ->header('Content-Type', 'application/xml');
    }
}
