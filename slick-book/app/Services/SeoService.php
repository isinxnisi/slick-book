<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Site;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SeoService
{
    public function generateSeoForCurrentPage(?Site $site): array
    {
        if (!$site) return [];

        $seo = [
            'meta_title' => $site->seoSetting?->meta_title ?? $site->name,
            'meta_description' => $site->seoSetting?->meta_description ?? '',
            'meta_keywords' => $this->normalizeKeywordStringAsString($site->seoSetting?->meta_keywords ?? ''),
            'canonical_url' => $site->seoSetting?->canonical_url ?? url()->current(),
            'twitter_card_type' => $site->seoSetting?->twitter_card_type ?? 'summary',
            'noindex' => false,
        ];

        // 記事ページの場合
        if (Route::is('posts.view')) {
            $post = request()->route('post');
            $postSeo = $post->seoSetting;

            $siteKeywordsArr = $this->normalizeKeywordString($seo['meta_keywords']);
            $postKeywordsRaw = $postSeo?->meta_keywords ?: implode(',', $this->generateMetaKeywordsArr($post));
            $postKeywordsArr = $this->normalizeKeywordString($postKeywordsRaw);

            $keywords = array_unique(array_filter([...$siteKeywordsArr, ...$postKeywordsArr]));

            $seo = [
                'meta_title' => $postSeo?->meta_title ?? $post->title,
                'meta_description' => Str::limit($post->seo_description ?? '', 100),
                'meta_keywords' => implode(', ', $keywords),
                'canonical_url' => $postSeo?->canonical_url ?? url()->current(),
                'twitter_card_type' => 'summary_large_image',
                'noindex' => $post->is_draft ?? false,
            ];
        }

        // 検索ページ
        if (Route::is('search.*')) {
            $seo['noindex'] = true;
        }

        // ページネーション付きの一覧ページ
        if (request()->has('page') && request('page') > 1) {
            $seo['noindex'] = true;
        }

        return $seo;
    }

    protected function normalizeKeywordString(string $keywords): array
    {
        // 改行・カンマ両方に対応して分解し、トリム＆空除去
        return array_filter(array_map('trim', preg_split('/[\n,]+/', $keywords)));
    }

    protected function normalizeKeywordStringAsString(string $keywords): string
    {
        return implode(', ', $this->normalizeKeywordString($keywords));
    }

    public function generateMetaKeywordsArr(Post $post): array
    {
        $keywords = [];

        // タグ名
        foreach ($post->tags as $tag) {
            $keywords[] = $tag->name;
        }

        // [mark]...[/mark] 抽出
        preg_match_all('/\\[mark\\](.*?)\\[\\/mark\\]/', $post->body, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $word) {
                $keywords[] = trim($word);
            }
        }

        return array_unique($keywords);
    }
}
