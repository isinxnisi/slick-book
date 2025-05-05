<?php

namespace Modules\ContentModule\Samples\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // {id} プレースホルダを使ってユニーク制約を更新時に除外
        $id = $this->route('id');
        $scopeKey = $this->input('scope_key');

        // 事前準備
        $types    = array_keys(config('content.types'));
        $mapping  = config('meta_schema.mapping');

        $type     = $this->input('content_type', '');
        $kind     = $this->input('content_kind', '');

        // ベースルール
        $rules = [
            'title'         => 'required|string|max:255',
            'slug' => [
                'required','string','max:255',
                Rule::unique('contents')
                    ->ignore($id)
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
