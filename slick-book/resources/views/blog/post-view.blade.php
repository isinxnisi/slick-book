@section('title', $post->title)
@section('structured_data')
    <script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => url()->current(),
    ],
    'headline' => $post->seoSetting->meta_title ?? $post->title,
    'description' => $post->seo_description,
    'image' => $post->thumbnail_image?->url ?? null,
    'author' => [
        '@type' => 'Person',
        'name' => $post->creator->name ?? 'Unknown',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => $currentSite->name,
        'logo' => [
            '@type' => 'ImageObject',
            'url' => route('secure.media', [
                'site' => $currentSite->id,
                'path' => 'favicon/' . basename($currentSite->images->firstWhere('type', 'favicon')->path ?? 'default.png')
            ]),
        ],
    ],
    'datePublished' => optional($post->published_at)->toIso8601String(),
    'dateModified' => optional($post->updated_at)->toIso8601String(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($breadcrumbs)->values()->map(function ($item, $i) {
        return [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['name'],
            'item' => $item['url'],
        ];
    })->toArray()
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
<x-blog-layout>
    <x-slot name="header">
        <x-blog.header />
    </x-slot>

    {{-- バナー: センター 位置: スロット 1 --}}
    <x-blog.ad.banner-slot section="center" :slot-no="1" />

    @php
        $image = $post->postEyecatchImage;
    @endphp
    @if ($image)
    <div class="container px-0 mb-4">
        <div class="post-image eyecatch">
            <img src="{{ route('secure.media', [
                    'site' => "{$image->site_id}",
                    'path' => "{$image->type}/" . basename($image->path),
                ]) }}"
                alt="{{ $image->alt }}"
                class="rounded">
        </div>
    </div>
    @endif

    @php
        $purposeStyles = config('tags.post_purpose_styles');
        $defaultStyle = $purposeStyles['public'];
    @endphp
    <div id="post-article" class="container justify-content-md-center rounded-md bg-gray-100 p-8">

        <div class="flex">
            {{-- タイトル --}}
            <h1 class="">{{ $post->title }}</h1>
        </div>

        <div class="flex mt-2">
            <time class="c-postTitle__date flex-1" datetime="{{ $post->published }}" aria-hidden="true">
                <span class="__ymd">{{ $post->published->format('Y/m/d H:i') }}</span>
            </time>
            {{-- タグ一覧 --}}
            <ul class="tag-list align-self-center">
                @foreach ($post->tags as $tag)
                    <li class="tag-item">
                        @include('components.blog.partials.tag-item', ['tag' => $tag, 'purpose' => 'public', 'link' => true])
                    </li>
                @endforeach
            </ul>
        </div>
        <article class="post mt-4 mb-4">
            {{-- 本文（HTML） --}}
            <div class="post-layout" style="gap: 2rem;">
                <div class="post-content">
                    <div class="post-body">{!! $post->html_body !!}</div>
                </div>
            </div>

        </article>
    </div>

    {{-- バナー: センター 位置: スロット 2 --}}
    <x-blog.ad.banner-slot section="center" :slot-no="2" marginClass="mt-4" />

    <div class="container justify-content-md-center rounded-md bg-gray-100 p-8 pt-0 mt-4">
        <div class="mt-0 pt-4">
            <h2 class="content-headding">おすすめ記事</h2>
            <div class="py-0">
                @php $cnt = 0; @endphp
                @foreach ($recommendPosts as $reccomendPost)
                @php $cnt++; @endphp
                @include('components.blog.partials.post-item', ['post' => $reccomendPost, 'purpose' => 'public'])
                @if ($cnt % 4 == 0)
                {{-- バナー: センター 位置: スロット 3 --}}
                <x-blog.ad.banner-slot section="center" :slot-no="3" marginClass="mt-2" />
                @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- バナー: センター 位置: スロット 2 --}}
    <x-blog.ad.banner-slot section="center" :slot-no="2" marginClass="mt-4" />

    <x-slot name="rAside">
        {{-- バナー: 右サイド 位置: スロット 1 --}}
        @if ($bannerComponent->hasBanner('rside', 1))
        <div class="add bg-gray-300 shadow-sm" style="width: 300px;">
            <x-blog.ad.banner-slot section="rside" :slot-no="1" marginClass="" />
        </div>
        @endif
        <div class="r-side-content">
            {{-- バナー: 右サイド 位置: スロット 3 --}}
            @if ($bannerComponent->hasBanner('rside', 3))
            <div class="add bg-gray-300 shadow-sm" style="width: 300px; height: 250px;">
                <x-blog.ad.banner-slot section="rside" :slot-no="3" marginClass="" />
            </div>
            @endif
            @if(!empty($post->toc))
            <div class="post-toc">{!! $post->toc !!}</div>
            @endif
            <div class="add hidden bg-gray-300 overflow-hidden shadow-sm" style="width: 300px; height: 250px; margin-top: 20px">
            </div>
        </div>
    </x-slot>

</x-blog-layout>
