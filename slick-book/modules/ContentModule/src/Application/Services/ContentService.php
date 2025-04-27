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

    /**
     * Ajax 用にフォーム描画で直接戦略を取り出す
     *
     * @param string $type
     * @param string $kind
     * @return ContentStrategyInterface
     */
    public function resolveStrategy(string $type, string $kind): ContentStrategyInterface
    {
        // protected な getStrategy() を呼び出して返却
        return $this->getStrategy($type, $kind);
    }

    /**
     * TYPE×KIND に合致するストラテジーを探す内部メソッド
     *
     * @throws \RuntimeException
     */
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
    public function create(ContentData $data, array $taxonomyIds = []): ContentEntity
    {
        $data = $this->prepareData($data);
        $entity = ContentEntity::fromData($data);
        return $this->getStrategy($data->content_type, $data->content_kind)
                    ->save($entity);
    }

    /**
     * 更新用メソッド
     */
    public function update(int $id, ContentData $data, array $taxonomyIds = []): ContentEntity
    {
        // ID をセット
        $data->id = $id;

        // バリデーション〜永続化
        $data = $this->prepareData($data);
        $entity = ContentEntity::fromData($data);
        return $this->getStrategy($data->content_type, $data->content_kind)
                    ->save($entity);
    }

    /**
     * validate → DTO 再生成 の共通処理
     */
    protected function prepareData(ContentData $data): ContentData
    {
        $strategy  = $this->getStrategy($data->content_type, $data->content_kind);
        $validated = $strategy->validate($data->toArray());
        return ContentData::fromArray($validated);
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

    /**
     * 削除
     *
     * @param int $id
     * @return ContentEntity
     */
    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}
