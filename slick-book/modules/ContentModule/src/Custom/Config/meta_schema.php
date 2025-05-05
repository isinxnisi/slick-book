<?php
// modules/ContentModule/config/meta_schema.php

return [

    /*
    |--------------------------------------------------------------------------
    | 利用可能なスキーマセット一覧
    |--------------------------------------------------------------------------
    */
    'sets' => [
        'basic'  => '基本フィールド',
        'seo'    => 'SEO設定用',
        'social' => 'OG/Twitterカード用',
    ],

    /*
    |--------------------------------------------------------------------------
    | スキーマセットごとのフィールド定義
    |--------------------------------------------------------------------------
    */
    'schemas' => [

        // basic（すべてのCONTENTで共通）
        'basic' => [
            'fields' => [
                [
                    'name'       => 'subtitle',
                    'label'      => 'サブタイトル',
                    'type'       => 'text',
                    'validation' => 'nullable|string|max:255',
                    'order'      => 10,
                ],
                [
                    'name'       => 'featured_image',
                    'label'      => 'アイキャッチ画像',
                    'type'       => 'image',
                    'validation' => 'nullable|image|max:2048',
                    'order'      => 20,
                ],
            ],
        ],

        // seo
        'seo' => [
            'fields' => [
                [
                    'name'       => 'meta_description',
                    'label'      => 'メタディスクリプション',
                    'type'       => 'textarea',
                    'validation' => 'nullable|string|max:160',
                    'order'      => 30,
                ],
                [
                    'name'       => 'meta_keywords',
                    'label'      => 'キーワード',
                    'type'       => 'tags',
                    'validation' => 'nullable|string',
                    'order'      => 40,
                ],
            ],
        ],

        // social
        'social' => [
            'fields' => [
                [
                    'name'       => 'og_image',
                    'label'      => 'OG画像',
                    'type'       => 'image',
                    'validation' => 'nullable|image',
                    'order'      => 50,
                ],
                [
                    'name'       => 'twitter_card',
                    'label'      => 'Twitterカード種類',
                    'type'       => 'select',
                    'options'    => ['summary' => 'Summary', 'large' => 'Large Image'],
                    'validation' => 'required|in:summary,large',
                    'order'      => 60,
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | TYPE×KINDごとに利用するスキーマセットのマッピング
    |--------------------------------------------------------------------------
    */
    'mapping' => [
        'slot.article'      => ['basic', 'seo'],
        'layout.article'    => ['basic', 'seo', 'social'],
        'layout.series'     => ['basic', 'social'],
        'static.article'    => ['basic', 'seo', 'social'],
        'static.collection' => ['basic', 'seo', 'social'],
        'system.article'    => ['basic'],
        'system.guidebook'  => ['basic'],
        'default'           => ['basic'],
    ],

];
