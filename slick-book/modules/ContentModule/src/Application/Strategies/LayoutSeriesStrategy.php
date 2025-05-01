<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Entities\ContentEntity;

class LayoutSeriesStrategy extends AbstractContentStrategy
{
    public const TYPE = 'layout';
    public const KIND = 'series';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
