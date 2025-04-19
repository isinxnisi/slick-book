@props([
    'section',
    'slotNo' => 1,
    'marginClass' => 'mb-4',
])

@php
    $bannerPc = $bannerComponent->getBanner('pc_tablet', $section, $slotNo);
    $bannerMb = $bannerComponent->getBanner('mobile', $section, $slotNo);
@endphp

@if (!empty($bannerPc?->html) || !empty($bannerMb?->html))
<div id="banner-{{ $section }}-slot-{{ $slotNo }}" class="banner-item {{ $section }} p-0 m-0">
    @if (!empty($bannerPc?->html))
        <div class="pc-tab-banner-slot container text-center justify-content-lg-center {{ $marginClass }} p-0">
            {!! $bannerPc->html !!}
        </div>
    @endif

    @if (!empty($bannerMb?->html))
        <div class="mb-banner-slot container justify-content-md-center {{ $marginClass }} p-0">
            {!! $bannerMb->html !!}
        </div>
    @endif
</div>
@endif
