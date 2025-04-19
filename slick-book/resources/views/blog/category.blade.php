@section('title', $category->title)
<x-blog-layout>
    <x-slot name="header">
        <x-blog.header />
    </x-slot>

    {{-- バナー: センター 位置: スロット 1 --}}
    <x-blog.ad.banner-slot section="center" :slot-no="1" />

    <div class="container justify-content-md-center rounded-md bg-gray-100 p-8 pt-0">
        <div class="overflow-hidden bg-gray-100 sm:rounded-lg">
            <div class="py-6 px-0">
                <p class="text-sm font-bold text-gray-400 mb-2">{{ $category->breadcrumbWOSelf }}</p>
                <h2 class="content-headding flex align-item-center mb-2">
                    <code class="text-xs px-2 py-2 me-2 rounded bg-gray-200">カテゴリ</code>
                    <span>{{ $category->title }}</span>
                </h2>
                <div class="ps-2">
                    <p class="text-sm font-bold text-gray-600">{{ $category->description }}</p>
                </div>
                {{-- 子カテゴリ --}}
                @if (!$category->children->isEmpty())
                    <h3 class="content-headding-sub mt-4">子カテゴリ</h3>
                    <ul class="content-item-list ps-2">
                        @foreach ($category->children as $child)
                            <li class="border-1 border-gray-300 rounded mt-1">
                                <a href="{{ route('blog.category', ['slug' => $child->slug]) }}"
                                    class="flex align-items-center text-sm font-bold px-2 py-2 text-gray-600 hover:bg-gray-200
                                {{ request()->routeIs('blog.category') && request()->slug === $child->slug ? 'bg-indigo-100 font-semibold border-r-4 border-indigo-300' : '' }}">
                                    <span class="item-name">{{ $child->title }}</span>
                                    @if (!empty($child->description))
                                    <span class="item-sep text-gray-400 px-4">・・・</span>
                                    <span class="item-desc text-gray-400">{{ $child->description }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                {{-- タグ --}}
                @if (!$categoryTags->isEmpty())
                    <h3 class="content-headding-sub mt-4">このカテゴリ内記事に含まれるタグ</h3>
                    <ul class="tag-list mt-2 px-0 ps-2">
                        @foreach ($categoryTags as $tag)
                            @include('components.blog.partials.tag-item', [
                                'tag' => $tag,
                                'purpose' => 'public',
                                'link' => true,
                            ])
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    @if (!$posts->isEmpty())
    <div class="container justify-content-md-center rounded-md bg-gray-100 p-8 pt-0 mt-4">
        <div class="overflow-hidden bg-gray-100 sm:rounded-lg">
            <div class="py-6 px-0">
                @php $cnt = 0; @endphp
                <h2 class="content-headding">カテゴリ内記事</h2>
                @foreach ($posts as $post)
                    @php $cnt++; @endphp
                    @include('components.blog.partials.post-item', [
                        'post' => $post,
                        'purpose' => 'public',
                    ])
                    @if ($cnt % 4 == 0)
                    {{-- バナー: センター 位置: スロット 3 --}}
                    <x-blog.ad.banner-slot section="center" :slot-no="3" marginClass="mt-2" />
                    @endif
                @endforeach

                {{ $posts->links() }}
            </div>
        </div>
    </div>
    @endif

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
