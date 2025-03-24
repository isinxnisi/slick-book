@section('title', 'タグ管理マスタ')
@php
    $icons = config('icons.list');
    $defaultIcon = config('icons.default', 'folder');
@endphp
<x-app-layout>
    <x-slot name="header">
        <x-admin.tags.purpose-tabs-link :active-purpose="$purpose" />
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-[3fr_1.2fr] gap-4">
        <!-- タググループ階層ツリー -->
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <!-- 階層ツリー -->
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="hierarchy-tree p-4 text-gray-900 dark:text-gray-100">
                    <ul class="tree sortable" id="tag-group-list">
                        @foreach($groups as $group)
                        <x-admin.tag-groups.group-item-with-tags :group="$group" />
                        @endforeach
                    </ul>
                    <!-- 新規追加ボタン -->
                    <div class="mt-4 mb-4">
                        <button id="add-group-btn" class="bg-indigo-700 text-white px-4 py-2 rounded hover:bg-indigo-800">
                            ＋ タググループを追加
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-right-panel dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <!-- タグ詳細編集パネル -->
            <div class="panel-content p-4 text-gray-900 dark:text-gray-100" id="tag-edit-panel" style="display: none;">
                <h2 class="text-lg font-bold mb-2">タグ<span class="mode-text"></span></h2>
                <form id="tag-edit-form">
                    <input type="hidden" id="editPanel-tag-id">
                    <input type="hidden" id="editPanel-tag-group-id">

                    <div class="mb-2">
                        <label for="editPanel-tag-name">タグ名</label>
                        <input type="text" id="editPanel-tag-name" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-tag-slug">スラッグ</label>
                        <input type="text" id="editPanel-tag-slug" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-tag-description">説明</label>
                        <textarea id="editPanel-tag-description" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700"></textarea>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" id="tag-panel-cancel" class="bg-gray-600 text-white px-4 py-2 rounded">キャンセル</button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">保存</button>
                    </div>
                </form>
            </div>
            <!-- タググループ編集パネル（右側） -->
            <div class="panel-content p-4 text-gray-900 dark:text-gray-100" id="group-edit-panel" style="display: none;">
                <h2 class="text-lg font-bold mb-2">タググループ<span class="mode-text"></span></h2>
                <form id="group-edit-form">
                    <input type="hidden" id="editPanel-group-id">
                    <input type="hidden" id="editPanel-group-parent_id">
                    <input type="hidden" id="editPanel-purpose-hidden" name="purpose" value="{{ $purpose }}">

                    <div class="mb-2">
                        <label for="editPanel-group-name">表示名</label>
                        <input type="text" id="editPanel-group-name" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-group-slug">スラッグ</label>
                        <input type="text" id="editPanel-group-slug" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-group-color">色コード</label>
                        <input type="color" id="editPanel-group-color" class="form-control form-control-color w-100" title="色を選択">
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-group-icon">アイコン</label>
                        <select id="editPanel-group-icon"
                                class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                            @foreach($icons as $key => $label)
                                <option value="{{ $key }}"
                                    class="dark:text-white dark:bg-gray-800"
                                    @if(old('icon', $group->icon ?? '') == $key) selected @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-group-description">説明</label>
                        <textarea id="editPanel-group-description" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700"></textarea>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" id="group-panel-cancel" class="bg-gray-600 text-white px-4 py-2 rounded">キャンセル</button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(function() {
            $('.sortable').sortable({
                connectWith: '.sortable',
                placeholder: 'ui-state-highlight',
                cursor: 'move',
                update: function() {
                    const hierarchy = buildHierarchy($('#tag-group-list'));
                    $.post("{{ route('tag-groups.reorder') }}", {
                        _token: '{{ csrf_token() }}',
                        hierarchy: hierarchy
                    });
                }
            }).disableSelection();

            $('.sortable-tags').sortable({
                connectWith: '.sortable-tags',
                placeholder: 'ui-state-highlight',
                cursor: 'move',
                update: function(event, ui) {
                    const $list = $(this);
                    const groupId = $list.data('group-id');
                    const tagIds = $list.children('li').map(function() {
                        return $(this).data('id');
                    }).get();

                    $.post('/tags/reorder', {
                        _token: '{{ csrf_token() }}',
                        tag_group_id: groupId,
                        tags: tagIds
                    });
                }
            }).disableSelection();

        });

        function buildHierarchy($list) {
            const items = [];
            $list.children('li').each(function() {
                const rawId = $(this).attr('id') || '';
                const id = parseInt(rawId.replace('tag-group-', ''));

                if (!id || isNaN(id)) return; // ← ここで不正なIDをスキップ

                const children = buildHierarchy($(this).children('ul'));
                items.push({
                    id: id,
                    children
                });
            });
            return items;
        }

        // タググループ作成
        $('#add-group-btn').on('click', function() {
            $('#editPanel-purpose-hidden').val('{{ $purpose }}');

            $('#editPanel-group-id').val('');
            $('#editPanel-group-name').val('');
            $('#editPanel-group-slug').val('');
            $('#editPanel-group-color').val('');
            $('#editPanel-group-icon').val('{{ $defaultIcon }}');
            $('#editPanel-group-description').val('');
            $('#editPanel-group-parent_id').val('');
            $('.ui-right-panel').find('.panel-content').hide();
            $('#group-edit-panel').find('.mode-text').text('追加');
            $('#group-edit-panel').slideDown(100);
        });

        // タググループ子階層作成
        $(document).on('click', '.add-btn', function() {
            const parentId = $(this).data('id');
            $('#editPanel-purpose-hidden').val('{{ $purpose }}');
            
            $('#editPanel-group-id').val('');
            $('#editPanel-group-name').val($(this).data('name'));
            $('#editPanel-group-slug').val($(this).data('slug'));
            $('#editPanel-group-color').val($(this).data('color'));
            $('#editPanel-group-icon').val($(this).attr('data-icon') || '{{ $defaultIcon }}');
            $('#editPanel-group-description').val($(this).data('description'));
            $('#editPanel-group-parent_id').val(parentId);
            $('.ui-right-panel').find('.panel-content').hide();
            $('#group-edit-panel').find('.mode-text').text('追加');
            $('#group-edit-panel').slideDown(100);
        });

        // タググループ編集ボタン
        $(document).on('click', '.edit-group-btn', function() {
            const groupId = $(this).data('id');
            $('#editPanel-purpose-hidden').val($(this).data('purpose'));

            $('#editPanel-group-id').val(groupId);
            $('#editPanel-group-name').val($(this).data('name'));
            $('#editPanel-group-slug').val($(this).data('slug'));
            $('#editPanel-group-color').val($(this).data('color'));
            $('#editPanel-group-icon').val($(this).attr('data-icon') || '{{ $defaultIcon }}');
            $('#editPanel-group-description').val($(this).data('description'));
            $('.ui-right-panel').find('.panel-content').hide();
            $('#group-edit-panel').find('.mode-text').text('編集');
            $('#group-edit-panel').slideDown(100);
        });

        // タグ追加ボタン
        $(document).on('click', '.add-tag-btn', function() {
            const groupId = $(this).data('group-id');

            $('#editPanel-tag-id').val('');
            $('#editPanel-tag-name').val('');
            $('#editPanel-tag-slug').val('');
            $('#editPanel-tag-description').val('');
            $('#editPanel-tag-group-id').val(groupId);

            $('.ui-right-panel').find('.panel-content').hide();
            $('#tag-edit-panel').find('.mode-text').text('追加');
            $('#tag-edit-panel').slideDown(100);
        });

        // タグ編集ボタン
        $(document).on('click', '.edit-tag-btn', function() {
            const tagId = $(this).data('id');
            const tagName = $(this).data('name');
            const tagSlug = $(this).data('slug');
            const tagDescription = $(this).data('description');
            const groupId = $(this).closest('.sortable-tags').data('group-id');

            $('#editPanel-tag-id').val(tagId);
            $('#editPanel-tag-name').val(tagName);
            $('#editPanel-tag-slug').val(tagSlug);
            $('#editPanel-tag-description').val(tagDescription);
            $('#editPanel-tag-group-id').val(groupId);

            $('.ui-right-panel').find('.panel-content').hide();
            $('#tag-edit-panel').find('.mode-text').text('編集');
            $('#tag-edit-panel').slideDown(100);
        });

        // 保存
        $('#tag-edit-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#editPanel-tag-id').val();
            const groupId = $('#editPanel-tag-group-id').val();
            const data = {
                name: $('#editPanel-tag-name').val(),
                slug: $('#editPanel-tag-slug').val(),
                description: $('#editPanel-tag-description').val(),
                _token: '{{ csrf_token() }}'
            };

            if (!id) {
                // 追加
                data.tag_group_id = groupId;
                $.post('/tags', data, () => location.reload());
            } else {
                // 編集
                $.ajax({
                    url: `/tags/${id}`,
                    method: 'PATCH',
                    data: data,
                    success: () => location.reload()
                });
            }
        });

        // タググループ編集パネル保存
        $('#group-edit-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#editPanel-group-id').val();
            const data = {
                name: $('#editPanel-group-name').val(),
                slug: $('#editPanel-group-slug').val(),
                purpose: $('#editPanel-purpose-hidden').val(),
                color: $('#editPanel-group-color').val(),
                icon: $('#editPanel-group-icon').val(),
                description: $('#editPanel-group-description').val(),
                parent_id: $('#editPanel-group-parent_id').val(),
                _token: '{{ csrf_token() }}'
            };

            if (!id) {
                $.post('/tag-groups', data, () => location.reload());

            } else {
                $.ajax({
                    url: `/tag-groups/${id}`,
                    method: 'PATCH',
                    data: data,
                    success: () => location.reload()
                });
            }
        });

        // キャンセル → パネル非表示
        $('#group-panel-cancel').on('click', function() {
            $('#group-edit-panel').slideUp();
        });

        // キャンセル → パネル非表示
        $('#tag-panel-cancel').on('click', function() {
            $('#tag-edit-panel').slideUp();
        });

        // タグ削除
        $(document).on('click', '.delete-tag-btn', function () {
            if (!confirm('このタグを削除してもよろしいですか？')) return;

            const tagId = $(this).data('id');
            $.ajax({
                url: `/tags/${tagId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: () => location.reload(),
                error: (xhr) => alert('削除に失敗しました')
            });
        });

        // タググループ削除
        $(document).on('click', '.delete-group-btn', function () {
            if (!confirm('このタググループを削除してもよろしいですか？')) return;

            const groupId = $(this).data('id');
            $.ajax({
                url: `/tag-groups/${groupId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: () => location.reload(),
                error: (xhr) => alert('削除に失敗しました')
            });
        });
    </script>
    @endpush
</x-app-layout>