<?php

namespace Modules\ContentModule\Core\Infrastructure\Eloquent\Repositories;

use Illuminate\Support\Facades\Schema;
use Modules\ContentModule\Core\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Core\Domain\Entities\ContentEntity;
use Modules\ContentModule\Core\Application\DTOs\ContentData;
use Modules\ContentModule\Core\Infrastructure\Eloquent\Models\ContentModel;

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
                     ->map(fn(ContentModel $model) => $this->toEntity($model))
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
     * @param int[] $taxonomyTermIds
     * @return ContentEntity
     */
    public function save(ContentEntity $entity, array $taxonomyTermIds = []): ContentEntity
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

        // タクソノミータームIDを同期
        if (! empty($taxonomyTermIds)) {
            $model->taxonomyTerms()->sync($taxonomyTermIds);
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
     * @param ContentModel $model
     * @return ContentEntity
     */
    private function toEntity(ContentModel $model): ContentEntity
    {
        // DTO 経由で基本フィールドを注入
        $dto    = ContentData::fromArray($model->toArray());
        $entity = ContentEntity::fromData($dto);

        if (Schema::hasTable('content_taxonomy_term') && Schema::hasTable('taxonomy_terms')) {
            try {
                // タクソノミータームIDを同期
                $ids = $model->taxonomyTerms()->pluck('taxonomy_terms.id')->toArray();
                $entity->setTaxonomyTermIds($ids);
            } catch (\Throwable $e) {
                // ログを出す or 無視
            }
        }

        return $entity;
    }
}
