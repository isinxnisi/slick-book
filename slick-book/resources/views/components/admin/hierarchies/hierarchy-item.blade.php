<li id="hierarchy-{{ $hierarchy->id }}">
    <span class="title" data-id="{{ $hierarchy->id }}">{{ $hierarchy->title }}</span>

    <!-- 操作ボタン -->
    <button class="edit-btn" data-id="{{ $hierarchy->id }}">
        <i data-lucide="edit"></i>
    </button>
    <button class="add-btn" data-id="{{ $hierarchy->id }}">
        <i data-lucide="square-plus"></i>
    </button>
    <button class="delete-btn" data-id="{{ $hierarchy->id }}">
        <i data-lucide="trash-2"></i>
    </button>

    <ul class="sortable">
        @if ($hierarchy->children->isNotEmpty())
        @foreach ($hierarchy->children as $child)
        <x-admin.hierarchies.hierarchy-item :hierarchy="$child" />
        @endforeach
        @endif
    </ul>
</li>