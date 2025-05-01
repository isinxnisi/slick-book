<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Entities\ContentEntity;

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
