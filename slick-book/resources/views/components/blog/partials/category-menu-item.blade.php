<li>
    <a href="{{ route('blog.category', ['slug' => $category->slug]) }}"
        class="flex items-center ps-4 py-2 text-gray-600 hover:bg-gray-200
              {{ request()->routeIs('blog.category') && request()->slug === $category->slug ? 'bg-indigo-100 font-semibold border-r-4 border-indigo-300' : '' }}">
        <span class="lh-base me-2" style="max-width: calc(100% - 50px); text-overflow: ellipsis; overflow: hidden;">
            {{ $category->title }}
        </span>
        <span class="lh-base text-xs text-gray-400">({{ $category->post_count }})</span>
    </a>

    @if ($category->children && $category->children->count())
        <ul class="ml-4">
            @foreach ($category->children->sortBy('order') as $child)
                @include('components.blog.partials.category-menu-item', ['category' => $child])
            @endforeach
        </ul>
    @endif
</li>
