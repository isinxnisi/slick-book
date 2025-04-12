@section('title', '記事一覧')
<x-blog-layout>
    <x-slot name="header">
        <x-blog.header />
    </x-slot>

    <div class="max-w-5xl mb-2">
        <div class="overflow-hidden bg-gray-100 sm:rounded-lg">
            <div class="p-6">
                <h2 class="content-headding mb-2">{{ $tag->name }}</h2>
                <div class="ps-2">
                    <p class="text-sm font-bold text-gray-600">{{ $tag->description }}</p>
                </div>
                 {{-- 子カテゴリ --}}
                @if (!$tagGroups->isEmpty())
                <h3 class="content-headding-sub mt-4">グループ</h3>
                <ul class="ps-2">
                    @foreach ($tagGroups as $tagGrp)
                    <li class="border-1 border-gray-300 rounded mt-1">
                        <a href="{{ route('blog.category', ['slug' => $tagGrp->slug]) }}"
                            class="flex align-items-center text-sm font-bold ps-2 py-2 text-gray-600 hover:bg-gray-200
                                {{ request()->routeIs('blog.category') && request()->slug === $tagGrp->slug ? 'bg-indigo-100 font-semibold border-r-4 border-indigo-300' : '' }}">
                            <span>{{ $tagGrp->breadcrumb }}</span>
                            @if (!empty($tagGrp->description))
                            <span class="text-gray-400"><span class="px-4">・・・</span>{{ $tagGrp->description }}</span>
                            @endif
                        </a>
                    </li>
                    @endforeach
                </ul>
                 @endif
            </div>
        </div>
    </div>

    @if (!$posts->isEmpty())
    <div class="max-w-5xl mb-2">
        <div class="overflow-hidden bg-gray-100 sm:rounded-lg">
            <div class="p-6">
                <h2 class="content-headding">新着記事</h2>
                @foreach ($posts as $post)
                    @include('components.blog.partials.post-item', ['post' => $post, 'purpose' => 'public'])
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <x-slot name="rAside">
        <div class="r-side-content mt-0 pb-0 sticky overflow-hidden rounded add bg-gray-300 shadow-sm" style="width: 300px;">
            <img src="{{asset('img/example-labo-img.png')}}" alt="">
            <img src="{{asset('img/my-lab-charactor.png')}}" alt="">
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