<li id="tag-group-{{ $group->id }}" class="group-item cursor-pointer pl-2 py-1 rounded" data-id="{{ $group->id }}">
    <div class="flex items-center justify-between">
        <div class="space-x-1">
            <span>
                @if ($group->icon)
                <span class="inline-block w-4 h-3 me-0 px-0"><i class="ms-0" data-lucide="{{ $group->icon }}"></i></span>
                @endif
                <span class="title ms-1">{{ $group->name }}</span>
                @if ($group->color)
                    <span class="inline-block ms-1 w-3 h-3 rounded-full" style="background-color: {{ $group->color }}"></span>
                @endif
            </span>
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
            <button class="add-btn text-sm text-green-400 hover:text-green-500" data-id="{{ $group->id }}">
                <i data-lucide="square-plus"></i>
            </button>
            <button class="delete-group-btn text-sm text-red-400 hover:text-red-500"
                data-tag-id="{{ $group->id }}">
                <i data-lucide="trash-2"></i>
            </button>
        </div>
        <div class="flex items-center justify-between w-auto">
            <!-- タグ追加ボタン -->
            <button class="add-tag-btn text-sm text-green-400 hover:text-green-500 mt-2 ml-4" data-group-id="{{ $group->id }}">
                <i class="inline-block text-green-400 me-2" data-lucide="square-plus"></i><span>タグ追加</span>
            </button>
        </div>
    </div>

    <ul class="sortable-tags dark:bg-gray-900 flex flex-wrap gap-2 ml-2 mt-2 ps-2 rounded" data-group-id="{{ $group->id }}">
        @if ($group->tags->isNotEmpty())
        @foreach ($group->tags as $tag)
        @php
            $purpose = $tag->purpose ?? 'public';
            $styleSet = config('tags.purpose_styles')[$purpose] ?? [
                'bg' => '#4f46e5',
                'text' => '#fff',
                'border' => '#4f46e5',
            ];
        @endphp

        <li class="flex items-center space-x-1 tag-item active ps-2 pe-2 py-1 rounded-full text-sm ui-sortable-handle"
            data-id="{{ $tag->id }}"
            style="background-color: {{ $styleSet['bg'] }}; color: {{ $styleSet['text'] }}; border: 1px solid {{ $styleSet['border'] }};">
            <span>{{ $tag->name }}</span>
            <button class="edit-tag-btn hover:text-yellow-400"
                data-id="{{ $tag->id }}"
                data-name="{{ $tag->name }}"
                data-slug="{{ $tag->slug }}"
                data-description="{{ $tag->description }}">
                <i data-lucide="pencil" class="w-4 h-4"></i>
            </button>
            <button class="delete-tag-btn hover:text-red-400"
                data-id="{{ $tag->id }}">
                <i data-lucide="trash" class="w-4 h-4"></i>
            </button>
        </li>
        @endforeach
        @endif
    </ul>

    {{-- 子グループ --}}
    <ul class="sortable ml-4">
        @if ($group->children->isNotEmpty())
        @foreach ($group->children as $child)
        <x-admin.tag-groups.group-item-with-tags :group="$child" />
        @endforeach
        @endif
    </ul>
</li>
