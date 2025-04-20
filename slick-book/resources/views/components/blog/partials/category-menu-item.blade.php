<li>
    <a href="{{ route('blog.category', ['slug' => $category->slug]) }}"
       class="block ps-4 py-2 text-gray-600 hover:bg-gray-200
              {{ request()->routeIs('blog.category') && request()->slug === $category->slug ? 'bg-indigo-100 font-semibold border-r-4 border-indigo-300' : '' }}">
        {{ mb_strimwidth($category->title, 0, 20, '…', 'UTF-8') }} <span class="text-xs text-gray-500">({{ $category->post_count }})</span>
    </a>

    @if ($category->children && $category->children->count())
        <ul class="ml-4">
            @foreach ($category->children->sortBy('order') as $child)
                @include('components.blog.partials.category-menu-item', ['category' => $child])
            @endforeach
        </ul>
    @endif
</li>
