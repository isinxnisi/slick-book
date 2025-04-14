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
    <div id="tag-selector-panel" class="space-y-2">
        <div id="tag-selector-content">
            @if ($post->images->isNotEmpty())
            <ul class="flex">
                @foreach ($post->images as $postImages)
                <li class="m-2 append-image">
                    <div class="set-image p-0 draggable">
                        <button class="delete-imgage-btn hover:text-red-400"
                            data-type="post_image"
                            data-image-id="{{ $postImages->id }}">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                        <img src="{{ route('admin.media', [
                                'site' => $post->site_id,
                                'path' => "{$postImages->type}/" . basename($postImages->path)
                            ]) }}"
                            alt="{{ $postImages->alt ?? '' }}"
                            class="h-16 w-100 rounded shadow border object-fit-cover"
                            onclick="window.appendImgTag('{{ $postImages->id }}')">
                        <span class="text-xs">{{ basename($postImages->path) }}</span>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
                <p class="text-gray-400">画像が見つかりません。</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // window.fetchTagSelector = function () {
    //     const siteId = @json($siteId);
    //     const postId = @json($post->id);
    //     const purpose = @json($purpose);

    //     $.get(`/posts/tags`, { site: siteId, post: postId, purpose }, function (html) {
    //         $('#tag-selector-content').html(html);

    //         // ★ ここでイベントバインドを行う
    //         bindTagToggleEvent();

    //         if (typeof window.refreshLucideAndBindEvents === 'function') {
    //             window.refreshLucideAndBindEvents();
    //         }
    //     });
    // };

    // // タグ選択イベントのバインド処理
    // function bindTagToggleEvent() {
    //     $('.tag-toggle-btn').off('click').on('click', function () {
    //         const $btn = $(this);
    //         const tagId = $btn.data('tag-id');
    //         const tagGroupId = $btn.data('tag-group-id');
    //         const tagName = $btn.text().trim();
    //         const purpose = $btn.data('purpose') || 'public';
    //         const style = window.purposeStyles?.[purpose] || { bg: '#888', text: '#fff' };

    //         const $item = $btn.parent('.tag-item');
    //         const isActive = $item.hasClass('active');

    //         // 選択状態トグル
    //         $item.toggleClass('active');
    //         if ($item.hasClass('active')) {
    //             $item.css({ backgroundColor: style.bg, color: style.text, borderColor: style.bg });
    //             window.addTagToLeftUI(window.currentSelectedGroupId, tagId, tagName, style.bg, style.text);
    //         } else {
    //             $item.css({ backgroundColor: 'transparent', color: '#fff', borderColor: '#fff' });
    //             window.removeTagFromLeftUI(window.currentSelectedGroupId, tagId);
    //         }

    //         updateSelectedTagsInput(purpose);

    //         window.refreshLucideAndBindEvents();
    //         window.fetchTagSelector();
    //     });
    // }

    // // フォーム送信用タグIDをhiddenに設定する関数
    // function updateSelectedTagsInput(purpose) {
    //     const selectedTagIds = [];
    //     $('.tag-item.active .tag-toggle-btn').each(function () {
    //         selectedTagIds.push($(this).data('tag-id'));
    //     });

    //     $(`#selected-tags-${purpose}`).val(JSON.stringify(selectedTagIds));
    // }

    // // 初回読み込み時にもバインド
    // $(document).ready(function () {
    //     fetchTagSelector();
    // });

    // $(function () {
    //     fetchTagSelector('{{ $purpose }}');

    //     $('.purpose-tab').on('click', function (e) {
    //         e.preventDefault();
    //         const purpose = $(this).data('purpose');
    //         fetchTagSelector(purpose);
    //         $('.purpose-tab').removeClass('text-white bg-gray-800 border-indigo-500').addClass('text-gray-400');
    //         $(this).removeClass('text-gray-400').addClass('text-white bg-gray-800 border-indigo-500');
    //     });
    // });

    // const updateSelectedTags = (purpose) => {
    //     let selectedTags = [];
    //     $(`.tag-checkbox[data-purpose="${purpose}"]:checked`).each((_, el) => {
    //         selectedTags.push($(el).data('tag-id'));
    //     });
    //     $(`#selected-tags-${purpose}`).val(JSON.stringify(selectedTags));
    // };

    // $(document).on('change', '.tag-checkbox', function () {
    //     const purpose = $(this).data('purpose');
    //     updateSelectedTags(purpose);
    // });

    // $(document).on('change', '.tag-checkbox-item', function() {
    //     const purpose = $(this).data('purpose');
    //     const selectedIds = $(`.tag-checkbox-item[data-purpose="${purpose}"]:checked`)
    //         .map(function() { return $(this).data('tag-id'); })
    //         .get();

    //     $(`#selected-tags-${purpose}`).val(JSON.stringify(selectedIds));

    //     // 左UIへのタグ反映が必要ならここで実施（後で調整）
    // });
</script>
@endpush
