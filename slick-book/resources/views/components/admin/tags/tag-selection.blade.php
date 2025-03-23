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
                    $bgColor = $purposeColors[$group->purpose] ?? '#888';
                    $textColor = $purposeTextColors[$group->purpose] ?? '#333';
                @endphp
                <div class="tag-item">
                    <button
                        class="tag-toggle-btn ps-2 pe-2 rounded-full text-sm border-1"
                        data-tag-id="{{ $tag->id }}"
                        data-site-id="{{ $siteId }}"
                        data-tag-group-id=""
                        style="
                            background-color: {{ $isActive ? $bgColor : 'transparent' }};
                            color: {{ $isActive ? $textColor : '#fff' }};
                            border-color: {{ $isActive ? $bgColor : '#fff' }};
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
                $btn.toggleClass('active');
                const purpose = $btn.attr('data-purpose') || 'public';
                const style = window.purposeStyles?.[purpose] || { bg: '#888', text: '#fff' };

                if ($btn.hasClass('active')) {
                    $btn.css({ backgroundColor: style.bg, color: style.text, borderColor: style.bg });
                    if (currentSelectedGroupId) {
                        window.addTagToLeftUI(currentSelectedGroupId, tagId, tagName, style.bg);
                        lucide.createIcons();
                    }
                } else {
                    $btn.css({ backgroundColor: 'transparent', color: '#fff', borderColor: '#fff' });
                    if (currentSelectedGroupId) {
                        window.removeTagFromLeftUI(currentSelectedGroupId, tagId);
                    }
                }
            }
        });
    });
</script>
@endpush
