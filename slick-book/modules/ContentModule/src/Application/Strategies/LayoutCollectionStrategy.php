<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Entities\ContentEntity;

class LayoutCollectionStrategy extends AbstractContentStrategy
{
    public const TYPE = 'layout';
    public const KIND = 'collection';

    public function validate(array $data): array
    {
        $rules = $this->baseRules($data);
        $rules['meta.items'] = 'required|array';
        return validator($data, $rules)->validate();
    }

    public function renderFormFields(?ContentEntity $entity = null): string
    {
        return view('content-module::admin.contents.forms.layout_collection', [
            'entity' => $entity,
        ])->render();
    }
}
