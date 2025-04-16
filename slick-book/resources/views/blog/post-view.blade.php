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

        <div class="flex px-4">
            {{-- タイトル --}}
            <h1 class="">{{ $post->title }}</h1>
        </div>

        <div class="flex mt-2 px-4">
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
        <article class="post mt-4 mb-4 px-4">
            {{-- 本文（HTML） --}}
            <div class="post-layout" style="gap: 2rem;">
                <div class="post-content">
                    <div class="post-body">{!! $post->html_body !!}</div>
                </div>
            </div>

        </article>
    </div>
    <div class="container justify-content-md-center rounded-md bg-gray-100 p-8 mt-4">
        <div class="mt-0 px-4">
            <h2 class="content-headding">おすすめ記事</h2>
            <div class="py-0">
                @foreach ($recommendPosts as $reccomendPost)
                @include('components.blog.partials.post-item', ['post' => $reccomendPost, 'purpose' => 'public'])
                @endforeach
            </div>
        </div>
    </div>

    <x-slot name="rAside">
        <div class="add bg-gray-300 overflow-hidden shadow-sm" style="width: 300px;">
            @include('components.blog.ad.adm-shinobi')
        </div>
        <div class="add hidden bg-gray-300 overflow-hidden shadow-sm" style="width: 300px; height: 250px; margin-top: 20px">
        </div>
        <div class="r-side-content">
            <div class="add bg-gray-300 overflow-hidden shadow-sm" style="width: 300px; height: 250px">
                @include('components.blog.ad.adm-shinobi')
            </div>
            @if(!empty($post->toc))
            <div class="post-toc">{!! $post->toc !!}</div>
            @endif
            <div class="add hidden bg-gray-300 overflow-hidden shadow-sm" style="width: 300px; height: 250px; margin-top: 20px">
            </div>
        </div>
    </x-slot>

</x-blog-layout>
