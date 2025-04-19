<div class="post-item-bar mt-2 border-t border-l border-gray-200 rounded-md shadow-md p-3 ms-0 text-xs bg-white">
    <div class="row justify-content-md-center rounded-md">
        <div class="thumnail d-flex px-2 w-30 border-r border-gray-200 align-items-center">
            @php
                $image = $post->thumbnail_image;
            @endphp
            <a class="" href="{{ route('posts.view', $post->id) }}">
            @if ($image)
                <img src="{{ route('secure.media', ['site' => $image->site_id, 'path' => $image->path]) }}"
                    alt="{{ $image->alt }}"
                    class="rounded shadow">
            @else
                <img class="rounded shadow-sm" src="{{ asset('img/noImage.jpg') }}" alt="">
            @endif
            </a>
        </div>
        <div class="post-info flex-1 lg:px-4">
            {{-- タイトル --}}
            <h2 class="title text-lg bold d-flex align-items-center border-b">
                <a class="" href="{{ route('posts.view', $post->id) }}">{{ $post->title }}</a>
            </h2>
            <div class="d-inline-block w-100 pt-2">
                <div class="post-tag-cat w-100 float-start">
                    <p class="cat">{{ $post->category?->breadcrumb }}</p>
                    <ul class="tag-list text-left px-0">
                        @foreach ($post->tags as $tag)
                        @include('components.blog.partials.tag-item', ['tag' => $tag, 'purpose' => $purpose])
                        @endforeach
                    </ul>
                </div>
            </div>
            <p class="desc pt-0">
                {{ Str::limit($post->seo_description, 100) }}
            </p>
            <time class="d-inline-block font-bold text-gray-400 mt-1 py-0 text-right mx-1 float-end" datetime="{{ $post->published }}" aria-hidden="true" style="min-width:100px;">
                <span class="__ymd">{{ $post->published->format('Y/m/d H:i') }}</span>
            </time>
        </div>
    </div>
</div>
