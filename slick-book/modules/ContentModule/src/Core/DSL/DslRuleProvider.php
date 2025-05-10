<?php

namespace Modules\ContentModule\Core\DSL;

/**
 * Core層: HTTPに依存しないバリデーションルール提供
 */
class DslRuleProvider
{
    public function __construct(protected DslRegistry $registry) {}

    /**
     * @return array<string,string|array>
     */
    public function getRules(string $type, string $kind): array
    {
        $definition = $this->registry->get($type, $kind);
        $rules = [];
        foreach ($definition->getSections() as $section) {
            foreach ($section['fields'] as $field) {
                $rules["meta.{$field['name']}"] = $field['validation'];
            }
        }
        return $rules;
    }
}
