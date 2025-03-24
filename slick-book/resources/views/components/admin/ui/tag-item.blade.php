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
$bg = $active && $background ? $background : 'transparent';
$borderColor = $active && $border ? $border : '#fff';
$deleteClass = $isToggleable ? 'delete-m-tag-btn' : 'delete-tag-btn';
$deleteIcon = $isToggleable ? 'trash' : 'x';
@endphp

<li class="flex items-center space-x-1 tag-item {{ $active ? 'active' : '' }} ps-2 pe-2 py-1 rounded-full text-sm"
    data-id="{{ $tagId }}"
    style="background-color: {{ $bg }}; color: {{ $text }}; border: 1px solid {{ $borderColor }}">

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
    <button class="{{ $deleteClass }} hover:text-red-400"
        data-id="{{ $tagId }}"
        data-tag-group-id="{{ $tagGroupId }}">
        <i data-lucide="{{ $deleteIcon }}" class="w-4 h-4"></i>
    </button>
    @endif
</li>
