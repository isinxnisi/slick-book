<?php
namespace Modules\ContentModule\Domain\Repositories;

use Modules\ContentModule\Domain\Entities\ContentEntity;

interface ContentRepositoryInterface
{
    public function save(ContentEntity $content): ContentEntity;
    public function find(int $id): ?ContentEntity;
    public function delete(int $id): void;
}
