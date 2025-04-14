<li id="category-{{ $category->id }}" class="pl-2">
    <div class="flex items-center justify-between">
        <!-- 左寄せグループ：タイトル＋編集＋追加 -->
        <div class="space-x-1">
            @if ($category->icon)
                <span class="inline-block w-4 h-3 me-0 px-0"><i class="ms-0" data-lucide="{{ $category->icon }}"></i></span>
            @endif
            <span class="title">{{ $category->title }}</span>
            @if ($category->color)
                <span class="inline-block w-3 h-3 rounded-full" style="background-color: {{ $category->color }}"></span>
            @endif

            <button class="edit-btn text-sm text-yellow-400 hover:text-yellow-500"
                data-id="{{ $category->id }}"
                data-title="{{ $category->title }}"
                data-slug="{{ $category->slug }}"
                data-description="{{ $category->description }}"
                data-icon="{{ $category->icon }}"
                data-color="{{ $category->color }}"
                data-is_visible="{{ $category->is_visible }}"
                data-image_path="{{ $category->image_path }}"
                data-site_id="{{ $category->site_id }}">
                <i data-lucide="edit"></i>
            </button>
            <button class="add-btn text-sm text-green-400 hover:text-green-500" data-id="{{ $category->id }}">
                <i data-lucide="square-plus"></i>
            </button>
        </div>
        <div class="flex items-center justify-between w-auto">

            <!-- 右寄せ：削除ボタン -->
            <div class="ml-4">
                <button class="delete-btn text-sm text-red-400 hover:text-red-500" data-id="{{ $category->id }}">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>
    </div>

    <ul class="sortable ml-4">
        @foreach ($category->children as $child)
            <x-admin.categories.category-item :category="$child" />
        @endforeach
    </ul>
</li>
