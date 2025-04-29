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
        $rules['meta.items'] = 'required|array';
        return validator($data, $rules)->validate();
    }

    public function renderFormFields(?ContentEntity $entity = null): string
    {
        // Blade パーシャルをレンダー
        return view('content-module::admin.contents.forms.layout_article', [
            'entity' => $entity,
        ])->render();
    }
}
