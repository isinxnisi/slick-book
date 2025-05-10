<?php

namespace Modules\ContentModule\Custom\DSL;

use Illuminate\Http\Request;
use Modules\ContentModule\Core\DSL\DslRegistry as CoreDslRegistry;
use Modules\ContentModule\Core\DSL\DslRuleProvider as CoreDslRuleProvider;

class DslRuleProvider extends CoreDslRuleProvider
{
    public function __construct(
        protected CoreDslRegistry $registry,
        protected Request $request
    ) {
        parent::__construct($registry);
    }

    public function getRules(string $type, string $kind): array
    {
        $rules = parent::getRules($type, $kind);

        $isUpdate = in_array($this->request->method(), ['PUT', 'PATCH'], true)
            || $this->request->route('id') !== null;

        if ($isUpdate) {
            $def = $this->registry->get($type, $kind);
            foreach ($def->getSections() as $section) {
                foreach ($section['fields'] as $field) {
                    if (isset($field['editable']) && $field['editable'] === false) {
                        unset($rules["meta.{$field['name']}"]);
                    }
                }
            }
        }

        return $rules;
    }
}
