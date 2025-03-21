<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hierarchy;

class HierarchySeeder extends Seeder
{
    public function run()
    {
        // ルート階層（親なし）
        $root = Hierarchy::create([
            'title' => 'Root',
            'order' => 1,
            'status' => 'published',
            'parent_id' => null,
            'created_user' => 1
        ]);

        // 子カテゴリ
        $child1 = Hierarchy::create([
            'title' => 'Child 1',
            'order' => 1,
            'status' => 'published',
            'parent_id' => $root->id,
            'created_user' => 1
        ]);

        $child2 = Hierarchy::create([
            'title' => 'Child 2',
            'order' => 2,
            'status' => 'published',
            'parent_id' => $root->id,
            'created_user' => 1
        ]);

        // 孫カテゴリ
        Hierarchy::create([
            'title' => 'Grandchild 1-1',
            'order' => 1,
            'status' => 'published',
            'parent_id' => $child1->id,
            'created_user' => 1
        ]);
    }
}
