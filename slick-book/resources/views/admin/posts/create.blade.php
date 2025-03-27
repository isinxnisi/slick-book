@section('title', '新規投稿')

<x-app-layout>
    <x-slot name="header">
        <!-- サイト切替タブ -->
        <x-admin.ui.site-tabs :sites="$sites" :active-id="$siteId" />
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-6">
        <!-- 左：投稿本文エリア -->
        <div class="overflow-hidden">
            <div class="p-6 text-gray-900 dark:text-gray-100 dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <form action="{{ route('posts.store') }}" method="POST" id="post-form">
                    @csrf
                    @foreach(config('tags.purposes') as $purposeKey => $label)
                        <input type="hidden"
                            name="selected_tag_ids[{{ $purposeKey }}]"
                            id="selected-tags-{{ $purposeKey }}"
                            form="post-form"
                            value='@json($selectedTagIdsByPurpose[$purposeKey] ?? [])'>
                    @endforeach
                    <label>タイトル:</label>
                    <x-admin.text-input name="title" type="text" class="mt-1 block w-full" :value="old('title', $post->title ?? '')" required autofocus autocomplete="title" />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('title')" />

                    <ul class="sortable-tags dark:bg-gray-900 flex flex-wrap gap-2 ml-2 mt-2 ps-2 rounded" id="tags-of-post">
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

                    <label>本文 (Markdown):</label>
                    <x-admin.textarea name="body" class="mt-1 block w-full" rows="15" required autofocus autocomplete="body">
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
        </div>

        <div class="ui-right-panel ui-scrollable sm:rounded-lg">
            <!-- 右：設定パネル -->
            <div class="panel text-gray-900 dark:text-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="panel-header pt-3 px-4 mb-0 border-b border-gray-700">
                    <!-- タブUI -->
                    <x-admin.ui.panel-tabs :active="'basic'" :tabs="[
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

                            <!-- カテゴリフィールド -->
                            <div class="mb-2 ps-2">
                                <div class="flex items-center space-x-4">
                                    <label class="w-20 text-left text-xs">カテゴリ:</label>
                                    <select name="category_id" form="post-form" class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm p-2 ms-0 text-xs">
                                        <option value="" @if(old('category_id', $category->id ?? '') == '') selected @endif>未選択</option>
                                        @foreach($categories as $key => $category)
                                            @if($category->children->isEmpty())
                                            <option value="{{ $category->id }}"
                                                @if(old('category_id', '') == $category->id) selected @endif>
                                                {{ $category->breadcrumb }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <x-admin.input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <!-- タグ選択UI -->
                            <div class="mb-2 border-b border-gray-600">
                                <h4 class="text-gray-300 py-3">公開タグ設定</h4>
                            </div>
                            <x-admin.posts.tag-selector 
                                :site-id="$siteId"
                                :selected-tag-ids-by-purpose="$selectedTagIdsByPurpose"
                                :purpose="request()->get('purpose', 'public')" 
                            />
                        </div>
                    </div>

                    <!-- SEO設定タブ（中身は後ほど） -->
                    <div id="panel-tab-seo" class="tab-content hidden">
                        <p>SEO設定項目は今後実装予定です。</p>
                    </div>

                    <!-- サイトプレビュータブ（中身は後ほど） -->
                    <div id="panel-tab-preview" class="tab-content hidden">
                        <p>プレビュー機能は今後実装予定です。</p>
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
            const $btn = $(this);
            const tagId = $btn.attr('data-tag-id');
            const tagGroupId = $btn.attr('data-group-id');
            const isActive = $btn.parent('.tag-item').hasClass('active');
            const tagName = $btn.text().trim();
            const purpose = $btn.attr('data-purpose') || 'public';
            const style = window.purposeStyles?.[purpose] || { bg: '#888', text: '#fff' };

            $.ajax({
                url: '/tag-tag-groups/toggle',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tag_id: tagId,
                    tag_group_id: tagGroupId,
                },
                success: function () {
                    const $item = $btn.parent('.tag-item');
                    $item.toggleClass('active');

                    if ($item.hasClass('active')) {
                        $item.css({ backgroundColor: style.bg, color: style.text, borderColor: style.bg });
                        if (window.currentSelectedGroupId) {
                            window.addTagToLeftUI(
                                window.currentSelectedGroupId,
                                tagId,
                                tagName,
                                style.bg,
                                style.text
                            );
                        }
                    } else {
                        $item.css({ backgroundColor: 'transparent', color: '#fff', borderColor: '#fff' });
                        if (window.currentSelectedGroupId) {
                            window.removeTagFromLeftUI(window.currentSelectedGroupId, tagId);
                        }
                    }

                    window.refreshLucideAndBindEvents();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
