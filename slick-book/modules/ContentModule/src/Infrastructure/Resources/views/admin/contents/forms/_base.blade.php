{{-- resources/views/admin/contents/forms/_base.blade.php --}}

@isset($entity)
<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">タイトル</label>
        <input type="text" name="title" value="{{ old('title', $entity?->getTitle() ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300" />
        <x-content-module::input-error class="mt-2" :messages="$errors->get('title')" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">スラッグ</label>
        <input type="text" name="slug" value="{{ old('slug', $entity?->getSlug() ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300" />
        <x-content-module::input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">説明 (body)</label>
        <textarea name="body" class="mt-1 block w-full rounded-md border-gray-300" rows="3">{{ old('body', $entity?->getBody() ?? '') }}</textarea>
        <x-content-module::input-error class="mt-2" :messages="$errors->get('body')" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">並び順 (meta.order)</label>
        <input type="number" name="meta[order]" value="{{ old('meta.order', $entity?->getMeta()['order'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300" />
        <x-content-module::input-error class="mt-2" :messages="$errors->get('meta[order]')" />
    </div>
</div>

@foreach ($fields as $field)
    <div class="form-group mb-4">
        <label for="meta_{{ $field['name'] }}">{{ $field['label'] }}</label>

        @switch($field['type'])
            @case('text')
                <input
                    type="text"
                    id="meta_{{ $field['name'] }}"
                    name="meta[{{ $field['name'] }}]"
                    value="{{ old("meta.{$field['name']}", $entity->getMetaValue($field['name'])) }}"
                    class="form-control"
                />
                @break

            @case('textarea')
                <textarea
                    id="meta_{{ $field['name'] }}"
                    name="meta[{{ $field['name'] }}]"
                    class="form-control"
                >{{ old("meta.{$field['name']}", $entity->getMetaValue($field['name'])) }}</textarea>
                @break

            @case('select')
                <select
                    id="meta_{{ $field['name'] }}"
                    name="meta[{{ $field['name'] }}]"
                    class="form-control"
                >
                    @foreach ($field['options'] as $optValue => $optLabel)
                        <option
                            value="{{ $optValue }}"
                            @if(old("meta.{$field['name']}", $entity->getMetaValue($field['name'])) == $optValue) selected @endif
                        >{{ $optLabel }}</option>
                    @endforeach
                </select>
                @break

            @case('image')
                <input type="file" name="meta[{{ $field['name'] }}]" class="form-control-file" />
                @if($entity->getMetaValue($field['name']))
                    <img src="{{ Storage::url($entity->getMetaValue($field['name'])) }}"
                         class="img-thumbnail mt-2" />
                @endif
                @break

            @case('color')
                <input
                    type="color"
                    id="meta_{{ $field['name'] }}"
                    name="meta[{{ $field['name'] }}]"
                    value="{{ old("meta.{$field['name']}", $entity->getMetaValue($field['name'])) }}"
                    class="form-control form-control-color"
                />
                @break

            @case('tags')
                <input
                    type="text"
                    name="meta[{{ $field['name'] }}]"
                    value="{{ old("meta.{$field['name']}", implode(',', $entity->getMetaValue($field['name']) ?: [])) }}"
                    class="form-control"
                    placeholder="カンマ区切りで入力"
                />
                @break

            {{-- 他のタイプも同様に追加可能 --}}
        @endswitch

        @error("meta.{$field['name']}")
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
@endforeach
@endisset
