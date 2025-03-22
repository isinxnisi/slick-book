@props([
    'sites' => [],
    'activeId' => null,
    'queryParam' => 'site',
])

<div class="mb-0 border-b border-gray-700 px-4">
    <nav class="flex space-x-2" aria-label="サイト切替タブ">
        @foreach ($sites as $site)
            <a href="?{{ $queryParam }}={{ $site->id }}"
               class="relative px-4 py-2 text-sm font-medium transition-all
               {{ $activeId == $site->id 
                    ? 'text-white border-b-2 border-indigo-500'
                    : 'text-gray-400 hover:text-white hover:border-b-2 hover:border-gray-500' }}">
                {{ $site->name }}
            </a>
        @endforeach
    </nav>
</div>
