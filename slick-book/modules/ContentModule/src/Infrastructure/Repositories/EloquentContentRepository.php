<?php
namespace Modules\ContentModule\Infrastructure\Repositories;

use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Domain\Entities\ContentEntity;
use Modules\ContentModule\Infrastructure\Eloquent\Models\ContentModel;

class EloquentContentRepository implements ContentRepositoryInterface
{
    public function save(ContentEntity $content): ContentEntity
    {
        $model = ContentModel::updateOrCreate(
            ['id' => $content->getId()],
            $content->toArray()
        );
        return ContentEntity::fromModel($model);
    }

    public function all(array $filters = []): array
    {
        $query = ContentModel::query();
        if (isset($filters['type'])) {
            $query->where('content_type', $filters['type']);
        }
        if (isset($filters['kind'])) {
            $query->where('content_kind', $filters['kind']);
        }
        return $query->get()
                     ->map(fn(ContentModel $m) => ContentEntity::fromModel($m))
                     ->all();  // Collection→array<ContentEntity>
    }

    public function find(int $id): ContentEntity
    {
        $model = ContentModel::findOrFail($id);
        return ContentEntity::fromModel($model);
    }

    public function delete(int $id): void
    {
        ContentModel::destroy($id);
    }
}
