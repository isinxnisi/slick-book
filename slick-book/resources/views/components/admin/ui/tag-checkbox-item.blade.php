@props([
    'tagId',
    'tagName',
    'checked' => false,
    'purpose' => 'public',
])

<label class="inline-flex items-center cursor-pointer space-x-2">
    <input type="checkbox"
        class="tag-checkbox-item"
        data-tag-id="{{ $tagId }}"
        data-purpose="{{ $purpose }}"
        {{ $checked ? 'checked' : '' }}>
    <span>{{ $tagName }}</span>
</label>
