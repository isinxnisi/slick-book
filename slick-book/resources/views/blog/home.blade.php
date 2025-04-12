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