<?php
// modules/ContentModule/src/Infrastructure/Http/Requests/StoreContentRequest.php

namespace Modules\ContentModule\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 必要に応じて権限チェックを
        return true;
    }

    public function rules(): array
    {
        $scopeKey = $this->input('scope_key');

        // 事前準備
        $types    = array_keys(config('content.types'));
        $mapping  = config('meta_schema.mapping');
        $schemas  = config('meta_schema.schemas');

        $type     = $this->input('content_type', '');
        $kind     = $this->input('content_kind', '');

        // ベースルール
        $rules = [
            'title'         => 'required|string|max:255',
            'slug' => [
                'required','string','max:255',
                Rule::unique('contents')
                    ->where(fn($q) => $q->where('scope_key', $scopeKey)),
            ],
            'content_type'  => ['required','string', Rule::in($types)],
            'content_kind'  => [
                'required','string',
                // TYPE×KIND の組み合わせチェック
                function($attr, $value, $fail) use ($mapping) {
                    $type = request('content_type','');
                    $key  = "{$type}.{$value}";
                    if (! isset($mapping[$key]) && $key !== 'default') {
                        $fail('この種別は選択できません。');
                    }
                },
            ],
            'body'          => 'nullable|string',
            'meta'          => 'array',
            'status'        => ['nullable', Rule::in(['draft','published','scheduled'])],
            'published_at'  => 'nullable|date_format:Y-m-d H:i:s',
        ];

        // メタスキーマからフィールドごとのルールをマージ
        $key  = "{$type}.{$kind}";
        $sets = $mapping[$key] ?? $mapping['default'];
        $schemas = config('meta_schema.schemas');

        foreach ($sets as $set) {
            foreach ($schemas[$set]['fields'] as $field) {
                $rules["meta.{$field['name']}"] = $field['validation'];
            }
        }

        return $rules;
    }
}
