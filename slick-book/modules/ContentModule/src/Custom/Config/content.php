<?php

return [

    /**
     * 管理画面でタブとして表示する TYPE
     * 第一階層メニューに対応
     */
    'types' => [
        'collection' => 'コレクション',
        'series'     => 'シリーズ',
        'widget'     => 'ウィジェット',
    ],

    /**
     * 各 TYPE 内で絞り込む KIND
     * 第二階層フィルタに対応
     */
    'kinds' => [
        'collection' => [
            'static'  => '静的コレクション',
            'dynamic' => '動的コレクション',
        ],
        'series' => [
            'manual' => '手動シリーズ',
            'auto'   => '自動シリーズ',
        ],
        'widget' => [
            'quiz' => 'クイズ',
        ],
    ],

    /**
     * 利用可能な Strategy クラス群
     */
    'strategies' => [],

    /**
     * DSL定義ファイルの検索パス一覧
     */
    'dsl_paths' => [
        // アプリケーション側で定義したDSL
        base_path('resources/dsl/definitions'),
        // パッケージ側のデフォルトDSL
        dirname(__DIR__, 4) . '/dsl/definitions',
    ],
];
