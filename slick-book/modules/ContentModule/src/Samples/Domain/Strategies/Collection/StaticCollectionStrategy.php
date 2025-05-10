<?php

namespace Modules\ContentModule\Samples\Domain\Strategies\Collection;

use Modules\ContentModule\Core\Application\Strategies\AbstractContentStrategy;

class StaticCollectionStrategy extends AbstractContentStrategy
{
    public const TYPE = 'collection';
    public const KIND = 'static';

    /**
     * target_type ごとに、どのコンテンツをリスト取得するかを定義
     * キーは target_type の値。
     * 'article' はアプリ側の Post モデルを例としています。
     */
    protected array $targetMappings = [
        'article' => [
            'service' => 'app',          // 'app' は Laravel の Post モデル例
            'model'   => \App\Models\Post::class,
            'pluck'   => ['title', 'id'],
        ],
        'product' => [
            'service' => 'content',      // ContentModule の別 TYPE×KIND
            'type'    => 'shop',
            'kind'    => 'product',
            'pluck'   => ['name', 'id'],
        ],
        'event' => [
            'service' => 'content',
            'type'    => 'calendar',
            'kind'    => 'event',
            'pluck'   => ['name', 'id'],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function getItems(string $targetType, array $context = []): array
    {
        // $context['type'], $context['kind'] も使えるように渡されます
        $map = match ($targetType) {
            'article' => ['service' => 'app', 'model' => \App\Models\Post::class, 'pluck' => ['title', 'id']],
            'product' => ['service' => 'content', 'type' => 'shop', 'kind' => 'product', 'pluck' => ['name', 'id']],
            'event'   => ['service' => 'content', 'type' => 'calendar', 'kind' => 'event', 'pluck' => ['name', 'id']],
            default   => null,
        };

        if (! $map) {
            return [];
        }

        if ($map['service'] === 'app') {
            return [$map['model']::pluck($map['pluck'][0], $map['pluck'][1])->toArray()];
        }

        $items = $this->service->list($map['type'], $map['kind']);
        return collect($items)->pluck($map['pluck'][0], $map['pluck'][1])->toArray();
    }
}
