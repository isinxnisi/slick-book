<?php

namespace Modules\ContentModule\Samples\Domain\Strategies\Series;

use Modules\ContentModule\Core\Application\Strategies\AbstractContentStrategy;

class AutoSeriesStrategy extends AbstractContentStrategy
{
    public const TYPE = 'series';
    public const KIND = 'auto';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
