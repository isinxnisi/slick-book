<?php

namespace Modules\ContentModule\Events;

use Modules\ContentModule\Domain\Entities\ContentEntity;

class ContentPublished
{
    public ContentEntity $entity;

    public function __construct(ContentEntity $entity)
    {
        $this->entity = $entity;
    }
}
