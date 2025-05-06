<?php

namespace Modules\ContentModule\Samples\Domain\Strategies\Collection;

use Modules\ContentModule\Core\Application\Strategies\AbstractContentStrategy;

class StaticCollectionStrategy extends AbstractContentStrategy
{
    public const TYPE = 'collection';
    public const KIND = 'static';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
