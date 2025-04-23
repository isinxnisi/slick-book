<?php
namespace Modules\ContentModule\Application\Services;

use Modules\ContentModule\Domain\Entities\ContentEntity;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Domain\Repositories\TaxonomySyncServiceInterface;
use Modules\ContentModule\Application\DTOs\ContentData;

class ContentService
{
    public function __construct(
        private ContentRepositoryInterface $repository,
        private TaxonomySyncServiceInterface $taxonomySync
    ) {}

    public function create(ContentData $data, array $taxonomyIds): ContentEntity
    {
        // Factory would create an entity from DTO
        $content = ContentEntity::fromData($data);

        // Perform domain rules here...
        // Persist content
        $this->repository->save($content);

        // Sync taxonomy if needed
        $this->taxonomySync->sync($content, $taxonomyIds);

        return $content;
    }
}
