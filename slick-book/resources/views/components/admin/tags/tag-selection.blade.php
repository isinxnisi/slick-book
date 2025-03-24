@props([
    'groups' => collect(), // マスタタググループ（階層付き）
    'selectedTagIds' => [], // サイトでON状態のタグID
    'purpose' => null, // 現在表示中の用途
    'siteId' => null, // サイトID
])

@php
$purposeStyles = config('tags.purpose_styles');
$bgColor = $purposeStyles[$purpose]['bg'] ?? '#888';
$textColor = $purposeStyles[$purpose]['text'] ?? '#fff';
@endphp

<div class="mb-4">
    <div class="tag-selection-ui space-y-4">
        @foreach ($groups as $group)
            @if ($group->purpose !== $purpose) @continue @endif
            @if ($group->tags->count() == 0) @continue @endif
            <div class="flex items-center justify-between text-sm text-gray-400 dark:text-gray-500 mb-1">
                <div>{{ $group->breadcrumb }}</div>
                <button class="add-m-tag-inline-btn text-green-400 hover:text-green-500"
                        data-group-id="{{ $group->id }}"
                        data-group-name="{{ $group->breadcrumb }}">
                    <i data-lucide="square-plus" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="dark:bg-gray-900 flex flex-wrap gap-2 ml-1 mt-2 ps-2 py-2 rounded">
            @php
                $selectedIdsForGroup = $selectedTagIds[$group->id] ?? [];
                $bgColor = $purposeColors[$group->purpose] ?? '#888';
                $textColor = $purposeTextColors[$group->purpose] ?? '#333';
            @endphp
            @foreach ($group->tags as $tag)
                @php
                    $isActive = in_array($tag->id, $selectedIdsForGroup);
                @endphp
                <div class="tag-item ps-2 pe-2 rounded-full text-sm border-1"
                        style="
                            background-color: {{ $isActive ? $bgColor : 'transparent' }};
                            color: {{ $isActive ? $textColor : '#fff' }};
                            border-color: {{ $isActive ? $bgColor : '#fff' }};
                        ">
                    <button class="tag-toggle-btn me-1"
                        data-purpose="{{ $group->purpose }}"
                        data-tag-id="{{ $tag->id }}"
                        data-site-id="{{ $siteId }}"
                        data-tag-group-id="">
                        {{ $tag->name }}
                    </button>
                    <button class="edit-m-tag-btn hover:text-yellow-400 me-1"
                        data-tag-group-id="{{ $group->id }}"
                        data-id="{{ $tag->id }}"
                        data-name="{{ $tag->name }}"
                        data-slug="{{ $tag->slug }}"
                        data-description="{{ $tag->description }}">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                    </button>
                    <button class="delete-m-tag-btn hover:text-red-400" data-id="{{ $tag->id }}" data-tag-group-id="{{ $group->id }}">
                        <i data-lucide="trash" class="w-4 h-4"></i>
                    </button>
                </div>
            @endforeach
            </div>
        @endforeach

        <!-- フォーム：タグ追加 -->
        <div id="inline-tag-form-container" class="hidden mt-4 border-t border-b py-2">
            <form id="inline-tag-form" class="space-y-3">
                <input type="hidden" name="tag_id" id="inline-tag-id">
                <input type="hidden" name="tag_group_id" id="inline-tag-group-id">
                <div>
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-gray-300">追加先グループ</label>
                        <button id="inline-tag-cancel" type="button"
                                class="text-gray-300 hover:text-white">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div id="inline-tag-group-name" class="mt-1 text-sm font-bold text-white"></div>
                </div>
                <div>
                    <label for="inline-tag-name">タグ名</label>
                    <input type="text" name="name" id="inline-tag-name"
                           class="form-control w-full dark:bg-gray-900 border border-gray-700 text-white"
                           placeholder="タグ名" required>
                </div>
                <div>
                    <label for="inline-tag-slug">スラッグ</label>
                    <input type="text" name="slug" id="inline-tag-slug"
                           class="form-control w-full dark:bg-gray-900 border border-gray-700 text-white"
                           placeholder="スラッグ (空欄可)">
                </div>
                <div>
                    <label for="inline-tag-description">説明</label>
                    <textarea name="description" id="inline-tag-description" rows="2"
                              class="form-control w-full dark:bg-gray-900 border border-gray-700 text-white"
                              placeholder="説明 (任意)"></textarea>
                </div>
                <div class="flex justify-end gap-2 mb-1">
                    <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">
                        保存
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).on('click', '.tag-toggle-btn', function () {
        const $btn = $(this);
        const tagId = $btn.attr('data-tag-id');
        const tagGroupId = $btn.attr('data-tag-group-id');
        const isActive = $btn.hasClass('active');
        const tagName = $btn.text().trim(); // 表示名
        const color = $btn.css('border-color');

        $.ajax({
            url: '/tag-tag-groups/toggle',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tag_id: tagId,
                tag_group_id: tagGroupId,
            },
            success: function () {
                $btn.parent('.tag-item').toggleClass('active');
                const purpose = $btn.attr('data-purpose') || 'public';
                const style = window.purposeStyles?.[purpose] || { bg: '#888', text: '#fff' };

                if ($btn.parent('.tag-item').hasClass('active')) {
                    $btn.parent('.tag-item').css({ backgroundColor: style.bg, color: style.text, borderColor: style.bg });
                    if (currentSelectedGroupId) {
                        window.addTagToLeftUI(currentSelectedGroupId, tagId, tagName, style.bg);
                        lucide.createIcons();
                    }
                } else {
                    $btn.parent('.tag-item').css({ backgroundColor: 'transparent', color: '#fff', borderColor: '#fff' });
                    if (currentSelectedGroupId) {
                        window.removeTagFromLeftUI(currentSelectedGroupId, tagId);
                    }
                }
            }
        });
    });
</script>
@endpush
