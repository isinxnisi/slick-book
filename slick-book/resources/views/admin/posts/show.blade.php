@section('title', $post->title)
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('投稿') }}
        </h2>
    </x-slot>

    <div class="mx-auto sm:px-6 lg:px-8">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <h1>{{ $post->title }}</h1>
                <p>{!! $post->html_body !!}</p>

                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning">編集</a>

                <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">削除</button>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>