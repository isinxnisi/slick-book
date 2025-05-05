<?php

namespace Modules\ContentModule\Core\Domain\Repositories;

use Modules\ContentModule\Core\Domain\Entities\ContentEntity;

interface TaxonomySyncServiceInterface
{
    public function sync(ContentEntity $content, array $termIds): void;
}
