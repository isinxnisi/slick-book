@section('title', 'HOME')
<x-blog-layout>
    <x-slot name="header">
        <x-blog.header />
    </x-slot>

    <x-slot name="eyecatch">
        @php
            $site = app('CurrentSite');
            $eyecatch = $site->images->firstWhere('type', 'site_eyecatch');
        @endphp
        @isset($eyecatch)
        <div class="site-image eyecatch">
            <img src="{{ route('secure.media', [
                    'site' => "{$eyecatch->site_id}",
                    'path' => "{$eyecatch->type}/" . basename($eyecatch->path),
                ]) }}"
                alt="{{ $eyecatch->alt }}"
                class="rounded">
        </div>
        @endisset
    </x-slot>

    @php
        $purposeStyles = config('tags.post_purpose_styles');
        $defaultStyle = $purposeStyles['public'];
    @endphp
    <div class="max-w-5xl">
        <div class="overflow-hidden bg-gray-100 sm:rounded-lg">
            <div class="p-6">
                <h2 class="content-headding">新着記事</h2>

                @foreach ($posts as $post)
                    @include('components.blog.partials.post-item', ['post' => $post, 'purpose' => 'public'])
                @endforeach

                {{ $posts->links() }}
            </div>
        </div>
    </div>

    <x-slot name="rAside">
        <div class="r-side-content mt-0 pb-0 sticky overflow-hidden rounded add bg-gray-300 shadow-sm" style="width: 300px;">
            <img src="{{asset('img/example-labo-img.png')}}" alt="">
            @php
                $site = app('CurrentSite');
                $thumbnail = $site->images->firstWhere('type', 'thumbnail');
            @endphp
            @isset($thumbnail)
            <img src="{{ route('secure.media', [
                    'site' => "{$thumbnail->site_id}",
                    'path' => "{$thumbnail->type}/" . basename($thumbnail->path),
                ]) }}"
                alt="{{ $thumbnail->alt }}">
            @endisset
            <img src="{{asset('img/example-labo-img.png')}}" alt="">
        </div>
        <div class="overflow-hidden rounded add bg-gray-300 shadow-sm" style="width: 300px; margin-top: 20px">
        </div>
        <div class="add hidden bg-gray-300" style="width: 300px; height: 250px; margin-top: 20px">
        </div>
        <div class="r-side-content">
            <div class="add hidden bg-gray-300" style="width: 300px; height: 250px">
            </div>
            <div class="add hidden bg-gray-300" style="width: 300px; height: 250px; margin-top: 20px">
            </div>
        </div>
    </x-slot>

</x-blog-layout>
