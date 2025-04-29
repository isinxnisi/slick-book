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
        $rules['meta.items'] = 'required|array';
        return validator($data, $rules)->validate();
    }

    public function renderFormFields(?ContentEntity $entity = null): string
    {
        // Blade パーシャルをレンダー
        return view('content-module::admin.contents.forms.slot_article', [
            'entity' => $entity,
        ])->render();
    }
}
