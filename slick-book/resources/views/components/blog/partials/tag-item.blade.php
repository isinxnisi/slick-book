@props([
    'link' => false,
])
@php
    $purposeStyles = config('tags.post_purpose_styles');
    if (!isset($purposeStyles[$tag->purpose])) {
        $first = array_keys($purposeStyles)[0];
        $defaultStyle = $purposeStyles[$first];
    } else {
        $defaultStyle = $purposeStyles[$tag->purpose];
    }
@endphp
<li class="tag-item">
    @if ($link)
    <a href="{{ route('blog.tag', ['slug' => $tag->slug]) }}"
        class="tag" style="background-color: {{ $defaultStyle['bg'] }}; color: {{ $defaultStyle['text'] }}; border: 1px solid {{ $defaultStyle['bg'] }}">
        #{{ $tag->name }}
    </a>
    @else
    <a class="tag" style="background-color: {{ $defaultStyle['bg'] }}; color: {{ $defaultStyle['text'] }}; border: 1px solid {{ $defaultStyle['bg'] }}">
        #{{ $tag->name }}
    </a>
    @endif
</li>
