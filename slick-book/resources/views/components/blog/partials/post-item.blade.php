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
                        @include('components.blog.partials.tag-item', ['tag' => $tag, 'purpose' => $purpose])
                    @endforeach
                </ul>
            </div>
            <p class="pt-2">{{ Str::limit($post->body, 100) }}</p>
        </div>
    </div>
</div>