@props([
    'purposes' => [],        // 例: ['main' => 'メイン', 'topic' => 'トピック']
    'active' => '',          // 初期表示するpurposeキー
])

<div class="flex border-b border-gray-300 dark:border-gray-600 mb-4">
    @foreach($purposes as $key => $label)
        <button
            class="purpose-tab px-4 py-2 text-sm font-medium focus:outline-none hover:bg-gray-100 dark:hover:bg-gray-700
                   {{ $active === $key ? 'border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300' }}"
            data-purpose="{{ $key }}"
        >
            {{ $label }}
        </button>
    @endforeach
</div>
