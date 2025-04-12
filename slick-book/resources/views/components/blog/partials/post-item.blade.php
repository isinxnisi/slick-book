<div class="post-item-bar mt-2 border-t border-l border-gray-200 rounded-md shadow-md p-3 ms-0 text-xs bg-white">
    <div class="row justify-content-md-center rounded-md">
        <div class="thumnail d-flex px-2 w-30 border-r border-gray-200 align-items-center">
            @php
                $image = $post->thumbnail_image;
            @endphp
            @if ($image)
                <img src="{{ route('secure.media', ['path' => $image->path]) }}"
                     alt="{{ $image->alt }}"
                     class="rounded shadow">
            @else
                <img class="rounded shadow-sm" src="{{ asset('img/noImage.jpg') }}" alt="">
            @endif
        </div>
        <div class="post-info flex-1 px-4">
            {{-- タイトル --}}
            <h2 class="title text-lg bold d-flex align-items-center border-b">
                <a class="" href="{{ route('posts.view', $post->id) }}">{{ $post->title }}</a>
            </h2>
            <div class="d-flex py-2">
                <ul class="tag-list text-left px-0">
                    @foreach ($post->tags as $tag)
                    @include('components.blog.partials.tag-item', ['tag' => $tag, 'purpose' => $purpose])
                    @endforeach
                </ul>
                <time class="flex-1 d-inline-block font-bold text-gray-400 py-1 text-right ms-2" datetime="{{ $post->published }}" aria-hidden="true" style="min-width:100px;">
                    <span class="__ymd">{{ $post->published->format('Y/m/d H:i') }}</span>
                </time>
            </div>
            <p class="pt-2">{{ Str::limit($post->body, 100) }}</p>
        </div>
    </div>
</div>
