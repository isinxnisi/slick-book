<?php
// config/content.php

use Modules\ContentModule\Application\Strategies\{
    SlotArticleStrategy,
    LayoutArticleStrategy,
    LayoutSeriesStrategy,
    LayoutCollectionStrategy,
    LayoutGuidebookStrategy,
    StaticArticleStrategy,
    StaticCollectionStrategy,
    StaticGuidebookStrategy,
    SystemArticleStrategy,
    SystemGuidebookStrategy,
};

return [

    /**
     * 管理画面でタブとして表示する TYPE
     * 第一階層メニューに対応
     */
    'types' => [
        'slot'   => 'UIコンポーネント（slot）',
        'layout' => 'UIコンポーネント（layout）',
        'static' => '固定ページ',
        'system' => 'システム文言',
    ],

    /**
     * 各 TYPE 内で絞り込む KIND
     * 第二階層フィルタに対応
     */
    'kinds' => [
        'article'    => '記事 (Article)',
        'series'     => 'シリーズ (Series)',
        'collection' => 'コレクション (Collection)',
        'guidebook'  => 'ガイドブック (Guidebook)',
    ],

    /**
     * 利用可能な Strategy クラス群
     */
    'strategies' => [
        SlotArticleStrategy::class,
        LayoutArticleStrategy::class,
        LayoutSeriesStrategy::class,
        LayoutCollectionStrategy::class,
        LayoutGuidebookStrategy::class,
        StaticArticleStrategy::class,
        StaticCollectionStrategy::class,
        StaticGuidebookStrategy::class,
        SystemArticleStrategy::class,
        SystemGuidebookStrategy::class,
    ],

];
