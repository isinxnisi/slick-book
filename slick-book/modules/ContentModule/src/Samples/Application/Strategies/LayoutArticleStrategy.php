<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Entities\ContentEntity;

class LayoutArticleStrategy extends AbstractContentStrategy
{
    public const TYPE = 'layout';
    public const KIND = 'article';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }

    public function render()
    {
        // 仮
        return 'LayoutArticle';
    }
}
