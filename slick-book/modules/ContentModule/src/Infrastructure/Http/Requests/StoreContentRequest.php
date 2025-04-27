<?php
// modules/ContentModule/src/Infrastructure/Http/Requests/StoreContentRequest.php

namespace Modules\ContentModule\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 必要に応じて権限チェックを
        return true;
    }

    public function rules(): array
    {
        return [
            'scope_key'     => 'nullable|string|max:64',
            'title'         => 'required|string|max:255',
            'slug'          => 'required|string|max:255|unique:contents,slug',
            'content_type'  => 'required|string',
            'content_kind'  => 'required|string',
            'body'          => 'nullable|string',
            'meta'          => 'array',
            'status'        => 'nullable|in:draft,published,scheduled',
            'published_at'  => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
