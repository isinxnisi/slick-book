<?php

namespace Modules\ContentModule\Samples\Domain\Strategies\Widget;

use Modules\ContentModule\Core\Application\Strategies\AbstractContentStrategy;

class QuizWidgetStrategy extends AbstractContentStrategy
{
    public const TYPE = 'widget';
    public const KIND = 'quiz';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        return validator($data, $rules)->validate();
    }
}
