<?php

namespace Modules\ContentModule\Events;

use Modules\ContentModule\Domain\Entities\ContentEntity;

class ContentReviewRequested
{
    public ContentEntity $entity;

    public function __construct(ContentEntity $entity)
    {
        $this->entity = $entity;
    }
}
