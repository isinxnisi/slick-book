<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Entities\ContentEntity;

class LayoutGuidebookStrategy extends AbstractContentStrategy
{
    public const TYPE = 'layout';
    public const KIND = 'guidebook';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
