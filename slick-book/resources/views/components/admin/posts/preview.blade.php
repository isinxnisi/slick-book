<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('css/theme/post-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prism-themes/prism-dracula.css') }}">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css"> --}}

    <!-- Scripts -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-200">
        @php
            $purposeStyles = config('tags.post_purpose_styles');
            $defaultStyle = $purposeStyles['public'];
        @endphp
        <main id="post-article" class="container py-6">
            <div class="row justify-content-md-center rounded-md bg-gray-100 p-8">

            <div class="flex px-4">
                <time class="c-postTitle__date flex-1" datetime="2025-04-20" aria-hidden="true">
                    <span class="__y">2025</span>
                    <span class="__md">4/20</span>
                </time>
                {{-- タイトル --}}
                <h1 class="">{{ $post->title }}</h1>
            </div>

            {{-- タグ一覧 --}}
            <ul class="tag-list mt-4 px-4">
                @foreach ($post->tags as $tag)
                    <li class="tag-item">
                        <a class="tag" style="background-color: {{ $defaultStyle['bg'] }}; color: {{ $defaultStyle['text'] }}; border: 1px solid {{ $defaultStyle['bg'] }}">
                            #{{ $tag->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <article class="post mt-4 px-4">
                {{-- 本文（HTML） --}}
                <div class="post-layout" style="gap: 2rem;">
                    <div class="post-content">
                        <div class="post-body">{!! $post->html_body !!}</div>
                    </div>
                </div>

            </article>
            </div>
        </main>
    </div>

    <!-- jQuery & jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    {{-- PRISM --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

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