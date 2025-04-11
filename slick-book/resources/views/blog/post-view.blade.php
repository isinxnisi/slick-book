@section('title', '記事一覧')
<x-blog-layout>
    <x-slot name="header">
        <x-blog.header />
    </x-slot>

    @php
        $purposeStyles = config('tags.post_purpose_styles');
        $defaultStyle = $purposeStyles['public'];
    @endphp
    <div id="post-article" class="container justify-content-md-center rounded-md bg-gray-100 p-8">

        <div class="flex px-4">
            <time class="c-postTitle__date flex-1" datetime="{{ $post->published }}" aria-hidden="true">
                <span class="__y">{{ $post->published->format('Y') }}</span>
                <span class="__md">{{ $post->published->format('m/d') }}</span>
            </time>
            {{-- タイトル --}}
            <h1 class="">{{ $post->title }}</h1>
        </div>

        {{-- タグ一覧 --}}
        <ul class="tag-list mt-4 px-4">
            @foreach ($post->tags as $tag)
                <li class="tag-item">
                    <a class="tag" style="background-color: {{ $defaultStyle['bg'] }}; color: {{ $defaultStyle['text'] }}; border: 1px solid {{ $defaultStyle['bg'] }}">
                        #{{ $tag->name }}
                    </a>
                </li>
            @endforeach
        </ul>
        <article class="post mt-4 mb-4 px-4">
            {{-- 本文（HTML） --}}
            <div class="post-layout" style="gap: 2rem;">
                <div class="post-content">
                    <div class="post-body">{!! $post->html_body !!}</div>
                </div>
            </div>

        </article>
    </div>
    <div class="container justify-content-md-center rounded-md bg-gray-100 p-8 mt-4">
        <div class="mt-0 px-4">
            <h2 class="content-headding">おすすめ記事</h2>
            <div class="py-0">
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
                                <time class="d-inline-block py-2 me-2" datetime="{{ $post->published }}" aria-hidden="true">
                                    <span class="__ymd">{{ $post->published->format('y/m/d H:i:s') }}</span>
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
                <div class="add bg-gray-300" style="width: 100%; height: 150px; margin-top: 20px">
                </div>
                <div class="add bg-gray-300" style="width: 100%; height: 150px; margin-top: 20px">
                </div>
                <div class="add hidden bg-gray-300" style="width: 100%; height: 150px; margin-top: 20px">
                </div>
                <div class="add hidden bg-gray-300" style="width: 100%; height: 150px; margin-top: 20px">
                </div>
            </div>
        </div>
    </div>

    <x-slot name="rAside">
        <div class="add bg-gray-300 overflow-hidden rounded shadow-sm" style="width: 300px;">
            <img src="{{asset('img/example-labo-img.png')}}" alt="">
        </div>
        <div class="add hidden bg-gray-300" style="width: 300px; height: 250px; margin-top: 20px">
        </div>
        <div class="r-side-content">
            <div class="add hidden bg-gray-300" style="width: 300px; height: 250px">
            </div>
            <div class="post-toc">{!! $post->toc !!}</div>
            <div class="add hidden bg-gray-300" style="width: 300px; height: 250px; margin-top: 20px">
            </div>
        </div>
    </x-slot>

</x-blog-layout>