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
            ['id' => $content->id],
            $content->toArray()
        );
        return ContentEntity::fromModel($model);
    }

    public function find(int $id): ?ContentEntity
    {
        $model = ContentModel::find($id);
        return $model ? ContentEntity::fromModel($model) : null;
    }

    public function delete(int $id): void
    {
        ContentModel::destroy($id);
    }
}
