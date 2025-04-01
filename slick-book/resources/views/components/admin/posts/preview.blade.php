<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    <!-- Scripts -->
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css'])

    
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-200">
        @php
            $purposeStyles = config('tags.post_purpose_styles');
            $defaultStyle = $purposeStyles['public'];
        @endphp
        <main class="p-6">
            <div class="flex">
                <time class="c-postTitle__date flex-1" datetime="2023-03-16" aria-hidden="true">
                    <span class="__y">2023</span>
                    <span class="__md">3/16</span>
                </time>
                {{-- タイトル --}}
                <h1 class="">{{ $post->title }}</h1>
            </div>
        
            {{-- タグ一覧 --}}
            <ul class="tag-list mt-4 p-0">
                @foreach ($post->tags as $tag)
                    <li class="tag-item">
                        <a class="tag" style="background-color: {{ $defaultStyle['bg'] }}; color: {{ $defaultStyle['text'] }}; border: 1px solid {{ $defaultStyle['bg'] }}">
                            #{{ $tag->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <article class="post mt-4">

                {{-- 本文（HTML） --}}
                <div class="post-layout" style="gap: 2rem;">
                    <div class="toc mx-auto">
                        {{-- 目次（オプション） --}}
                        {!! $toc !!}
                    </div>
                    <div class="post-content">
                        <div class="post-body">{!! $post->html_body !!}</div>
                    </div>
                </div>

            </article>
        </main>
    </div>

    <!-- jQuery & jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    {{-- PRISM --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism.min.css">
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
    <!-- 必要な言語を追加 -->
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-php.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-javascript.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        });
    </script>

</body>

</html>