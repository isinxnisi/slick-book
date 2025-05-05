<?php

namespace Modules\ContentModule\Infrastructure\Repositories;

use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Domain\Entities\ContentEntity;
use Modules\ContentModule\Application\DTOs\ContentData;
use Modules\ContentModule\Infrastructure\Eloquent\Models\ContentModel;

class EloquentContentRepository implements ContentRepositoryInterface
{
    /**
     * 全件取得
     * @param array $filters
     * @return ContentEntity[]
     */
    public function all(array $filters = []): array
    {
        $query = ContentModel::query();
        if (!empty($filters['type'])) {
            $query->where('content_type', $filters['type']);
        }
        if (!empty($filters['kind'])) {
            $query->where('content_kind', $filters['kind']);
        }

        return $query->get()
                     ->map(fn(ContentModel $m) => $this->toEntity($m))
                     ->all();
    }

    /**
     * 単一取得
     */

    public function find(int $id): ContentEntity
    {
        $model = ContentModel::findOrFail($id);
        return $this->toEntity($model);
    }

    /**
     * スラッグから取得（公開側用）
     */
    public function findBySlug(string $slug): ?ContentEntity
    {
        $model = ContentModel::where('slug', $slug)->first();
        return $model ? $this->toEntity($model) : null;
    }

    /**
     * 保存（新規/更新）
     * @param ContentEntity $entity
     * @param int[] $taxonomyIds
     * @return ContentEntity
     */
    public function save(ContentEntity $entity, array $taxonomyIds = []): ContentEntity
    {
        // モデル取得 or 新規
        $model = $entity->getId()
            ? ContentModel::findOrFail($entity->getId())
            : new ContentModel();

        // エンティティ → DTO → 配列
        $data = ContentData::fromArray($entity->toArray())->toArray();

        // fillable に準拠して一括代入
        $model->fill([
            'scope_key'    => $data['scope_key'],
            'title'        => $data['title'],
            'slug'         => $data['slug'],
            'content_type' => $data['content_type'],
            'content_kind' => $data['content_kind'],
            'body'         => $data['body'],
            'meta'         => $data['meta'],
            'status'       => $data['status'],
            'published_at' => $data['published_at'],
        ]);

        // 保存
        $model->save();

        // タクソノミー同期
        if (!empty($taxonomyIds)) {
            $model->taxonomies()->sync($taxonomyIds);
        }

        return $this->toEntity($model);
    }

    /**
     * 削除
     */
    public function delete(int $id): void
    {
        ContentModel::destroy($id);
    }

    /**
     * モデル → ドメインエンティティ
     */
    private function toEntity(ContentModel $model): ContentEntity
    {
        // DTO 経由で基本フィールドを注入
        $dto    = ContentData::fromArray($model->toArray());
        $entity = ContentEntity::fromData($dto);

        // タクソノミーIDを同期
        $entity->setTaxonomyTermIds(
            $model->taxonomyTerms()->pluck('taxonomy_terms.id')->toArray()
        );

        return $entity;
    }
}
