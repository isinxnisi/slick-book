<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagGroupRequest extends FormRequest
{
    public function rules(): array
    {
        $tagGroupId = $this->route('tag_group')?->id ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tag_groups', 'slug')->ignore($tagGroupId)],
            'purpose' => ['required', Rule::in(array_keys(config('tags.purposes')))],
            'color' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'purpose.in' => '選択された用途（purpose）は無効です。',
        ];
    }
}
