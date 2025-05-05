<?php

namespace Modules\ContentModule\Samples\Application\Strategies;

use Modules\ContentModule\Core\Application\Strategies\AbstractContentStrategy;

class SystemArticleStrategy extends AbstractContentStrategy
{
    public const TYPE = 'system';
    public const KIND = 'article';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
