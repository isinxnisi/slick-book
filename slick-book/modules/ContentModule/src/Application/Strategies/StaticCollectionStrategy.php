<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Entities\ContentEntity;

class StaticCollectionStrategy extends AbstractContentStrategy
{
    public const TYPE = 'static';
    public const KIND = 'collection';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
