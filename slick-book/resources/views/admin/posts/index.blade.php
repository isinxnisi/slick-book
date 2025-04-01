@section('title', '記事一覧')
<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('posts.create') }}" class="btn bg-indigo-800 text-white px-4 py-2 rounded hover:bg-indigo-900">
            新規投稿
        </a>
    </div>


    <div class="max-w-5xl">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                @foreach ($posts as $post)
                <div class="mt-2 border-t border-l border-gray-700 rounded-md shadow-md p-3 ms-0 text-xs">
                    <h2 class="text-lg bold"><a href="{{ route('posts.edit', $post->id) }}">{{ $post->title }}</a></h2>
                    <p>{{ Str::limit($post->body, 100) }}</p>
                </div>
                @endforeach

                {{ $posts->links() }}
            </div>
        </div>
    </div>

</x-app-layout>