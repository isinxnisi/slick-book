<?php

namespace App\Services;

use Illuminate\Support\Collection;

class TagGroupService
{
    /**
     * タグに purpose を注入する
     * （タグが紐づく最初のマスタグループから purpose を取得）
     */
    public function injectPurposeIntoTags($groups)
    {
        foreach ($groups as $group) {
            foreach ($group->tags as $tag) {
                $firstMasterGroup = $tag->tagGroups
                    ->filter(fn ($g) => $g->sites->isEmpty()) // サイトに紐づいていないマスタグループのみ
                    ->first();

                $tag->purpose = $firstMasterGroup?->purpose ?? 'public';
            }

            if ($group->children && $group->children->isNotEmpty()) {
                $this->injectPurposeIntoTags($group->children);
            }
        }
    }


    /**
     * ネストされたグループをフラットなリストに変換
     */
    public function flattenGroups(Collection $groups): Collection
    {
        $flattened = collect();

        foreach ($groups as $group) {
            $flattened->push($group);

            if ($group->children && $group->children->isNotEmpty()) {
                $flattened = $flattened->merge($this->flattenGroups($group->children));
            }
        }

        return $flattened;
    }
}