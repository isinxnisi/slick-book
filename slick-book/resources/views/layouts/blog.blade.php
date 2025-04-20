<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $title = trim($__env->yieldContent('title'));
@endphp
    <title>{{ $title ? $title . '｜' : '' }}{{ $seo['meta_title'] ?? $currentSite->name }}</title>
    <x-blog.meta-head :seo="$seo" :site="$currentSite" />
@if(!empty($seo['noindex']) && $seo['noindex'] === true)
    <meta name="robots" content="noindex, follow">
@endif
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- App -->
    @vite(['resources/css/app.css'])

    <link rel="stylesheet" href="{{ asset('css/theme/site-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme/post-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prism-themes/prism-dracula.css') }}">

    <!-- Scripts -->
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/js/blog.js'])

@yield('structured_data') {{-- JSON-LD埋め込み --}}
</head>
{!! $seo['google_analytics_tags'] ?? '' !!}
<body class="font-sans antialiased bg-gray-200">
    <div class="min-h-screen">

        @include('components.blog.navigation', ['pageTitle' => View::getSections()['title'] ?? ''])

        <!-- Page Heading -->
        @isset($header)
        <header id="content-header" class="sticky top-0 text-gray-200 bg-gray-700 shadow-sm">
            <div class="mx-auto">
                {{ $header }}
            </div>
        </header>
        @endisset

        @isset($eyecatch)
        <div class="w-100">
            {{ $eyecatch }}
        </div>
        @endisset

        <!-- Page Content -->
        <main class="pb-6 shadow-lg">
            <div class="flex py-6" style="min-height: 100vh;">

                <div id="main-layout" class="flex mx-auto" style="opacity: 0">
                    <!-- サイドメニュー -->
                    @include('components.blog.sidebar')

                    <div class="content-container container px-6">
                        {{ $slot }}
                    </div>
                    <aside id="site-r-side-menu">
                        @isset($rAside)
                        {{ $rAside }}
                        @endisset
                    </aside>
                </div>
            </div>
        </main>
        <footer class="text-gray-800 bg-gray-300" style="height: 100px">

        </footer>
    </div>

    <!-- jQuery & jQuery UI -->

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
            $('#main-layout').animate({opacity: 1}, 250);
        });
    </script>

    @stack('scripts')
</body>

</html>
