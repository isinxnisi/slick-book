@section('title', $hierarchy->title)
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('階層') }}
        </h2>
    </x-slot>

    <div class="mx-auto sm:px-6 lg:px-8">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <ul>
                    @foreach ($hierarchy->children as $child)
                        <li><a href="{{ route('hierarchies.show', $child) }}">{{ $child->title }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

</x-app-layout>