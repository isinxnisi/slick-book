@props([
    'groups' => collect(),
    'selectedTagIds' => [],
    'purpose' => null,
    'siteId' => null,
])

<div class="tag-selection-ui space-y-4">
    @foreach ($groups as $group)
        @if ($group->purpose !== $purpose || $group->tags->isEmpty()) @continue @endif

        <div class="text-sm font-semibold text-gray-400 dark:text-gray-500 mb-2">
            {{ $group->breadcrumb }}
        </div>

        <div class="flex flex-wrap gap-2 ml-1 ps-2">
            @foreach ($group->tags as $tag)
                @php
                    $isChecked = in_array($tag->id, $selectedTagIds[$group->id] ?? []);
                @endphp
                <x-admin.ui.tag-checkbox-item
                    :tag-id="$tag->id"
                    :tag-name="$tag->name"
                    :checked="$isChecked"
                    :purpose="$purpose"
                />
            @endforeach
        </div>
    @endforeach
</div>
