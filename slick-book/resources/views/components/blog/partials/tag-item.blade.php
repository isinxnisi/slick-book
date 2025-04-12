@php
    $purposeStyles = config('tags.post_purpose_styles');
    $defaultStyle = $purposeStyles[$purpose];
@endphp
<li class="tag-item">
    <a class="tag" style="background-color: {{ $defaultStyle['bg'] }}; color: {{ $defaultStyle['text'] }}; border: 1px solid {{ $defaultStyle['bg'] }}">
        #{{ $tag->name }}
    </a>
</li>