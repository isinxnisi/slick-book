<?php
// modules/ContentModule/src/Application/Services/ContentService.php

namespace Modules\ContentModule\Application\Services;

use Modules\ContentModule\Application\DTOs\ContentData;
use Modules\ContentModule\Domain\Contracts\ContentStrategyInterface;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Domain\Entities\ContentEntity;

class ContentService
{
    /** @var iterable<ContentStrategyInterface> */
    protected iterable $strategies;

    public function __construct(
        private ContentRepositoryInterface $repository
    ) {}

    public function setStrategies(iterable $strategies): void
    {
        $this->strategies = $strategies;
    }

    protected function getStrategy(string $type, string $kind): ContentStrategyInterface
    {
        foreach ($this->strategies as $s) {
            if ($s->supportsType() === $type && $s->supportsKind() === $kind) {
                return $s;
            }
        }
        throw new \RuntimeException("No strategy for type={$type}, kind={$kind}");
    }

    /** 新規作成・更新 */
    public function create(ContentData $data, array $taxonomyIds): ContentEntity
    {
        $strategy  = $this->getStrategy($data->content_type, $data->content_kind);
        $validated = $strategy->validate($data->toArray());
        $dto       = ContentData::fromArray($validated);
        $entity    = ContentEntity::fromData($dto);
        return $strategy->save($entity);
    }

    /**
     * コンテンツ一覧を取得
     *
     * @param string      $type TYPE フィルタ
     * @param string|null $kind KIND フィルタ（任意）
     * @return ContentEntity[] 配列で返却
     */
    public function list(string $type, ?string $kind = null): array
    {
        $filters = ['type' => $type];
        if ($kind !== null) {
            $filters['kind'] = $kind;
        }

        // リポジトリが返す array<ContentEntity> をそのまま返却
        return $this->repository->all($filters);
    }

    /**
     * 単一コンテンツ取得
     *
     * @param int $id
     * @return ContentEntity
     */
    public function get(int $id): ContentEntity
    {
        return $this->repository->find($id);
    }
}
