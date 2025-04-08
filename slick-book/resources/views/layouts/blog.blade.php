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
    <link rel="stylesheet" href="{{ asset('css/theme/site-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme/post-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prism-themes/prism-dracula.css') }}">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css"> --}}

    <!-- Scripts -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="font-sans antialiased bg-gray-200">
    <div class="min-h-screen flex">

        <!-- メインコンテンツ -->
        <div class="flex-1">
            @include('components.blog.navigation', ['pageTitle' => View::getSections()['title'] ?? ''])

            <!-- Page Heading -->
            @isset($header)
            <header id="content-header" class="sticky top-0 border-b border-gray-100 text-gray-200 bg-gray-700 shadow-sm">
                <div class="mx-auto">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <!-- Page Content -->
            <main class="">
                <div class="flex py-6">
                    <!-- サイドメニュー -->
                    @include('components.blog.sidebar')

                    <div id="main-layout" class="flex mx-auto">
                        <div class="container px-6">
                            {{ $slot }}
                        </div>
                        <aside>
                            @isset($rAside)
                            {{ $rAside }}
                            @endisset
                        </aside>
                    </div>
                </div>
            </main>
        </div>
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
        });
    </script>

    @stack('scripts')
</body>

</html>