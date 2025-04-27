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

        return [
            'scope_key'     => 'nullable|string|max:64',
            'title'         => 'required|string|max:255',
            'slug'          => "required|string|max:255|unique:contents,slug,{$id}",
            'content_type'  => 'required|string',
            'content_kind'  => 'required|string',
            'body'          => 'nullable|string',
            'meta'          => 'array',
            'status'        => 'nullable|in:draft,published,scheduled',
            'published_at'  => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
