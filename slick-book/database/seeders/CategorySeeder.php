<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $siteId = 1; // 任意のサイトID

        $root1 = Category::create([
            'title' => 'カテゴリA',
            'slug' => 'category-a',
            'site_id' => $siteId,
            'order' => 1,
        ]);

        $root2 = Category::create([
            'title' => 'カテゴリB',
            'slug' => 'category-b',
            'site_id' => $siteId,
            'order' => 2,
        ]);

        Category::create([
            'title' => '子カテゴリB-1',
            'slug' => 'category-b-1',
            'site_id' => $siteId,
            'parent_id' => $root2->id,
            'order' => 1,
        ]);
    }
}
