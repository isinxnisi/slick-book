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
                    <div class="row justify-content-md-center rounded-md">
                        <div class="px-2 w-20 border-r border-gray-700 d-flex align-items-center">
                            <time class="c-postTitle__date d-inline-block" datetime="{{ $post->created }}" aria-hidden="true">
                                <span class="__ymd">{{ $post->created->format('y/m/d') }}</span>
                                <span class="__time">{{ $post->created->format('H:i:s') }}</span>
                            </time>
                        </div>
                        <div class="px-2 w-20 border-r border-gray-700 d-flex align-items-center">
                            <img src="{{ asset('img/noImage.jpg') }}" alt="">
                        </div>
                        <div class="flex-1 px-2">
                            {{-- タイトル --}}
                            <h2 class="text-lg bold">
                                <a class="hover:text-white hover:border-b hover:border-gray-200" href="{{ route('posts.edit', $post->id) }}">{{ $post->title }}</a>
                            </h2>
                            <p class="pt-2">{{ Str::limit($post->body, 100) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach

                {{ $posts->links() }}
            </div>
        </div>
    </div>

</x-app-layout>