@section('title', '記事の編集')

<x-app-layout>
    <x-slot name="header">
        <h3 class="text-gray-300 bold py-2 ps-4">{{ $sites->firstWhere('id', $siteId)->name ?? '' }}</h3>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-6">
        <!-- 左：投稿本文エリア -->
        <div id="ui-post-panel">
            <div class="panel text-gray-900 dark:text-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="panel-header pt-3 px-4 mb-0 border-b border-gray-700">

                <!-- タブUI -->
                <x-admin.ui.panel-tabs :panel="'#ui-post-panel'" :active="'markdown'" :tabs="[
                    'markdown' => 'テキスト',
                    'preview' => 'プレビュー',
                ]" />
            </div>
            <div class="panel-content p-4 pt-2">

                <!-- 基本設定タブ -->
                <div id="panel-tab-markdown" class="tab-content text-sm active">
                <form id="post-form" action="{{ route('posts.update', $post) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @foreach(config('tags.purposes') as $purposeKey => $label)
                        <input type="hidden"
                            name="selected_tag_ids[{{ $purposeKey }}]"
                            id="selected-tags-{{ $purposeKey }}"
                            form="post-form"
                            value='@json($selectedTagIdsByPurpose[$purposeKey])'>
                    @endforeach

                    <input type="hidden" name="id" value="{{ $post->id }}">

                    <div class="flex h-6 mt-3">
                        <div class="align-self-center text-left">
                            <label>タイトル:</label>
                        </div>
                    </div>
                    <x-admin.text-input name="title" type="text" class="mt-1 block w-full" :value="old('title', $post->title ?? '')" autofocus autocomplete="title" />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('title')" />

                    <div class="flex h-6 mt-3">
                        <div class="align-self-center text-left">
                            <label>公開タグ:</label>
                        </div>
                    </div>
                    <ul class="sortable-tags dark:bg-gray-900 flex flex-wrap gap-2 mt-1 p-2 rounded" id="tags-of-group-post">
                        @if ($post->tags->isNotEmpty())
                        @foreach ($post->tags as $tag)
                            @php
                                $purpose = $tag->purpose;
                                $styleSet = config('tags.purpose_styles')[$purpose] ?? [
                                    'bg' => '#4f46e5',
                                    'text' => '#fff',
                                    'border' => '#4f46e5',
                                ];
                            @endphp
                            <x-admin.ui.tag-item
                                :tag-id="$tag->id"
                                :tag-name="$tag->name"
                                :active="true"
                                :editable="false"
                                :deletable="true"
                                :background="$styleSet['bg']"
                                :text="$styleSet['text']"
                                :border="$styleSet['border']"
                                :is-toggleable="false"
                                :purpose="$purpose"
                            />
                        @endforeach
                        @endif
                    </ul>

                    <div class="flex h-10 mt-3">
                        <div class="align-self-center text-left">
                            <label>本文 (Markdown):</label>
                        </div>
                        <div class="flex-1 align-self-center text-right">
                            <button class="md-code-btn" type="button" onclick="window.wrapWithCodeTag('br')">br</button>
                            <button class="md-code-btn" type="button" onclick="window.wrapWithCodeTag('toc')">toc</button>
                            <button class="md-code-btn" type="button" onclick="window.wrapWithCodeTag('mark')">mark</button>
                            <button class="md-code-btn" type="button" onclick="window.wrapWithCodeTag('code', true)">code</button>
                        </div>
                    </div>
                    <x-admin.textarea id="post-markdown" name="body" class="block w-full" rows="15" autofocus autocomplete="body">
                        {{ old('body', $post->body ?? '') }}
                    </x-admin.textarea>
                    <x-admin.input-error class="mt-2" :messages="$errors->get('body')" />

                    <div class="flex justify-end mt-4">
                        <x-admin.primary-button>
                            {{ __('投稿する') }}
                        </x-admin.primary-button>
                    </div>
                </form>
                </div>

                <div id="panel-tab-preview" class="tab-content text-sm hidden">
                    <div id="html-preview" class="w-100 rounded-lg" style="max-height: 70vh; scroll-y:auto;">
                        <iframe id="html-preview-iframe" class="w-100 bg-white rounded-lg" src="" frameborder="0" style="min-height: 70vh;"></iframe>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="ui-right-panel ui-scrollable sm:rounded-lg">
            <!-- 右：設定パネル -->
            <div id="setting-panel" class="panel text-gray-900 dark:text-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="panel-header pt-3 px-4 mb-0 border-b border-gray-700">
                    <!-- タブUI -->
                    <x-admin.ui.panel-tabs :panel="'#setting-panel'" :active="'basic'" :tabs="[
                        'basic' => '基本設定',
                        'seo' => 'SEO設定',
                        'preview' => 'サイトプレビュー',
                    ]" />
                </div>
                <div class="panel-content p-4 pt-2">

                    <!-- 基本設定タブ -->
                    <div id="panel-tab-basic" class="tab-content text-sm">
                        <div class="mb-4">
                            <div class="mb-2 border-b border-gray-600">
                                <h4 class="text-gray-300 py-3">投稿設定</h4>
                            </div>
                            <!-- 公開状態フィールド -->
                            <div class="mb-2 ps-2">
                                <div class="flex items-center space-x-4">
                                    <label class="w-20 text-left text-xs">公開状態:</label>
                                    <select name="status" form="post-form" class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm p-2 ms-0 text-xs">
                                        <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>下書き</option>
                                        <option value="published" {{ old('status', $post->status ?? 'draft') === 'published' ? 'selected' : '' }}>公開</option>
                                    </select>
                                </div>
                                <x-admin.input-error class="mt-2" :messages="$errors->get('status')" />
                            </div>

                            <!-- 公開日時フィールド -->
                            <div class="mb-2 ps-2">
                                <div class="flex items-center space-x-4">
                                    <label class="w-20 text-left text-xs">公開日時:</label>
                                    <input type="datetime-local" name="published_at" form="post-form"
                                    class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm p-2 ms-0 text-xs"
                                    value="{{ old('published_at', $post->published_at ?? ($post->status == 'published'? new Datetime()->format('Y-m-d H:i'): '')) }}">
                                </div>
                                <x-admin.input-error class="mt-2" :messages="$errors->get('published_at')" />
                            </div>

                            <!-- カテゴリフィールド -->
                            <div class="mb-2 ps-2">
                                <div class="flex items-center space-x-4">
                                    <label class="w-20 text-left text-xs">カテゴリ:</label>
                                    <select name="category_id" form="post-form" class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm p-2 ms-0 text-xs">
                                        <option value="" @if(old('category_id', $category->id ?? '') == '') selected @endif>未選択</option>
                                        @foreach($categories as $key => $category)
                                            @if($category->children->isEmpty())
                                            <option value="{{ $category->id }}"
                                                @if(old('category_id', $post->category_id ?? '') == $category->id) selected @endif>
                                                {{ $category->breadcrumb }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <x-admin.input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>
                        </div>

                        <div class="mb-4" x-data="{ open: { selectTags: false } }">
                            <!-- タグ選択UI -->
                            <div class="border-b border-gray-600">
                                <h4 @click="open.selectTags = !open.selectTags" class="flex justify-between items-center text-gray-300 py-3" style="cursor:pointer;">
                                    公開タグ設定
                                    <svg x-bind:class="{ 'rotate-180': open.selectTags }" class="h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                                </h4>
                            </div>
                            <div x-show="open.selectTags" class="open-tab p-0">
                                <x-admin.posts.tag-selector
                                    :site-id="$siteId"
                                    :post="$post"
                                    :selected-tag-ids-by-purpose="$selectedTagIdsByPurpose"
                                    :purpose="request()->get('purpose', 'public')"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- SEO設定タブ（中身は後ほど） -->
                    <div id="panel-tab-seo" class="tab-content hidden">
                        <div class="p-4">
                            <p>※SEO設定項目は今後実装予定です。</p>
                        </div>
                    </div>

                    <!-- サイトプレビュータブ（中身は後ほど） -->
                    <div id="panel-tab-preview" class="tab-content hidden">
                        <div class="p-4">
                            <p>※プレビュー機能は今後実装予定です。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // グローバルに定義
        window.purposeStyles = @json(config('tags.purpose_styles'));
        // 右UIパネル：タグの選択／選択解除
        $(document).on('click', '.tag-toggle-btn', function () {
            const postId = $('input[type="hidden"][name="id"]').val();
            const $btn = $(this);
            const tagId = $btn.attr('data-tag-id');

            const isActive = $btn.parent('.tag-item').hasClass('active');
            const tagName = $btn.text().trim();
            const purpose = $btn.attr('data-purpose') || 'public';
            const style = window.purposeStyles?.[purpose] || { bg: '#888', text: '#fff' };

            $.ajax({
                url: '/post-tags/toggle',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    post_id: postId,
                    tag_id: tagId,
                },
                success: function (data) {
                    const $item = $btn.parent('.tag-item');
                    if (data.status == 2) {
                        $item.css({ backgroundColor: style.bg, color: style.text, borderColor: style.bg });
                        window.addTagToLeftUI(
                            'post',
                            tagId,
                            tagName,
                            style.bg,
                            style.text
                        );
                    } else {
                        $item.css({ backgroundColor: 'transparent', color: '#fff', borderColor: '#fff' });
                        window.removeTagFromLeftUI('post', tagId);
                    }

                    window.refreshLucideAndBindEvents();
                }
            });
        });
        // 右UIパネル：タグの選択／選択解除
        $(document).on('click', '#ui-post-panel .tab-button', function () {
            const $btn = $(this);
            if ($btn.hasClass('active') && $btn.attr('data-tab') == 'preview') {

                document.getElementById('html-preview-iframe').setAttribute('srcdoc', '<p>読み込んでいます</p>');
                $('#panel-tab-preview').addClass('hidden');

                const postId = $('#panel-tab-markdown').find('input[type="hidden"][name="id"]').val();
                const selectedTagIds = $('#panel-tab-markdown').find('input[type="hidden"][name="selected_tag_ids[public]"]').val();
                const title = $('#panel-tab-markdown').find('input[name="title"]').val();
                $.ajax({
                    url: '/posts/preview',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        post_id: postId,
                        selected_tags: selectedTagIds,
                        title: title,
                        body: $('#panel-tab-markdown').find('textarea').val(),
                    },
                    success: function (html) {
                        // $('#html-preview').html(html);
                        $('#panel-tab-preview').removeClass('hidden');
                        document.getElementById('html-preview-iframe').setAttribute('srcdoc', html);
                    }
                });
            }
        });
        // サイト：タグ削除
        $(document).on('click', '.tag-item .delete-tag-btn', function (e) {
            e.preventDefault();
            const postId = $('input[type="hidden"][name="id"]').val();
            const tagId = $(this).attr('data-tag-id');

            $.ajax({
                url: '/post-tags/unlink',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    post_id: postId,
                    tag_id: tagId
                },
                success: () => {
                    cancelSelectedTagId('public', tagId);
                    // 左UIからタグを削除
                    window.removeTagFromLeftUI('post', tagId);
                    // 右UI選択状態の更新
                    window.fetchTagSelector();
                },
                error: () => alert('削除に失敗しました')
            });
        });

        // マスタ：タグ追加
        $(document).on('click', '.add-m-tag-inline-btn', function () {
            const groupId = $(this).data('group-id');
            const groupName = $(this).data('group-name');

            $('#inline-tag-id').val('');
            $('#inline-tag-group-id').val(groupId);
            $('#inline-tag-group-name').text(groupName);
            $('#inline-tag-name').val('');
            $('#inline-tag-slug').val('');
            $('#inline-tag-description').val('');

            $('#inline-tag-form-container').hide().removeClass('hidden').slideDown(200);
        });

        // マスタ：タグ編集ボタン
        $(document).on('click', '.edit-m-tag-btn', function () {
            const tagId = $(this).attr('data-tag-id');
            const groupId = $(this).data('group-id');
            const groupName = $(this).data('group-name');
            const tagName = $(this).attr('data-name');
            const tagSlug = $(this).attr('data-slug');
            const tagDescription = $(this).attr('data-description');
            // const groupId = $(this).closest('.tag-selection-ui').find('input[name=tag_group_id]').val();

            $('#inline-tag-id').val(tagId);
            $('#inline-tag-group-id').val(groupId);
            $('#inline-tag-group-name').text(groupName);
            $('#inline-tag-name').val(tagName);
            $('#inline-tag-slug').val(tagSlug);
            $('#inline-tag-description').val(tagDescription);

            $('#inline-tag-form-container').hide().removeClass('hidden').slideDown(200);
        });

        // マスタ：タグ削除ボタン
        $(document).on('click', '.delete-m-tag-btn', function () {
            const tagId = $(this).attr('data-tag-id');

            if (!confirm('このタグを削除してもよろしいですか？')) return;
            console.log('TEST');
            $.ajax({
                url: `/tags/${tagId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    // 再描画してUI更新
                    window.fetchTagSelector();
                    // 左UIからタグを削除
                    window.removeTagFromLeftUI('post', tagId);
                },
                error: function () {
                    alert('削除に失敗しました');
                }
            });
        });

        // マスタ：タグ追加：キャンセル
        $('#inline-tag-cancel').on('click', function () {
            $('#inline-tag-form-container').slideUp(200);
        });

        // マスタ：タグ保存
        $(document).on('submit', '#inline-tag-form', function (e) {
            e.preventDefault();

            // 送信データ
            const tagId = $('#inline-tag-id').val();
            const data = {
                tag_id: tagId,
                tag_group_id: $('#inline-tag-group-id').val(),
                name: $('#inline-tag-name').val(),
                slug: $('#inline-tag-slug').val(),
                purpose: 'public',
                description: $('#inline-tag-description').val(),
                _token: '{{ csrf_token() }}'
            };

            if (!tagId) {
                // 新規
                $.post('/tags', data, function (res) {
                    $('#inline-tag-form-container').slideUp(0);

                    // 右UIのタグ一覧を再取得
                    window.fetchTagSelector();
                }).fail(function () {
                    alert('保存に失敗しました');
                });
            } else {
                // 編集
                $.ajax({
                    url: `/tags/${tagId}`,
                    method: 'PATCH',
                    data: data,
                    success: (tagData) => {
                        $('#inline-tag-form-container').slideUp(0);

                        // 右UIのタグ一覧を再取得
                        window.fetchTagSelector();
                    }
                });

            }
        });

        $("iframe").on("load", function () {
            $("iframe")
                .contents()
                .find("head")
                .append(
                '<link rel="stylesheet" href="{{ asset('css/theme/post-theme.css') }}" type="text/css">'
                );
        });

        function cancelSelectedTagId(purpose, tagId) {
            let selectedTagIds = $('[name="selected_tag_ids[' + purpose + ']"]').val();
            selectedTagIds = JSON.parse(selectedTagIds);
            let cancelIndex = selectedTagIds.indexOf(tagId);
            selectedTagIds.pop(cancelIndex);
            selectedTagIds = JSON.stringify(selectedTagIds);
            $('[name="selected_tag_ids[' + purpose + ']"]').val(selectedTagIds);
        }
    </script>
    @endpush
</x-app-layout>
