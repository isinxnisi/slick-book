@props([
    'groups' => collect(),
    'post',
    'siteId',
    'selectedTagIdsByPurpose' => [],
    'purpose' => 'public',
])

@php
    $purposes = config('tags.purposes');
    $purposeStyles = config('tags.purpose_styles');
@endphp

<div class="py-2">
    @foreach ($purposes as $key => $label)
        <input type="hidden" name="selected_tag_ids[{{ $key }}]" id="selected-tags-{{ $key }}" 
            value='@json($selectedTagIdsByPurpose[$key] ?? [])'>
    @endforeach

    <div id="tag-selector-panel" class="space-y-2">
        <div id="tag-selector-content">
            @if ($groups->isNotEmpty())
                @include('components.admin.tags.post-tag-selection', [
                    'groups' => $groups,
                    'selectedTagIds' => $selectedTagIdsByPurpose[$purpose],
                    'purpose' => $purpose,
                    'siteId' => $siteId
                ])
            @else
                <p class="text-gray-400">タグが見つかりません。</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.fetchTagSelector = function () {
        const siteId = @json($siteId);
        const postId = @json($post->id);
        const purpose = @json($purpose);

        $.get(`/posts/tags`, { site: siteId, post: postId, purpose }, function (html) {
            $('#tag-selector-content').html(html);

            // ★ ここでイベントバインドを行う
            bindTagToggleEvent();

            if (typeof window.refreshLucideAndBindEvents === 'function') {
                window.refreshLucideAndBindEvents();
            }
        });
    };

    // タグ選択イベントのバインド処理
    function bindTagToggleEvent() {
        $('.tag-toggle-btn').off('click').on('click', function () {
            const $btn = $(this);
            const tagId = $btn.data('tag-id');
            const tagGroupId = $btn.data('tag-group-id');
            const tagName = $btn.text().trim();
            const purpose = $btn.data('purpose') || 'public';
            const style = window.purposeStyles?.[purpose] || { bg: '#888', text: '#fff' };

            const $item = $btn.parent('.tag-item');
            const isActive = $item.hasClass('active');

            // 選択状態トグル
            $item.toggleClass('active');
            if ($item.hasClass('active')) {
                $item.css({ backgroundColor: style.bg, color: style.text, borderColor: style.bg });
                window.addTagToLeftUI(window.currentSelectedGroupId, tagId, tagName, style.bg, style.text);
            } else {
                $item.css({ backgroundColor: 'transparent', color: '#fff', borderColor: '#fff' });
                window.removeTagFromLeftUI(window.currentSelectedGroupId, tagId);
            }

            updateSelectedTagsInput(purpose);

            window.refreshLucideAndBindEvents();
            window.fetchTagSelector();
        });
    }

    // フォーム送信用タグIDをhiddenに設定する関数
    function updateSelectedTagsInput(purpose) {
        const selectedTagIds = [];
        $('.tag-item.active .tag-toggle-btn').each(function () {
            selectedTagIds.push($(this).data('tag-id'));
        });

        $(`#selected-tags-${purpose}`).val(JSON.stringify(selectedTagIds));
    }

    // 初回読み込み時にもバインド
    $(document).ready(function () {
        fetchTagSelector();
    });

    $(function () {
        fetchTagSelector('{{ $purpose }}');

        $('.purpose-tab').on('click', function (e) {
            e.preventDefault();
            const purpose = $(this).data('purpose');
            fetchTagSelector(purpose);
            $('.purpose-tab').removeClass('text-white bg-gray-800 border-indigo-500').addClass('text-gray-400');
            $(this).removeClass('text-gray-400').addClass('text-white bg-gray-800 border-indigo-500');
        });
    });

    const updateSelectedTags = (purpose) => {
        let selectedTags = [];
        $(`.tag-checkbox[data-purpose="${purpose}"]:checked`).each((_, el) => {
            selectedTags.push($(el).data('tag-id'));
        });
        $(`#selected-tags-${purpose}`).val(JSON.stringify(selectedTags));
    };

    $(document).on('change', '.tag-checkbox', function () {
        const purpose = $(this).data('purpose');
        updateSelectedTags(purpose);
    });

    $(document).on('change', '.tag-checkbox-item', function() {
        const purpose = $(this).data('purpose');
        const selectedIds = $(`.tag-checkbox-item[data-purpose="${purpose}"]:checked`)
            .map(function() { return $(this).data('tag-id'); })
            .get();

        $(`#selected-tags-${purpose}`).val(JSON.stringify(selectedIds));

        // 左UIへのタグ反映が必要ならここで実施（後で調整）
    });
</script>
@endpush