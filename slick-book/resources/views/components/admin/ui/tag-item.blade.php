@props([
    'tagId',
    'tagName',
    'active' => false,
    'editable' => false,
    'deletable' => false,
    'background' => null,
    'text' => '#fff',
    'border' => null,

    // 右UI操作用
    'isToggleable' => false,
    'tagGroupId' => null,
    'siteId' => null,
    'purpose' => null,
    'slug' => '',
    'description' => '',
    'groupName' => '',
])

@php
$purposeStyles = config('tags.purpose_styles');
$style = $purposeStyles[$purpose] ?? ['bg' => '#888', 'text' => '#fff', 'border' => '#888'];
$bg = $active ? $style['bg'] : 'transparent';
$borderColor = $active ? $style['border'] : '#fff';
$textColor = $active ? $style['text'] : '#fff';
@endphp

<li class="flex items-center space-x-1 tag-item {{ $active ? 'active' : '' }} ps-2 pe-2 py-1 rounded-full text-sm"
    data-id="{{ $tagId }}"
    style="background-color: {{ $bg }}; color: {{ $textColor }}; border: 1px solid {{ $borderColor }}">

    @if($isToggleable)
        <button class="tag-toggle-btn me-1"
            data-tag-id="{{ $tagId }}"
            data-tag-group-id="{{ $tagGroupId }}"
            data-site-id="{{ $siteId }}"
            data-purpose="{{ $purpose }}">
            {{ $tagName }}
        </button>
    @else
        <span>{{ $tagName }}</span>
    @endif

    @if($editable)
    <button class="edit-m-tag-btn hover:text-yellow-400"
        data-id="{{ $tagId }}"
        data-name="{{ $tagName }}"
        data-slug="{{ $slug }}"
        data-description="{{ $description }}"
        data-group-id="{{ $tagGroupId }}"
        data-group-name="{{ $groupName }}">
        <i data-lucide="pencil" class="w-4 h-4"></i>
    </button>
    @endif

    @if($deletable)
    <button class="{{ $isToggleable ? 'delete-m-tag-btn' : 'delete-tag-btn' }} hover:text-red-400"
        data-id="{{ $tagId }}"
        @if(!$isToggleable)
            data-tag-group-id="{{ $tagGroupId }}"
        @endif
    >
        <i data-lucide="{{ $isToggleable ? 'trash' : 'x' }}" class="w-4 h-4"></i>
    </button>
    @endif
</li>
