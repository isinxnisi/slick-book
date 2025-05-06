<?php

namespace Modules\ContentModule\Samples\Domain\Strategies\Series;

use Modules\ContentModule\Core\Application\Strategies\AbstractContentStrategy;

class ManualSeriesStrategy extends AbstractContentStrategy
{
    public const TYPE = 'series';
    public const KIND = 'manual';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
