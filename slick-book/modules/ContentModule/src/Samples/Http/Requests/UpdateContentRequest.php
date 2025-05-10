<?php

namespace Modules\ContentModule\Samples\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\ContentModule\Core\DSL\DslRegistry;
use Modules\ContentModule\Core\DSL\DslRuleProvider;

class UpdateContentRequest extends FormRequest
{
    /**
     * サンプル側独自の権限チェックがあればここで。
     * 親の authorize() を使う場合は parent::authorize() を呼べばOK。
     */
    public function authorize(): bool
    {
        return true; // または parent::authorize();
    }

    public function prepareForValidation(): void
    {
        // TYPE/KIND から DslDefinition を取ってくる
        $def  = app(DslRegistry::class)
            ->get($this->input('content_type'), $this->input('content_kind'));
        $all  = $this->all();
        $keep = [];

        // editable=true の meta フィールドだけを残す
        foreach ($def->getSections() as $section) {
            foreach ($section['fields'] as $field) {
                $name = $field['name'];
                if (($field['editable'] ?? true) === false) {
                    // 更新時は input から取り除く
                    unset($all['meta'][$name]);
                } else {
                    $keep[$name] = $all['meta'][$name] ?? null;
                }
            }
        }

        // リクエストの meta を上書き
        $this->merge([
            'meta' => $keep,
        ]);
    }

    /**
     * Core の動的ルール + サンプル固有ルール をマージして返す
     */
    public function rules(): array
    {
        $id       = $this->route('id');            // 更新時はルートパラメータから取得
        $scopeKey = $this->input('scope_key');     // input() でOK

        // TYPE/KIND の組み合わせチェック用
        $mapping = config('meta_schema.mapping');
        $types   = array_keys(config('content.types'));

        // サンプル固有ルールを定義
        $custom = [
            'title'         => 'required|string|max:255',
            'slug'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('contents')
                    ->ignore($id)
                    ->where(fn($q) => $q->where('scope_key', $scopeKey)),
            ],
            'content_type'  => ['required', 'string', Rule::in($types)],
            'content_kind'  => [
                'required',
                'string',
                // TYPE×KIND の組み合わせチェック
                function ($attr, $value, $fail) use ($mapping) {
                    $key = "{$this->input('content_type')}.{$value}";
                    if (! isset($mapping[$key]) && $key !== 'default') {
                        $fail('この種別は選択できません。');
                    }
                },
            ],
            'body'          => 'nullable|string',
            'meta'          => 'array',
            'status'        => ['nullable', Rule::in(['draft', 'published', 'scheduled'])],
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
