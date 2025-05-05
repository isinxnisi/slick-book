<?php

namespace Modules\ContentModule\Core\Domain\Repositories;

use Modules\ContentModule\Core\Domain\Entities\ContentEntity;

interface ContentRepositoryInterface
{
    /**
     * コンテンツ一覧を取得
     *
     * @param array $filters TYPE/KIND などのフィルタ
     * @return ContentEntity[]
     */
    public function all(array $filters = []): array;

    /**
     * ID で単一取得
     *
     * @param int $id
     * @return ContentEntity
     */
    public function find(int $id): ContentEntity;

    /**
     * スラッグで取得（公開用）
     *
     * @param string $slug
     * @return ContentEntity|null
     */
    public function findBySlug(string $slug): ?ContentEntity;

    /**
     * 新規 or 更新 保存処理
     *
     * @param ContentEntity $entity
     * @param int[] $taxonomyTermIds
     * @return ContentEntity
     */
    public function save(ContentEntity $entity, array $taxonomyTermIds = []): ContentEntity;

    /**
     * 削除
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;
}
