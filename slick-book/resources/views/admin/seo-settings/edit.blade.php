@section('title', 'サイト編集')
<x-app-layout>
    <x-slot name="header">
        <!-- サイト切替タブ -->
        <x-admin.ui.site-tabs :sites="$sites" :active-id="$siteId" />
    </x-slot>

    <div class="max-w-5xl">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('site-seo-settings.update', [], false) }}?site={{ $siteId }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <x-admin.input label="Meta Title" name="meta_title" :value="$seo->meta_title" :placeholder="$site->name" />
                    <x-admin.textarea label="Meta Description" name="meta_description" :placeholder="$site->description">
                        {{ old('meta_description', $seo->meta_description ?? '') }}
                    </x-admin.textarea>
                    <x-admin.input label="Meta Keywords" name="meta_keywords" :value="$seo->meta_keywords" />
                    <x-admin.input label="Canonical Base URL" name="canonical_base" :value="$seo->canonical_base" :placeholder="$site->site_url"/>
                    @php
                        $image = $site->images->firstWhere('type', 'ogp');
                    @endphp

                    <div class="mb-4">
                        <label class="block text-sm font-semibold">{{ 'OG画像' }}（ogp）</label>

                        @if ($image && $site->domain)
                            <div class="mt-2 mb-2">
                                <img src="{{ route('admin.media', [
                                        'site' => $site->id,
                                        'path' => "ogp/" . basename($image->path)
                                    ]) }}"
                                    alt="{{ $image->alt ?? 'OG画像' }}"
                                    class="h-16 rounded shadow border">
                            </div>
                        @endif

                        <input type="file" name="images[ogp]" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-700 file:bg-gray-100 file:border file:rounded file:px-2 file:py-1">
                    </div>
                    <x-admin.select label="Twitterカード種別" name="twitter_card_type" :options="['summary' => 'summary', 'summary_large_image' => 'summary_large_image']" :value="$seo->twitter_card_type" />
                    <x-admin.textarea label="追加headタグ" name="custom_head_tags" rows="5">
                        {{ old('custom_head_tags', $seo->custom_head_tags ?? '') }}
                    </x-admin.textarea>

                    <x-admin.button type="submit" class="mt-4">保存</x-admin.button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
