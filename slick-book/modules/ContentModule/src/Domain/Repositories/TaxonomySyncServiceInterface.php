<?php
namespace Modules\ContentModule\Domain\Repositories;

use Modules\ContentModule\Domain\Entities\ContentEntity;

interface TaxonomySyncServiceInterface
{
    public function sync(ContentEntity $content, array $termIds): void;
}
