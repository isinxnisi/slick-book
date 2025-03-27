@props([
    'tagId',
    'tagName',
    'active' => false,
    'editable' => false,
    'deletable' => false,
    'background' => null,
    'text' => null,
    'border' => null,

    // 選択 (トグル) 用
    'isToggleable' => false,
    'tagGroupId' => null,
    'siteId' => null,
    'purpose' => null,

    // 編集用
    'slug' => '',
    'description' => '',
    'groupName' => '',
])

@php
    // スタイル適用の統一
    $purposeStyles = config('tags.purpose_styles');
    $defaultStyle = $purposeStyles[$purpose] ?? ['bg' => '#888', 'text' => '#fff', 'border' => '#888'];

    $bg = $background ?? ($active ? $defaultStyle['bg'] : 'transparent');
    $borderColor = $border ?? ($active ? $defaultStyle['border'] : '#fff');
    $textColor = $text ?? ($active ? $defaultStyle['text'] : '#fff');
@endphp

<li class="tag-item flex items-center space-x-1 tag-item {{ $active ? 'active' : '' }} ps-2 pe-2 py-1 rounded-full text-sm"
    data-tag-id="{{ $tagId }}"
    data-group-id="{{ $tagGroupId }}"
    data-site-id="{{ $siteId }}"
    data-purpose="{{ $purpose }}"
    style="background-color: {{ $bg }}; color: {{ $textColor }}; border: 1px solid {{ $borderColor }}">

    {{-- タグ名の表示・選択 --}}
    @if($isToggleable)
        <button class="tag-toggle-btn tag-name"
                data-toggle="tag"
                data-tag-id="{{ $tagId }}"
                data-group-id="{{ $tagGroupId }}"
                data-site-id="{{ $siteId }}"
                data-purpose="{{ $purpose }}">
            {{ $tagName }}
        </button>
    @else
        <span class="tag-name">{{ $tagName }}</span>
    @endif

    {{-- 編集ボタン --}}
    @if($editable)
        <button class="edit-m-tag-btn hover:text-yellow-400"
                data-edit="tag"
                data-tag-id="{{ $tagId }}"
                data-name="{{ $tagName }}"
                data-slug="{{ $slug }}"
                data-description="{{ $description }}"
                data-group-id="{{ $tagGroupId }}"
                data-group-name="{{ $groupName }}">
            <i data-lucide="pencil" class="w-4 h-4"></i>
        </button>
    @endif

    {{-- 削除ボタン --}}
    @if($deletable)
        <button class="{{ $isToggleable ? 'delete-m-tag-btn' : 'delete-tag-btn' }} hover:text-red-400"
                data-delete="tag"
                data-tag-id="{{ $tagId }}"
                @if(!$isToggleable)
                    data-group-id="{{ $tagGroupId }}"
                @endif>
            <i data-lucide="{{ $isToggleable ? 'trash' : 'x' }}" class="w-4 h-4"></i>
        </button>
    @endif
</li>
