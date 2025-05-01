<?php
// modules/ContentModule/src/Infrastructure/Http/Requests/UpdateContentRequest.php

namespace Modules\ContentModule\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

        // ① 既存のバリドルール（title, slug, body など）
        $rules = [
            'title'         => 'required|string|max:255',
            'slug'          => "required|string|max:255|unique:contents,slug,{$id}",
            'content_type'  => 'required|string',
            'content_kind'  => 'required|string',
            'body'          => 'nullable|string',
            'meta'          => 'array',
            'status'        => 'nullable|in:draft,published,scheduled',
            'published_at'  => 'nullable|date_format:Y-m-d H:i:s',
        ];

        // ② メタスキーマから該当フィールドのバリデーションを取得
        $type   = $this->input('content_type', '');
        $kind   = $this->input('content_kind', '');
        $mapping = config('meta_schema.mapping');
        $schemas = config('meta_schema.schemas');

        // TYPE.KIND キー or default
        $key  = "{$type}.{$kind}";
        $sets = $mapping[$key] ?? $mapping['default'];

        // 各セットのフィールド定義をマージ
        foreach ($sets as $set) {
            if (! empty($schemas[$set]['fields'])) {
                foreach ($schemas[$set]['fields'] as $field) {
                    // meta.<name> => validation
                    $rules["meta.{$field['name']}"] = $field['validation'];
                }
            }
        }

        return $rules;
    }
}
