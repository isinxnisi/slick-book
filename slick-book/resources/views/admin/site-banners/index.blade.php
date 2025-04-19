@section('title', 'バナー管理')

@php
    $deviceTypes = config('banner.device_types');
    $sections = config('banner.sections');
    $slots = config('banner.slots');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h3 class="text-gray-300 bold py-2 ps-4">{{ $sites->firstWhere('id', $siteId)->name ?? '' }}</h3>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 text-sm text-gray-900 dark:text-gray-100" style="max-width: 1040px">
        <div class="panel text-gray-900 dark:text-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <!-- デバイスタイプ切替 -->
            <div class="mb-2 pt-2 flex space-x-2 border-b border-gray-600">
                @foreach ($deviceTypes as $key => $label)
                    <a href="{{ route('site-banners.index', ['site' => $siteId, 'device' => $key]) }}"
                        class="relative px-4 py-2 text-sm font-medium transition-all
                        {{ $device === $key
                            ? 'text-white border-b-2 border-indigo-500'
                            : 'text-gray-400 hover:text-white hover:border-b-2 hover:border-gray-500' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- 一括開閉ボタン -->
            <div class="mb-2 text-right space-x-2">
                <button class="text-xs text-blue-400 underline me-2" id="expand-all">全部開く</button>
                <button class="text-xs text-blue-400 underline me-2" id="collapse-all">全部閉じる</button>
            </div>

            <!-- 表示枠ごとのバナー一覧＋編集＆追加フォーム -->
            @foreach ($sections as $sectionKey => $sectionLabel)
                @if (!isset($slots[$sectionKey]))
                    @continue
                @endif

                <div class="border-t border-gray-600 rounded mb-6 p-2">
                    <h4 class="text-gray-200 text-sm font-semibold mb-2">表示枠: {{ $sectionLabel }}</h4>
                    <ul class="space-y-2">
                        @foreach ($slots[$sectionKey] as $slotNo => $slotLabel)
                            @php
                                $banners = $bannersBySlot[$sectionKey][$slotNo] ?? collect();
                                $now = now();
                                $activeBanner = $banners->first(
                                    fn($b) => $b->enabled &&
                                        (!$b->start_at || $b->start_at <= $now) &&
                                        (!$b->end_at || $b->end_at >= $now),
                                );
                            @endphp
                            <li class="bg-gray-800 p-2 rounded border-b border-gray-700">
                                <div class="flex justify-between items-center cursor-pointer slot-header"
                                    data-toggle="#slot-body-{{ $sectionKey }}-{{ $slotNo }}">
                                    <div class="text-xs text-white">
                                        スロット{{ $slotNo }}: <strong>{{ $slotLabel }}</strong>
                                        @if ($activeBanner)
                                            <span
                                                class="ml-2 text-green-300">{{ $activeBanner->title }}（{{ $activeBanner->start_at?->format('m/d') }}〜{{ $activeBanner->end_at?->format('m/d') }}）</span>
                                        @else
                                            <span class="ml-2 text-gray-400">（未設定）</span>
                                        @endif
                                    </div>
                                    <button class="text-xs text-blue-400 underline add-banner-btn"
                                        data-target="#form-{{ $sectionKey }}-{{ $slotNo }}"
                                        data-body="#slot-body-{{ $sectionKey }}-{{ $slotNo }}">+追加</button>
                                </div>

                                <div id="slot-body-{{ $sectionKey }}-{{ $slotNo }}" class="mt-2 hidden">
                                    <ul class="space-y-2">
                                        @foreach ($banners->sortBy(function ($b) use ($now) {
        return $b->end_at && $b->end_at < $now ? 1 : 0;
    }) as $banner)
                                            <li
                                                class="text-xs p-2 rounded {{ $banner->end_at && $banner->end_at < $now ? 'bg-gray-700 text-gray-500' : 'bg-gray-800 text-white' }}">
                                                <div class="flex justify-between gap-2">
                                                    <form method="POST"
                                                        action="{{ route('site-banners.update', $banner) }}"
                                                        class="flex-1">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="site_id"
                                                            value="{{ $siteId }}">
                                                        <input type="hidden" name="device_type"
                                                            value="{{ $device }}">
                                                        <input type="hidden" name="section"
                                                            value="{{ $sectionKey }}">
                                                        <input type="hidden" name="slot_no"
                                                            value="{{ $slotNo }}">

                                                        <div class="flex justify-between items-center mb-1 gap-2">
                                                            <label class="text-xs text-right whitespace-nowrap"
                                                                style="width: 70px;">タイトル:</label>
                                                            <input type="text" name="title"
                                                                value="{{ $banner->title }}" required
                                                                class="flex-1 text-xs rounded px-1 py-0.5 bg-gray-600 border-gray-500">
                                                            <button type="submit"
                                                                class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs whitespace-nowrap">更新</button>
                                                        </div>
                                                        <div class="flex justify-between items-center mb-1 gap-2">
                                                            <label class="text-xs text-right"
                                                                style="width: 70px;">表示HTML:</label>
                                                            <textarea name="html" rows="2"
                                                                class="flex-1 resize-x-none w-full text-xs rounded px-1 py-0.5 bg-gray-600 border-gray-500">{{ $banner->html }}</textarea>
                                                        </div>
                                                        <div class="flex items-end space-x-2 text-xs mb-1">
                                                            <div class="flex justify-between items-center gap-2 w-full">
                                                                <label class="text-xs text-right"
                                                                    style="width: 70px;">開始:</label>
                                                                <input type="datetime-local" name="start_at"
                                                                    value="{{ optional($banner->start_at)->format('Y-m-d\TH:i') }}"
                                                                    class="flex-1 text-xs rounded bg-gray-600 border-gray-500 px-1 py-0.5"
                                                                    style="width: calc(100% - 70px);">
                                                            </div>
                                                            <div class="flex justify-between items-center gap-2 w-full">
                                                                <label class="text-xs text-right"
                                                                    style="width: 70px;">終了:</label>
                                                                <input type="datetime-local" name="end_at"
                                                                    value="{{ optional($banner->end_at)->format('Y-m-d\TH:i') }}"
                                                                    class="flex-1 text-xs rounded bg-gray-600 border-gray-500 px-1 py-0.5"
                                                                    style="width: calc(100% - 70px);">
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('site-banners.destroy', $banner) }}"
                                                        onsubmit="return confirm('本当に削除しますか？')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-400 text-xs hover:underline">×</button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach

                                        <!-- 追加フォーム -->
                                        <li id="form-{{ $sectionKey }}-{{ $slotNo }}" class="hidden">
                                            <form method="POST" action="{{ route('site-banners.store') }}">
                                                @csrf
                                                <input type="hidden" name="site_id" value="{{ $siteId }}">
                                                <input type="hidden" name="device_type" value="{{ $device }}">
                                                <input type="hidden" name="section" value="{{ $sectionKey }}">
                                                <input type="hidden" name="slot_no" value="{{ $slotNo }}">

                                                <div class="flex justify-between items-center mb-1 gap-2">
                                                    <label class="text-xs text-right whitespace-nowrap"
                                                        style="width: 70px;">タイトル:</label>
                                                    <input type="text" name="title" value="{{ old('title') }}"
                                                        required
                                                        class="flex-1 text-xs rounded px-1 py-0.5 bg-gray-700 border-gray-500">
                                                    <button type="submit"
                                                        class="bg-green-600 text-white px-2 py-0.5 rounded text-xs whitespace-nowrap">登録</button>
                                                </div>
                                                <div class="flex justify-between items-center mb-1 gap-2">
                                                    <label class="text-xs text-right"
                                                        style="width: 70px;">表示HTML:</label>
                                                    <textarea name="html" rows="2"
                                                        class="flex-1 resize-x-none w-full text-xs rounded px-1 py-0.5 bg-gray-700 border-gray-500">{{ old('html') }}</textarea>
                                                </div>
                                                <div class="flex items-end space-x-2 text-xs mb-1">
                                                    <div class="flex justify-between items-center gap-2 w-full">
                                                        <label class="text-xs text-right"
                                                            style="width: 70px;">開始:</label>
                                                        <input type="datetime-local" name="start_at"
                                                            value="{{ old('start_at') }}"
                                                            class="flex-1 text-xs rounded bg-gray-700 border-gray-500 px-1 py-0.5"
                                                            style="width: calc(100% - 70px);">
                                                    </div>
                                                    <div class="flex justify-between items-center gap-2 w-full">
                                                        <label class="text-xs text-right"
                                                            style="width: 70px;">終了:</label>
                                                        <input type="datetime-local" name="end_at"
                                                            value="{{ old('end_at') }}"
                                                            class="flex-1 text-xs rounded bg-gray-700 border-gray-500 px-1 py-0.5"
                                                            style="width: calc(100% - 70px);">
                                                    </div>
                                                </div>

                                                @if ($errors->any())
                                                    <div class="text-xs text-red-400 mt-1">
                                                        <ul class="list-disc list-inside">
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.add-banner-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const form = document.querySelector(targetId);
                    const bodyId = btn.getAttribute('data-body');
                    if (form) form.classList.toggle('hidden');
                    if (bodyId) document.querySelector(bodyId)?.classList.remove('hidden');
                });
            });
            document.querySelectorAll('.slot-header').forEach(header => {
                header.addEventListener('click', (e) => {
                    if (e.target.closest('button')) return;
                    const target = header.getAttribute('data-toggle');
                    document.querySelector(target)?.classList.toggle('hidden');
                });
            });
            document.getElementById('expand-all')?.addEventListener('click', () => {
                document.querySelectorAll('[id^="slot-body-"]').forEach(el => el.classList.remove('hidden'));
            });
            document.getElementById('collapse-all')?.addEventListener('click', () => {
                document.querySelectorAll('[id^="slot-body-"]').forEach(el => el.classList.add('hidden'));
            });
        </script>
    @endpush
</x-app-layout>
