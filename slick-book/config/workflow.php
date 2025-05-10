<?php

return [

    'content' => [
        // 状態を保持するプロパティ
        'marking_store' => [
            'type'     => 'single_state',
            'property' => 'status',
        ],

        // 対象エンティティクラス
        'supports' => [
            Modules\ContentModule\Core\Domain\Entities\ContentEntity::class,
        ],

        // 定義可能な状態
        'places' => [
            'draft',
            'review',
            'published',
            'archived',
        ],

        // 遷移イベントと前後状態
        'transitions' => [
            'to_review' => [
                'from' => ['draft'],
                'to'   => 'review',
            ],
            'publish' => [
                'from' => ['review'],
                'to'   => 'published',
            ],
            'archive' => [
                'from' => ['published'],
                'to'   => 'archived',
            ],
        ],
    ],

];
