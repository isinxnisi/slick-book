<?php

namespace Modules\ContentModule\Core\Application\Strategies;

interface ItemProviderInterface
{
    /**
     * @param  string $key     例: 'article' や 'product'
     * @param  array  $context 戦略固有のコンテキスト（type/kindなど）
     * @return array           [value => label, …]
     */
    public function getItems(string $key, array $context = []): array;
}
