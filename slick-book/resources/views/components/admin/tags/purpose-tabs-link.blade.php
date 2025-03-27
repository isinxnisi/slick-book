@props([
    'activePurpose' => 'public',
])

@php
    $purposes = config('tags.purposes');
    $purposeStyles = config('tags.purpose_styles');
@endphp

<div class="mb-4 border-b border-gray-700 px-0">
    <nav class="flex space-x-2" aria-label="タグ目的切替タブ">
        @foreach ($purposes as $key => $label)
            @php
                $isActive = $activePurpose === $key;
                $style = $purposeStyles[$key] ?? ['bg' => '#4f46e5', 'text' => '#fff'];
                $url = request()->fullUrlWithQuery(['purpose' => $key]);
            @endphp

            <a
                href="{{ $url }}"
                class="relative px-2 py-2 text-sm font-medium transition-all
                    {{ $isActive 
                        ? 'text-white border-b-2 border-indigo-500' 
                        : 'text-gray-400 hover:text-white hover:border-b-2 hover:border-gray-500' }}"
                style="border-color: {{ $isActive ? $style['bg'] : 'transparent' }};"
            >
                {{ $label }}
            </a>
        @endforeach
    </nav>
</div>
