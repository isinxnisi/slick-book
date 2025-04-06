@props([
    'groups' => collect(), // マスタタググループ（階層付き）
    'selectedTagIds' => [], // サイトでON状態のタグID
    'purpose' => null, // 現在表示中の用途
    'siteId' => null, // サイトID
])

<div class="mb-4">
    <div class="tag-selection-ui space-y-4">
        <div class="open-tab-container p-2">
        @foreach ($groups as $group)
            @if ($group->purpose !== $purpose) @continue @endif
            @if ($group->tags->count() == 0) @continue @endif
            <div class="flex items-center justify-between text-sm text-gray-400 dark:text-gray-500 mt-2">
                <div>{{ $group->breadcrumb }}</div>
                <button class="add-m-tag-inline-btn text-green-400 hover:text-green-500"
                        data-group-id="{{ $group->id }}"
                        data-group-name="{{ $group->breadcrumb }}">
                    <i data-lucide="square-plus" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="dark:bg-gray-900 flex flex-wrap gap-2 mt-0 ps-2 py-2 rounded">
            @foreach ($group->tags as $tag)
                @php
                    $isActive = in_array($tag->id, $selectedTagIds);
                @endphp
                <x-admin.ui.tag-item
                    :tag-id="$tag->id"
                    :tag-name="$tag->name"
                    :active="$isActive"
                    :editable="true"
                    :deletable="true"

                    :is-toggleable="true"
                    :tag-group-id="$group->id"
                    :site-id="$siteId"
                    :purpose="$tag->purpose"
                    :slug="$tag->slug"
                    :description="$tag->description"
                    :group-name="$group->breadcrumb"
                    />
            @endforeach

            </div>
        @endforeach
        </div>

        <!-- フォーム：タグ追加 -->
        <div id="inline-tag-form-container" class="hidden mt-4 border-t border-b py-2">
            <form id="inline-tag-form" class="space-y-3">
                <input type="hidden" name="tag_id" id="inline-tag-id">
                <input type="hidden" name="tag_group_id" id="inline-tag-group-id">
                <input type="hidden" name="purpose" id="inline-tag-purpose" value="{{ $purpose }}">
                <div>
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-gray-300">追加先グループ</label>
                        <button id="inline-tag-cancel" type="button"
                                class="text-gray-300 hover:text-white">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div id="inline-tag-group-name" class="mt-1 text-sm font-bold text-white"></div>
                </div>
                <div>
                    <label for="inline-tag-name">タグ名</label>
                    <input type="text" name="name" id="inline-tag-name"
                           class="form-control w-full dark:bg-gray-900 border border-gray-700 text-white"
                           placeholder="タグ名" required>
                </div>
                <div>
                    <label for="inline-tag-slug">スラッグ</label>
                    <input type="text" name="slug" id="inline-tag-slug"
                           class="form-control w-full dark:bg-gray-900 border border-gray-700 text-white"
                           placeholder="スラッグ (空欄可)">
                </div>
                <div>
                    <label for="inline-tag-description">説明</label>
                    <textarea name="description" id="inline-tag-description" rows="2"
                              class="form-control w-full dark:bg-gray-900 border border-gray-700 text-white"
                              placeholder="説明 (任意)"></textarea>
                </div>
                <div class="flex justify-end gap-2 mb-1">
                    <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">
                        保存
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
