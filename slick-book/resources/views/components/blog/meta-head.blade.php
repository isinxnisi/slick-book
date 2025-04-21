@props(['seo', 'site', 'title'])
@php
    $favicon = $site->images->firstWhere('type', 'favicon');
    $ogpImage = $site->images->firstWhere('type', 'ogp');
@endphp
@if ($favicon)
    <link rel="icon" type="image/png" href="{{ route('secure.media', ['site' => $site->id, 'path' => 'favicon/' . basename($favicon->path)]) }}">
@endif
    <meta name="description" content="{{ $seo['meta_description'] ?? $site->description }}">
    <meta name="keywords" content="{{ $seo['meta_keywords'] ?? '' }}">
    <link rel="canonical" href="{{ $seo['canonical_url'] ?? url()->current() }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $seo['meta_description'] ?? $site->description }}">
@if ($ogpImage)
    <meta property="og:image" content="{{ route('secure.media', ['site' => $site->id, 'path' => 'ogp/' . basename($ogpImage->path)]) }}">
@endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="{{ $seo['twitter_card_type'] ?? 'summary' }}">
    {!! $seo['custom_head_tags'] ?? '' !!}
