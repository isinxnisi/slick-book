<?php

namespace Modules\ContentModule\Application\Services;

use Modules\ContentModule\Domain\Entities\ContentEntity;
use Modules\ContentModule\Application\DTOs\ContentData;
use Modules\ContentModule\Domain\Contracts\ContentStrategyInterface;

class ContentService
{
    /** @var ContentStrategyInterface[] */
    protected iterable $strategies;

    public function setStrategies(iterable $strategies): void
    {
        $this->strategies = $strategies;
    }

    protected function getStrategy(string $type, string $kind): ContentStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supportsType() === $type
                && $strategy->supportsKind() === $kind
            ) {
                return $strategy;
            }
        }
        throw new \RuntimeException("No strategy for type={$type}, kind={$kind}");
    }

    public function create(ContentData $data, array $taxonomyIds): ContentEntity
    {
        // ① 戦略の解決
        $strategy = $this->getStrategy($data->content_type, $data->content_kind);

        // ② validate → DTO再構築
        $validated = $strategy->validate($data->toArray());
        $dto       = ContentData::fromArray($validated);

        // ③ Entity生成
        $entity    = ContentEntity::fromData($dto);

        // ④ 永続化
        $saved     = $strategy->save($entity);

        // ⑤ タクソノミー連携など
        // $this->taxonomySync->sync($saved, $taxonomyIds);

        return $saved;
    }
}
