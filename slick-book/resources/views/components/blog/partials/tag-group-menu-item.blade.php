<li>
    <a href="{{ route('blog.tagGroup', ['slug' => $group->slug]) }}"
        class="flex items-center ps-4 py-2 text-gray-600 hover:bg-gray-200
              {{ request()->routeIs('blog.tagGroup') && request()->slug === $group->slug ? 'bg-indigo-100 font-semibold border-r-4 border-indigo-300' : '' }}">
        <span class="lh-base me-2" style="max-width: calc(100% - 50px); text-overflow: ellipsis; overflow: hidden;">
            {{ $group->name }}
        </span>
        <span class="lh-base text-xs text-gray-500">({{ $group->post_count }})</span>
    </a>

    @if ($group->children && $group->children->count())
        <ul class="ml-4">
            @foreach ($group->children->sortBy('order') as $child)
                @include('components.blog.partials.tag-group-menu-item', ['group' => $child])
            @endforeach
        </ul>
    @endif
</li>
