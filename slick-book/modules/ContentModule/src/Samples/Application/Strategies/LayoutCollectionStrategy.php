<?php

namespace Modules\ContentModule\Samples\Application\Strategies;

use Modules\ContentModule\Core\Application\Strategies\AbstractContentStrategy;

class LayoutCollectionStrategy extends AbstractContentStrategy
{
    public const TYPE = 'layout';
    public const KIND = 'collection';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
