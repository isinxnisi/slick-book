@section('title', '記事一覧')
<x-app-layout>
    <x-slot name="header">
        <!-- サイト切替タブ -->
        <x-admin.ui.site-tabs :sites="$sites" :active-id="$siteId" />
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('posts.create') }}?site={{ $siteId }}" class="btn bg-indigo-800 text-white px-4 py-2 rounded hover:bg-indigo-900">
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
                            <time class="c-postTitle__date d-inline-block" datetime="{{ $post->published }}" aria-hidden="true">
                                <span class="__ymd">{{ $post->published->format('y/m/d') }}</span>
                                <span class="__time">{{ $post->published->format('H:i:s') }}</span>
                            </time>
                        </div>
                        <div class="px-2 w-20 border-r border-gray-700 d-flex align-items-center">
                            @php
                                $thumbnail = $post->postThumbnailImage;
                            @endphp
                            @if ($thumbnail)
                            <img src="{{ route('admin.media', [
                                    'site' => $post->site_id,
                                    'path' => "{$thumbnail->type}/" . basename($thumbnail->path)
                                ]) }}"
                                alt="{{ $thumbnail->alt ?? '' }}"
                                class="h-16 rounded shadow border object-fit-cover">
                            @else
                                <img src="{{ asset('img/noImage.jpg') }}" alt="">
                            @endif
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
