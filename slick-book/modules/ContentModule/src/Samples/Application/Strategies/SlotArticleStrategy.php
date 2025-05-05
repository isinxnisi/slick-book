<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Entities\ContentEntity;

class SlotArticleStrategy extends AbstractContentStrategy
{
    public const TYPE = 'slot';
    public const KIND = 'article';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
