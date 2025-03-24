@section('title', 'サイトタグ管理')
<x-app-layout>
    <x-slot name="header">
        <!-- サイト切替タブ -->
        <x-admin.ui.site-tabs :sites="$sites" :active-id="$siteId" />
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-[3fr_2.0fr] gap-4">
        <!-- タググループ階層ツリー -->
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="hierarchy-tree p-4 text-gray-900 dark:text-gray-100">
                    <ul class="tree sortable" id="tag-group-list">
                        @foreach($groups as $group)
                        <x-admin.site-tag-groups.group-item-with-tags :group="$group" />
                        @endforeach
                    </ul>
                    <div class="mt-4 mb-4">
                        <button id="add-group-btn" class="bg-indigo-700 text-white px-4 py-2 rounded hover:bg-indigo-800">
                            ＋ タググループを追加
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-right-panel dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="panel-content p-4 text-gray-900 dark:text-gray-100" id="tag-edit-panel" style="display: none;">

                <!-- タグ用途切り替えタブ -->
                <x-admin.tags.purpose-tabs :active-purpose="$purpose" />

                <!-- マスタ：タググループ階層ツリー -->
                <div id="tag-selection-body" class="hierarchy-tree mt-3 p-0 text-gray-900 dark:text-gray-100">
                    <x-admin.tags.tag-selection
                        :groups="$mastaGroups"
                        :selected-tag-ids="$selectedTagIds"
                        :purpose="$purpose"
                        :site-id="$siteId"
                        />
                    <ul class="tree" id="tag-group-list">
                        @foreach($mastaGroups as $group)
                        @endforeach
                    </ul>
                </div>
                <div class="mt-4 flex justify-start space-x-2">
                    <button type="button" id="tag-panel-cancel" class="bg-gray-600 text-white px-4 py-2 rounded">キャンセル</button>
                </div>
            </div>

            <div class="panel-content p-4 text-gray-900 dark:text-gray-100" id="group-edit-panel" style="display: none;">
                <h2 class="text-lg font-bold mb-2">タググループ<span class="mode-text"></span></h2>
                <form id="group-edit-form">
                    <input type="hidden" id="editPanel-group-id">
                    <input type="hidden" id="editPanel-group-parent_id">
                    <input type="hidden" id="editPanel-site-id" value="{{ $siteId }}">

                    <div class="mb-2">
                        <label for="editPanel-group-name">表示名</label>
                        <input type="text" id="editPanel-group-name" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-group-slug">スラッグ</label>
                        <input type="text" id="editPanel-group-slug" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-group-purpose">用途</label>
                        <input type="text" id="editPanel-group-purpose" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-group-color">色コード</label>
                        <input type="color" id="editPanel-group-color" class="form-control form-control-color w-100" title="色を選択">
                    </div>
                    <div class="mb-2">
                        <label for="editPanel-group-icon">アイコン</label>
                        <input type="text" id="editPanel-group-icon" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700" placeholder="例: folder">
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
        // グローバルに定義
        window.purposeStyles = @json(config('tags.purpose_styles'));
        window.currentSelectedPurpose = '{{ $purpose }}';
        window.currentSelectedGroupId = null;

        $(function() {
            bindRightUIEvents();

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
                    const groupId = $list.attr('data-group-id');
                    const tagIds = $list.children('li').map(function() {
                        return $(this).attr('data-id');
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
            $('#editPanel-group-id').val('');
            $('#editPanel-group-name').val('');
            $('#editPanel-group-slug').val('');
            $('#editPanel-group-purpose').val('');
            $('#editPanel-group-color').val('');
            $('#editPanel-group-icon').val('');
            $('#editPanel-group-description').val('');
            $('#editPanel-group-parent_id').val('');
            $('.ui-right-panel').find('.panel-content').hide();
            $('#group-edit-panel').find('.mode-text').text('追加');
            $('#group-edit-panel').slideDown(100);
        });

        // タググループ子階層作成
        $(document).on('click', '.add-btn', function() {
            const parentId = $(this).attr('data-id');
            const groupId = $(this).attr('data-id');
            // $('#editPanel-group-id').val(groupId);
            $('#editPanel-group-name').val($(this).attr('data-name'));
            $('#editPanel-group-slug').val($(this).attr('data-slug'));
            $('#editPanel-group-purpose').val($(this).attr('data-purpose'));
            $('#editPanel-group-color').val($(this).attr('data-color'));
            $('#editPanel-group-icon').val($(this).attr('data-icon'));
            $('#editPanel-group-description').val($(this).attr('data-description'));
            $('#editPanel-group-parent_id').val(parentId);
            $('.ui-right-panel').find('.panel-content').hide();
            $('#group-edit-panel').find('.mode-text').text('追加');
            $('#group-edit-panel').slideDown(100);
        });

        // タググループ編集ボタン
        $(document).on('click', '.edit-group-btn', function() {
            const groupId = $(this).attr('data-id');
            $('#editPanel-group-id').val(groupId);
            $('#editPanel-group-name').val($(this).attr('data-name'));
            $('#editPanel-group-slug').val($(this).attr('data-slug'));
            $('#editPanel-group-purpose').val($(this).attr('data-purpose'));
            $('#editPanel-group-color').val($(this).attr('data-color'));
            $('#editPanel-group-icon').val($(this).attr('data-icon'));
            $('#editPanel-group-description').val($(this).attr('data-description'));
            $('.ui-right-panel').find('.panel-content').hide();
            $('#group-edit-panel').find('.mode-text').text('編集');
            $('#group-edit-panel').slideDown(100);
        });

        // タグ追加ボタン
        $(document).on('click', '.add-tag-btn', function() {
            const groupId = $(this).attr('data-group-id');
            window.currentSelectedGroupId = groupId;

            applySelectedTagsToRightPanel(groupId);

            $('.ui-right-panel').find('.panel-content').hide();
            $('#tag-edit-panel').find('.mode-text').text('追加');
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
                site_id: $('#editPanel-site-id').val(),
                name: $('#editPanel-group-name').val(),
                slug: $('#editPanel-group-slug').val(),
                purpose: $('#editPanel-group-purpose').val(),
                color: $('#editPanel-group-color').val(),
                icon: $('#editPanel-group-icon').val(),
                description: $('#editPanel-group-description').val(),
                parent_id: $('#editPanel-group-parent_id').val(),
                _token: '{{ csrf_token() }}'
            };

            if (!id) {
                $.post('/site-tag-groups', data, () => location.reload());

            } else {
                $.ajax({
                    url: `/site-tag-groups/${id}`,
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
            const tagId = $(this).attr('data-id');
            const groupId = $(this).closest('.sortable-tags').attr('data-group-id'); // ULに group-id がある前提

            $.ajax({
                url: '/tag-tag-groups/unlink',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tag_id: tagId,
                    tag_group_id: groupId
                },
                success: () => {
                    // 左UIからタグを削除
                    $(this).closest('li').remove();
                    // 右UIからタグを削除
                    applySelectedTagsToRightPanel(window.currentSelectedGroupId);
                },
                error: () => alert('削除に失敗しました')
            });
        });

        // タググループ削除
        $(document).on('click', '.delete-group-btn', function () {
            if (!confirm('このタググループを削除してもよろしいですか？')) return;

            const groupId = $(this).attr('data-id');
            $.ajax({
                url: `/site-tag-groups/${groupId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: () => location.reload(),
                error: (xhr) => alert('削除に失敗しました')
            });
        });

        function applySelectedTagsToRightPanel(groupId) {
            const selectedTagIds = getCurrentTagIdsFromLeftUI(groupId);

            $('.tag-toggle-btn').each(function () {
                const $btn = $(this);
                const tagId = parseInt($btn.attr('data-tag-id'));
                const purpose = $btn.attr('data-purpose') || 'public';
                const style = window.purposeStyles?.[purpose] || { bg: '#888', text: '#fff' };

                $btn.attr('data-tag-group-id', groupId);

                if (selectedTagIds.includes(tagId)) {
                    $btn.parent('.tag-item').addClass('active').css({
                        backgroundColor: style.bg,
                        color: style.text,
                        borderColor: style.bg
                    });
                } else {
                    $btn.parent('.tag-item').removeClass('active').css({
                        backgroundColor: 'transparent',
                        color: '#fff',
                        borderColor: '#fff'
                    });
                }
            });
        }

        function getCurrentTagIdsFromLeftUI(groupId) {
            const $groupUl = $(`#tags-of-group-${groupId}`);
            const tagIds = [];
            $groupUl.find('li').each(function () {
                const tagId = parseInt($(this).attr('data-id'));
                if (!isNaN(tagId)) {
                    tagIds.push(tagId);
                }
            });
            return tagIds;
        }

        $(document).on('click', '.purpose-tab', function () {
            const selectedPurpose = $(this).attr('data-purpose');
            const style = window.purposeStyles?.[selectedPurpose] || { bg: '#888', text: '#fff' };

            // URLパラメータから site を維持
            const params = new URLSearchParams(window.location.search);
            const siteId = params.get('site');

            // Ajaxで右UIを更新（目的に応じたマスタタグ一覧を取得）
            $.get('/site-tag-groups/master-tags', {
                site: siteId,
                purpose: selectedPurpose
            }, function (html) {
                // 選択中の用途を更新
                window.currentSelectedPurpose = selectedPurpose;

                $('#tag-edit-panel .hierarchy-tree').html(html);
                // タブの表示状態を更新
                // タブ見た目更新
                $('.purpose-tab').removeClass('text-white border-indigo-500')
                    .addClass('text-gray-400 hover:text-white hover:border-b-2 hover:border-gray-500')
                    .css('border-color', 'transparent');

                $(`.purpose-tab[data-purpose="${selectedPurpose}"]`)
                    .addClass('text-white border-b-2')
                    .removeClass('text-gray-400')
                    .css('border-color', style.bg ?? "#4f46e5");

                applySelectedTagsToRightPanel(window.currentSelectedGroupId);
                window.refreshLucideAndBindEvents();
            });
        });

        function bindRightUIEvents() {
            // イベント重複バインド防止
            $(document).off('click', '.add-m-tag-inline-btn');
            $(document).off('click', '.edit-m-tag-btn');
            $(document).off('click', '.delete-m-tag-btn');
            $('#inline-tag-cancel').off('click');
            $('#inline-tag-form').off('submit');

            // マスタ：タグ追加
            $(document).on('click', '.add-m-tag-inline-btn', function () {
                const groupId = $(this).data('group-id');
                const groupName = $(this).data('group-name');
            
                $('#inline-tag-id').val('');
                $('#inline-tag-group-id').val(groupId);
                $('#inline-tag-group-name').text(groupName);
                $('#inline-tag-name').val('');
                $('#inline-tag-slug').val('');
                $('#inline-tag-description').val('');
            
                $('#inline-tag-form-container').hide().removeClass('hidden').slideDown(200);
            });

            // マスタ：タグ編集ボタン
            $(document).on('click', '.edit-m-tag-btn', function () {
                const tagId = $(this).attr('data-id');
                const groupId = $(this).data('group-id');
                const groupName = $(this).data('group-name');
                const tagName = $(this).attr('data-name');
                const tagSlug = $(this).attr('data-slug');
                const tagDescription = $(this).attr('data-description');
                // const groupId = $(this).closest('.tag-selection-ui').find('input[name=tag_group_id]').val();

                $('#inline-tag-id').val(tagId);
                $('#inline-tag-group-id').val(groupId);
                $('#inline-tag-group-name').text(groupName);
                $('#inline-tag-name').val(tagName);
                $('#inline-tag-slug').val(tagSlug);
                $('#inline-tag-description').val(tagDescription);

                $('#inline-tag-form-container').hide().removeClass('hidden').slideDown(200);
            });

            // マスタ：タグ削除ボタン
            $(document).on('click', '.delete-m-tag-btn', function () {
                const tagId = $(this).attr('data-id');

                if (!confirm('このタグを削除してもよろしいですか？')) return;

                $.ajax({
                    url: `/tags/${tagId}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function () {
                        // 再描画してUI更新
                        refreshRightTagUI();
                        // 左UIを更新
                        refreshLeftTagUI();
                    },
                    error: function () {
                        alert('削除に失敗しました');
                    }
                });
            });

            $('#inline-tag-cancel').on('click', function () {
                $('#inline-tag-form-container').slideUp(200);
            });

            // マスタ：タグ保存
            $('#inline-tag-form').on('submit', function (e) {
                e.preventDefault();
                const tagId = $('#inline-tag-id').val();
                const data = {
                    tag_id: tagId,
                    tag_group_id: $('#inline-tag-group-id').val(),
                    name: $('#inline-tag-name').val(),
                    slug: $('#inline-tag-slug').val(),
                    description: $('#inline-tag-description').val(),
                    _token: '{{ csrf_token() }}'
                };
    
                if (!tagId) {
                    $.post('/tags', data, function (res) {
                        $('#inline-tag-form-container').slideUp(0);
                        const params = new URLSearchParams(window.location.search);
                        const siteId = params.get('site');
        
                        // 右UIのタグ一覧を再取得
                        refreshRightTagUI();
                    }).fail(function () {
                        alert('保存に失敗しました');
                    });
                } else {
                    // 編集
                    $.ajax({
                        url: `/tags/${tagId}`,
                        method: 'PATCH',
                        data: data,
                        success: () => {
                            $('#inline-tag-form-container').slideUp(0);
                            const params = new URLSearchParams(window.location.search);
                            const siteId = params.get('site');
            
                            // 右UIのタグ一覧を再取得
                            refreshRightTagUI();

                            // 左UIを更新
                            refreshLeftTagUI();
                        }
                    });
                    
                }
            });
        }

        function refreshRightTagUI() {
            const params = new URLSearchParams(window.location.search);
            const siteId = params.get('site');

            $.get('/site-tag-groups/master-tags', {
                site: siteId,
                purpose: window.currentSelectedPurpose
            }, function (html) {
                $('#tag-selection-body').html(html);
                applySelectedTagsToRightPanel(window.currentSelectedGroupId);
                window.refreshLucideAndBindEvents();
            });
        }

    </script>
    @endpush
</x-app-layout>