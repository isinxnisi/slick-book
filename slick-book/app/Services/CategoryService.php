<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CategoryService
{
    /**
     * ネストされたグループをフラットなリストに変換
     */
    public function flatten(Collection $categories): Collection
    {
        $flattened = collect();

        foreach ($categories as $category) {
            $flattened->push($category);

            if ($category->children && $category->children->isNotEmpty()) {
                $flattened = $flattened->merge($this->flatten($category->children));
            }
        }

        return $flattened;
    }
}