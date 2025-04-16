<?php

namespace App\Services;

use App\Models\Site;
use Illuminate\Support\Facades\Route;

class SeoService
{
    public function generateSeoForCurrentPage(?Site $site): array
    {
        if (!$site) return [];

        // 記事詳細なら記事SEO優先（例: 'posts.show'）
        if (Route::is('posts.show')) {
            $post = request()->route('post');
            return [
                'meta_title' => $post->meta_title ?? $post->title,
                'meta_description' => $post->meta_description ?? \Str::limit(strip_tags($post->body), 100),
                'canonical_url' => $post->canonical_url ?? url()->current(),
                'twitter_card_type' => 'summary_large_image',
            ];
        }

        // サイトのSEO設定（デフォルト）
        return $site->seoSetting ? $site->seoSetting->toArray() : [];
    }
}
