<?php

return [
    'purposes' => [
        'public' => '公開用',      // フロント表示・検索に使うタグ
        'seo' => 'SEO用',         // 検索エンジン向け構造化・インデックス用
        'system' => 'システム用', // 内部処理用・条件判定など
        'user' => 'ユーザー用',   // ユーザー操作で追加されるタグ等
    ],

    'purpose_styles' => [
        'public' => [
            'bg' => '#38bdf8',
            'text' => '#333',
            'border' => '#38bdf8',
        ],
        'seo'    => [
            'bg' => '#facc15',
            'text' => '#333',
            'border' => '#facc15',
        ],
        'system' => [
            'bg' => '#9ca3af',
            'text' => '#333',
            'border' => '#9ca3af',
        ],
        'user'   => [
            'bg' => '#10b981',
            'text' => '#333',
            'border' => '#10b981',
        ],
    ],

    'post_purpose_styles' => [
        'public' => [
            'bg' => '#24637f',
            'text' => '#FFF',
            'border' => '#24637f',
        ],
    ],
];
