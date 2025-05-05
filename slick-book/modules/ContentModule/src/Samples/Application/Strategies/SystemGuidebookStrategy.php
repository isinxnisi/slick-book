<?php

namespace Modules\ContentModule\Samples\Application\Strategies;

use Modules\ContentModule\Core\Application\Strategies\AbstractContentStrategy;

class SystemGuidebookStrategy extends AbstractContentStrategy
{
    public const TYPE = 'system';
    public const KIND = 'guidebook';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
