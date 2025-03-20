<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('記事一覧') }}
        </h2>
    </x-slot>

    <div class="mx-auto sm:px-6 lg:px-8">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <a href="{{ route('posts.create') }}" class="btn btn-primary">新規投稿</a>

                @foreach ($posts as $post)
                <div>
                    <h2><a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a></h2>
                    <p>{{ Str::limit($post->body, 100) }}</p>
                </div>
                @endforeach

                {{ $posts->links() }}
            </div>
        </div>
    </div>

</x-app-layout>