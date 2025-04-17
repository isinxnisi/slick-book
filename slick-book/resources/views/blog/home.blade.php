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

    {{-- SP広告 --}}
    <div class="container justify-content-md-center mb-4 p-0 d-md-none">
        @include('components.blog.ad.sp-320x50')
    </div>

    @php
        $purposeStyles = config('tags.post_purpose_styles');
        $defaultStyle = $purposeStyles['public'];
        $cnt = 0;
    @endphp
    <div class="container justify-content-md-center rounded-md bg-gray-100 p-8 pt-0 mt-4">
        <div class="overflow-hidden bg-gray-100 sm:rounded-lg">
            <div class="py-6 px-0">
                <h2 class="content-headding">新着記事</h2>

                @foreach ($posts as $post)
                    @php $cnt++; @endphp
                    @include('components.blog.partials.post-item', ['post' => $post, 'purpose' => 'public'])
                    @if ($cnt % 4 == 0)
                    {{-- SP広告 --}}
                    <div class="container justify-content-md-center my-2 p-0 d-md-none">
                        @include('components.blog.ad.sp-320x100')
                    </div>
                    @endif
                @endforeach

                {{ $posts->links() }}
            </div>
        </div>
    </div>

    {{-- SP広告 --}}
    <div class="container justify-content-md-center mb-4 p-0 d-md-none">
        @include('components.blog.ad.sp-320x100')
    </div>

    <x-slot name="rAside">
        <div class="r-side-content mt-0 pb-0 sticky overflow-hidden add" style="width: 300px;">
            <div class="add bg-gray-300 shadow-sm" style="width: 300px; height: 250px;">
                @include('components.blog.ad.adm-shinobi')
            </div>
            <div class="overflow-hidden rounded add bg-gray-300 shadow-sm" style="width: 300px; margin-top: 20px">
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
            </div>
            <div class="add bg-gray-300 shadow-sm" style="width: 300px; height: 250px; margin-top: 20px">
                @include('components.blog.ad.adm-shinobi')
            </div>
        </div>
    </x-slot>

</x-blog-layout>
