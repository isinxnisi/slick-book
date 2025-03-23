@props([
    'groups' => collect(), // マスタタググループ（階層付き）
    'selectedTagIds' => [], // サイトでON状態のタグID
    'purpose' => null, // 現在表示中の用途
    'siteId' => null, // サイトID
])

@php
$purposeColors = config('tags.purpose_colors');
@endphp

<div class="mb-4">
    <h2 class="text-lg font-bold mb-2">タグ選択（{{ config('tags.purposes')[$purpose] ?? $purpose }}）</h2>

    <div class="tag-selection-ui space-y-4">
        @foreach ($groups as $group)
            @if ($group->purpose !== $purpose) @continue @endif
            @if ($group->tags->count() == 0) @continue @endif
            <div class="text-sm text-gray-400 dark:text-gray-500 mb-1">
                {{ $group->breadcrumb }}
            </div>
            <div class="dark:bg-gray-900 flex flex-wrap gap-2 ml-1 mt-2 ps-2 py-2 rounded">
            @php
                $selectedIdsForGroup = $selectedTagIds[$group->id] ?? [];
            @endphp
            @foreach ($group->tags as $tag)
                @php
                    $isActive = in_array($tag->id, $selectedIdsForGroup);
                    $color = $purposeColors[$group->purpose] ?? '#888';
                @endphp
                <div class="tag-item">
                    <button
                        class="tag-toggle-btn ps-2 pe-2 rounded-full text-sm border"
                        data-tag-id="{{ $tag->id }}"
                        data-site-id="{{ $siteId }}"
                        data-tag-group-id=""
                        style="
                            background-color: {{ $isActive ? $color : 'transparent' }};
                            color: {{ $isActive ? '#fff' : $color }};
                            border-color: {{ $color }};
                        "
                    >
                        {{ $tag->name }}
                    </button>
                </div>
            @endforeach
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    $(document).on('click', '.tag-toggle-btn', function () {
        const $btn = $(this);
        const tagId = $btn.data('tag-id');
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
                $btn.toggleClass('active');
                const bgColor = $btn.css('border-color');
                if ($btn.hasClass('active')) {
                    $btn.css({ backgroundColor: bgColor, color: '#fff' });
                    if (currentSelectedGroupId) {
                        window.addTagToLeftUI(currentSelectedGroupId, tagId, tagName, color);
                        lucide.createIcons();
                    }
                } else {
                    $btn.css({ backgroundColor: 'transparent', color: bgColor });
                    if (currentSelectedGroupId) {
                        window.removeTagFromLeftUI(currentSelectedGroupId, tagId);
                    }
                }
            },
            error: function () {
                alert('更新に失敗しました');
            }
        });
    });
</script>
@endpush
