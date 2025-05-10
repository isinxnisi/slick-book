<?php

namespace Modules\ContentModule\Samples\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\ContentModule\Core\DSL\DslRuleProvider;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 必要に応じて権限チェックを
        return true;
    }

    public function rules(): array
    {
        $scopeKey = $this->input('scope_key');     // input() でOK

        // TYPE/KIND の組み合わせチェック用
        $mapping = config('meta_schema.mapping');
        $types   = array_keys(config('content.types'));

        // サンプル固有ルールを定義
        $custom = [
            'title'         => 'required|string|max:255',
            'slug'          => [
                'required','string','max:255',
                Rule::unique('contents')
                    ->where(fn($q) => $q->where('scope_key', $scopeKey)),
            ],
            'content_type'  => ['required','string', Rule::in($types)],
            'content_kind'  => [
                'required','string',
                // TYPE×KIND の組み合わせチェック
                function($attr, $value, $fail) use ($mapping) {
                    $key = "{$this->input('content_type')}.{$value}";
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

        // meta_schema の fields ルールを追加
        $type   = $this->input('content_type', '');
        $kind   = $this->input('content_kind', '');
        $key    = "{$type}.{$kind}";
        $sets   = $mapping[$key] ?? $mapping['default'];
        $schemas = config('meta_schema.schemas');

        foreach ($sets as $set) {
            foreach ($schemas[$set]['fields'] as $field) {
                // nested array で meta.フィールド名
                $custom["meta.{$field['name']}"] = $field['validation'];
            }
        }

        // Core側(DslRegistry由来)のルールを取得
        $rules = app(DslRuleProvider::class)->getRules($type, $kind);

        // マージして返却
        return array_merge($rules, $custom);
    }
}
