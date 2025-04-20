<?php

namespace App\Services;

use App\Models\Category;
use App\Models\TagGroup;

class CurrentSiteDataProvider
{
    public function get(): array
    {
        $site = app('CurrentSite');

        $site->load([
            'categories' => fn ($q) => $q
                ->whereNull('parent_id')
                ->where('is_visible', true)
                ->with(['children' => fn ($q2) => $q2->with('childrenRecursive')]),
        ]);

        // 投稿件数を設定
        Category::setPostCountsForTree($site->categories, $site->id);

        // タググループ取得（depth:1階層）
        $tagGroups = TagGroup::getSiteTagGroupTree($site->id, 'public', 1);

        // 投稿件数を全階層に設定
        TagGroup::setPostCountsForTree($tagGroups, $site->id, 'public');

        return [
            'currentSite' => $site,
            'categories' => $site->categories->sortBy('order'),
            'tagGroups' => $tagGroups,
        ];
    }
}
