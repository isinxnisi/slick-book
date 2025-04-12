@section('title', 'サイト作成')
<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="max-w-4xl">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('sites.store') }}" enctype="multipart/form-data">

                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm">サイト名</label>
                        <x-admin.text-input name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm">スラッグ</label>
                        <x-admin.text-input name="slug" type="text" class="mt-1 block w-full" :value="old('slug')" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm">ドメイン</label>
                        <x-admin.text-input name="domain" type="text" class="mt-1 block w-full" :value="old('domain')" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm">説明</label>
                        <x-admin.textarea name="description" class="mt-1 block w-full">{{ old('description') }}</x-admin.textarea>
                    </div>

                    @php
                        $imageTypes = ['favicon' => 'ファビコン', 'logo' => 'ロゴ画像', 'nav-logo' => 'NAVロゴ画像', 'site-icon' => 'サイトアイコン画像', 'thumbnail' => 'サムネイル画像'];
                    @endphp
                    <div class="mb-6 border-t border-b border-gray-700 pt-6">
                        <h3 class="text-lg font-bold mb-4">画像のアップロード</h3>

                        @foreach ($imageTypes as $type => $label)
                            @php
                                $image = $site->images->firstWhere('type', $type);
                            @endphp

                            <div class="mb-4">
                                <label class="block text-sm font-semibold">{{ $label }}（{{ $type }}）</label>

                                @if ($image && $site->domain)
                                    <div class="mt-2 mb-2">
                                        <img src="{{ route('admin.media', [
                                                'site' => $site->id,
                                                'path' => "{$type}/" . basename($image->path)
                                            ]) }}"
                                            alt="{{ $image->alt ?? $label }}"
                                            class="h-16 rounded shadow border">
                                    </div>
                                @endif

                                <input type="file" name="images[{{ $type }}]" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-700 file:bg-gray-100 file:border file:rounded file:px-2 file:py-1">
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('sites.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">戻る</a>
                        <x-admin.primary-button>保存</x-admin.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
