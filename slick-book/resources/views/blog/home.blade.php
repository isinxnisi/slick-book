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

    {{-- バナー: センター 位置: スロット 1 --}}
    <x-blog.ad.banner-slot section="center" :slot-no="1" />

    @php
        $purposeStyles = config('tags.post_purpose_styles');
        $defaultStyle = $purposeStyles['public'];
        $cnt = 0;
    @endphp
    <div class="container justify-content-md-center rounded-md bg-gray-100 p-8 pt-0">
        <div class="overflow-hidden bg-gray-100 sm:rounded-lg">
            <div class="py-6 px-0">
                <h2 class="content-headding">新着記事</h2>

                @foreach ($posts as $post)
                    @php $cnt++; @endphp
                    @include('components.blog.partials.post-item', ['post' => $post, 'purpose' => 'public'])
                    @if ($cnt % 4 == 0)
                    {{-- バナー: センター 位置: スロット 3 --}}
                    <x-blog.ad.banner-slot section="center" :slot-no="3" marginClass="mt-2" />
                    @endif
                @endforeach

                {{ $posts->links() }}
            </div>
        </div>
    </div>

    {{-- バナー: センター 位置: スロット 2 --}}
    <x-blog.ad.banner-slot section="center" :slot-no="2" marginClass="mt-4" />

    <x-slot name="rAside">
        <div class="r-side-content mt-0 pb-0 sticky overflow-hidden add" style="width: 300px;">
            {{-- バナー: 右サイド 位置: スロット 1 --}}
            @if ($bannerComponent->hasBanner('rside', 1))
            <div class="add bg-gray-300 shadow-sm" style="width: 300px; height: 250px;">
                <x-blog.ad.banner-slot section="rside" :slot-no="1" marginClass="" />
            </div>
            @endif
            {{-- バナー: 右サイド 位置: スロット 2 --}}
            @if ($bannerComponent->hasBanner('rside', 2))
            <div class="overflow-hidden rounded add bg-gray-300 shadow-sm" style="width: 300px; margin-top: 20px">
                <x-blog.ad.banner-slot section="rside" :slot-no="2" marginClass="" />
            </div>
            @endif
            {{-- バナー: 右サイド 位置: スロット 3 --}}
            @if ($bannerComponent->hasBanner('rside', 3))
            <div class="add bg-gray-300 shadow-sm" style="width: 300px; height: 250px; margin-top: 20px">
                <x-blog.ad.banner-slot section="rside" :slot-no="3" marginClass="" />
            </div>
            @endif
        </div>
    </x-slot>

</x-blog-layout>
