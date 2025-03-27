<li id="tag-group-{{ $group->id }}" class="group-item cursor-pointer pl-2 py-1 rounded" data-id="{{ $group->id }}">
    <div class="flex items-center justify-between">
        <span>
            @if ($group->icon)
            <i data-lucide="{{ $group->icon }}" class="me-1"></i>
            @endif
            {{ $group->name }}
        </span>
        <div class="space-x-1">
            <button class="edit-group-btn text-sm text-yellow-400 hover:text-yellow-500"
                data-id="{{ $group->id }}"
                data-name="{{ $group->name }}"
                data-slug="{{ $group->slug }}"
                data-purpose="{{ $group->purpose }}"
                data-color="{{ $group->color }}"
                data-icon="{{ $group->icon }}"
                data-description="{{ $group->description }}">
                <i data-lucide="edit"></i>
            </button>
            <button class="delete-group-btn text-sm text-red-400 hover:text-red-500"
                data-id="{{ $group->id }}">
                <i data-lucide="trash-2"></i>
            </button>
        </div>
    </div>

    <ul class="sortable">
        @if ($group->children->isNotEmpty())
        @foreach ($group->children as $child)
        <x-admin.tag-groups.group-item :group="$child" />
        @endforeach
        @endif
    </ul>
</li>