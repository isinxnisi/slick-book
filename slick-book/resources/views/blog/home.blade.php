@section('title', '記事一覧')
<x-blog-layout>
    <x-slot name="header">
        <x-blog.header />
    </x-slot>

    @php
        $purposeStyles = config('tags.post_purpose_styles');
        $defaultStyle = $purposeStyles['public'];
    @endphp
    <div class="max-w-5xl">
        <div class="overflow-hidden bg-gray-100 shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="content-headding">記事</h2>

                @foreach ($posts as $post)
                <div class="post-item-bar mt-2 border-t border-l border-gray-200 rounded-md shadow-md p-3 ms-0 text-xs bg-white">
                    <div class="row justify-content-md-center rounded-md">
                        <div class="thumnail d-flex px-2 w-30 border-r border-gray-200 align-items-center">
                            <img src="{{ asset('img/noImage.jpg') }}" alt="">
                        </div>
                        <div class="post-info flex-1 px-4">
                            {{-- タイトル --}}
                            <h2 class="title text-lg bold d-flex align-items-center">
                                <a class="" href="{{ route('posts.view', $post->id) }}">{{ $post->title }}</a>
                            </h2>
                            <div class="d-flex">
                                <time class="d-inline-block py-2 me-2" datetime="{{ $post->created }}" aria-hidden="true">
                                    <span class="__ymd">{{ $post->created->format('y/m/d H:i:s') }}</span>
                                </time>
                                <ul class="tag-list mt-2 px-0">
                                    @foreach ($post->tags as $tag)
                                        <li class="tag-item">
                                            <a class="tag" style="background-color: {{ $defaultStyle['bg'] }}; color: {{ $defaultStyle['text'] }}; border: 1px solid {{ $defaultStyle['bg'] }}">
                                                #{{ $tag->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <p class="pt-2">{{ Str::limit($post->body, 100) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach

                {{ $posts->links() }}
            </div>
        </div>
    </div>

</x-blog-layout>