<?php
// config/content.php

use Modules\ContentModule\Application\Strategies\SlotArticleStrategy;
use Modules\ContentModule\Application\Strategies\LayoutSeriesStrategy;
use Modules\ContentModule\Enums\ContentType;
use Modules\ContentModule\Enums\ContentKind;

return [
    // 利用可能なコンテンツTYPE
    'types' => array_map(fn(ContentType $e) => $e->value, ContentType::cases()),

    // 利用可能なコンテンツKIND
    'kinds' => array_map(fn(ContentKind  $e) => $e->value, ContentKind::cases()),

    // アプリ側で自動登録したい Strategy クラス
    'strategies' => [
        SlotArticleStrategy::class,
        LayoutSeriesStrategy::class,
        // 追加の戦略があればここに追記
    ],
];
