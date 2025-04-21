<nav x-data="{ open: false }" id="nav-header" class="border-b border-gray-100 bg-white">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-content-center h-16">
            <div class="sm:flex sm:items-center sm:ms-0">
                <h2 class="flex h-16 font-semibold text-xl p-2 ps-2 border-b border-gray-100 leading-tight align-items-center">
                    <a href="{{ route('blog.home') }}">
                        <!-- Logo -->
                        <div class="flex items-center me-2" style="width: max-content;">
                            @php
                                $naviLogo = $currentSite->images->firstWhere('type', 'navi-logo');
                            @endphp

                            @if ($naviLogo)
                                <img src="{{ route('secure.media', [
                                    'site' => $naviLogo->site_id,
                                    'path' => 'navi-logo/' . basename($naviLogo->path),
                                ]) }}"
                                alt="{{ $naviLogo->alt ?? '記事のサムネイル画像' }}"
                                class="" style="height:33px;">
                            @else
                                @php
                                    $siteIcon = $post->siteIcon ?? $currentSite->images->firstWhere('type', 'site-icon');
                                @endphp
                                @if ($siteIcon)
                                    <img src="{{ route('secure.media', [
                                        'site' => $siteIcon->site_id,
                                        'path' => 'site-icon/' . basename($siteIcon->path),
                                    ]) }}"
                                    alt="{{ $siteIcon->alt ?? '記事のサムネイル画像' }}"
                                    class="rounded shadow-sm" style="height:33px;">
                                @endif
                                <span class="flex-1 d-block px-2">
                                    <span id="header-site-title" class="flex items-center w-100" style="word-break: keep-all;">
                                        {{$currentSite->name}}
                                        @if ($currentSite->sub_title)
                                        <span id="header-site-sub-title" class="text-sm text-gray-400 flex-1 ms-2" style="word-break: keep-all;">
                                            {!! $currentSite->sub_title !!}
                                        </span>
                                        @endif
                                    </span>
                                    @if ($currentSite->sub_message)
                                    <span id="header-site-sub-message" class="flex items-center text-xs text-gray-300" style="word-break: keep-all;">
                                        {!! $currentSite->sub_message !!}
                                    </span>
                                    @endif
                                </span>
                            @endif
                        </div>
                    </a>
                </h2>
            </div>
        </div>
    </div>

</nav>
