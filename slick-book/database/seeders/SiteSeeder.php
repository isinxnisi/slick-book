<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        Site::create([
            'name' => 'Slick Book 本館',
            'slug' => 'slick-book-main',
            'description' => 'メインサイトです',
        ]);

        Site::create([
            'name' => 'Slick Book 開発ログ',
            'slug' => 'devlog',
            'description' => '開発者向けのブログサイト',
        ]);

        Site::create([
            'name' => 'Slick Book テストサイト',
            'slug' => 'testing',
            'description' => '機能検証用のプレサイト',
        ]);
    }
}