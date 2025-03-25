@section('title', '新規投稿')

<x-app-layout>
    <x-slot name="header">
        <!-- サイト切替タブ -->
        <x-admin.ui.site-tabs :sites="$sites" :active-id="$siteId" />
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-6">
        <!-- 左：投稿本文エリア -->
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form action="{{ route('posts.store') }}" method="POST" id="post-form">
                    @csrf

                    <label>タイトル:</label>
                    <x-admin.text-input name="title" type="text" class="mt-1 block w-full" :value="old('title', $post->title ?? '')" required autofocus autocomplete="title" />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('title')" />

                    <label>本文 (Markdown):</label>
                    <x-admin.textarea name="body" class="mt-1 block w-full" required autofocus autocomplete="body">
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

        <!-- 右：設定パネル -->
        <div class="ui-right-panel dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
            <!-- タブUI -->
            <x-admin.ui.panel-tabs :active="'basic'" :tabs="[
                'basic' => '基本設定',
                'seo' => 'SEO設定',
                'preview' => 'サイトプレビュー',
            ]" />

            <!-- 基本設定タブ -->
            <div id="panel-tab-basic" class="tab-content mt-4">
                <!-- 公開状態 -->
                <label>公開状態:</label>
                <select name="status" form="post-form"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm p-2 w-full">
                    <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>下書き</option>
                    <option value="published" {{ old('status', $post->status ?? 'draft') === 'published' ? 'selected' : '' }}>公開</option>
                </select>
                <x-admin.input-error class="mt-2" :messages="$errors->get('status')" />

                <!-- カテゴリ（仮：select） -->
                <label class="mt-4 block">カテゴリ:</label>
                <select name="category_id" form="post-form" class="form-control">
                    <option value="">未選択</option>
                    <!-- カテゴリ一覧があればここにループ挿入 -->
                </select>

                <!-- タグ選択UI -->
                <x-admin.tags.tag-selector 
                    :site-id="$siteId"
                    :selected-tag-ids-by-purpose="$selectedTagIdsByPurpose"
                    :purpose="request()->get('purpose', 'public')" 
                />
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
</x-app-layout>
