@section('title', 'タググループ管理')
<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <!-- 新規追加ボタン -->
    <div class="mb-4">
        <button class="bg-indigo-700 text-white px-4 py-2 rounded hover:bg-indigo-800" data-bs-toggle="modal" data-bs-target="#createTagGroupModal">
            ＋ タググループを追加
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- タググループ階層ツリー -->
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="hierarchy-tree p-4 text-gray-900 dark:text-gray-100">
                <ul class="tree sortable" id="tag-group-list">
                    @foreach($groups as $group)
                    <x-admin.tag-groups.group-item :group="$group" />
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- マスタタグ表示・追加エリア -->
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 text-gray-900 dark:text-gray-100">
                <h2 class="text-lg font-bold mb-2">タグ一覧（選択グループ）</h2>
                <div id="tag-list">
                    <!-- JSで選択中のタググループに応じて読み込み -->
                    <p class="text-sm text-gray-400">左のグループを選択してください</p>
                </div>
                <div class="mt-4">
                    <button id="add-tag-btn" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800" disabled>
                        ＋ タグを追加
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- タググループ編集モーダル -->
    <x-admin.tag-groups.edit-modal
        id="editTagGroupModal"
        title="タググループ編集"
        :fields="[
            'name' => '表示名',
            'slug' => 'スラッグ',
            'purpose' => '用途',
            'color' => '色コード',
            'icon' => 'アイコン',
            'description' => '説明',
        ]"
        saveButtonId="save-tag-group-btn" />

    <!-- タグ編集モーダル -->
    <x-admin.categories.edit-modal
        id="editTagModal"
        title="タグ編集"
        :fields="[
            'name' => 'タグ名',
            'slug' => 'スラッグ',
            'description' => '説明'
        ]"
        saveButtonId="save-tag-btn" />

    <!-- タググループ作成モーダル -->
    <x-admin.tag-groups.create-modal
        id="createTagGroupModal"
        title="タググループ追加"
        :fields="[
            'name' => '表示名',
            'slug' => 'スラッグ',
            'purpose' => '用途',
            'color' => '色コード',
            'icon' => 'アイコン',
            'description' => '説明',
        ]"
        saveButtonId="create-tag-group-btn" />

</x-app-layout>
<script>
    $(function() {
        $('.sortable').sortable({
            connectWith: '.sortable',
            placeholder: 'ui-state-highlight',
            cursor: 'move',
            update: function() {
                const hierarchyData = buildHierarchy($('#tag-group-list'));
                $.post("{{ route('tag-groups.reorder') }}", {
                    _token: '{{ csrf_token() }}',
                    hierarchy: hierarchyData
                });
            }
        }).disableSelection();
        // 編集ボタンを押したときの処理
        $(document).on('click', '.edit-group-btn', function() {
            const fields = ['id', 'name', 'slug', 'purpose', 'color', 'icon', 'description'];
            const modalId = 'editTagGroupModal';

            fields.forEach(field => {
                $(`#${modalId}-${field}`).val($(this).data(field));
            });

            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        });

        // 保存ボタンを押したときの処理
        $('#save-tag-group-btn').on('click', function() {
            const modalId = 'editTagGroupModal';
            const id = $(`#${modalId}-id`).val();
            const data = {
                name: $(`#${modalId}-name`).val(),
                slug: $(`#${modalId}-slug`).val(),
                purpose: $(`#${modalId}-purpose`).val(),
                color: $(`#${modalId}-color`).val(),
                icon: $(`#${modalId}-icon`).val(),
                description: $(`#${modalId}-description`).val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: `/tag-groups/${id}`,
                method: 'PATCH',
                data: data,
                success: function() {
                    location.reload(); // 更新後リロード
                },
                error: function(xhr) {
                    alert('エラーが発生しました。');
                    console.error(xhr.responseText);
                }
            });
        });

        $('#create-tag-group-btn').on('click', function() {
            const modalId = 'createTagGroupModal';
            const data = {
                name: $(`#${modalId}-name`).val(),
                slug: $(`#${modalId}-slug`).val(),
                purpose: $(`#${modalId}-purpose`).val(),
                color: $(`#${modalId}-color`).val(),
                icon: $(`#${modalId}-icon`).val(),
                description: $(`#${modalId}-description`).val(),
                _token: '{{ csrf_token() }}'
            };

            $.post('{{ route("tag-groups.store") }}', data, function() {
                location.reload(); // 成功したらリロード
            }).fail(function(xhr) {
                alert('エラーが発生しました。');
                console.error(xhr.responseText);
            });
        });


        function buildHierarchy($list) {
            const items = [];
            $list.children('li').each(function() {
                const id = $(this).attr('id')?.replace('tag-group-', '');
                const children = buildHierarchy($(this).children('ul'));
                items.push({
                    id: parseInt(id),
                    children
                });
            });
            return items;
        }



        let selectedGroupId = null;

        // タググループをクリックで選択・表示
        $(document).on('click', '.group-item > div', function() {
            selectedGroupId = $(this).closest('.group-item').data('id');

            // ハイライト用のクラス操作
            $('.group-item > div').removeClass('bg-indigo-800 text-white');
            $(this).addClass('bg-indigo-800 text-white');

            // タグ一覧を読み込み
            $.get(`/tags/by-group/${selectedGroupId}`, function(tags) {
                if (tags.length === 0) {
                    $('#tag-list').html('<p class="text-sm text-gray-400">タグがありません</p>');
                } else {
                    let html = '<ul class="list-disc pl-5 space-y-2">';
                    tags.forEach(tag => {
                        html += `<li class="flex justify-between items-center">
                        <span>${tag.name}</span>
                        <div class="space-x-2">
                            <button class="edit-tag-btn text-yellow-400 hover:text-yellow-500"
                                data-id="${tag.id}"
                                data-name="${tag.name}"
                                data-slug="${tag.slug}"
                                data-description="${tag.description ?? ''}">
                                <i data-lucide="edit"></i>
                            </button>
                            <button class="delete-tag-btn text-red-400 hover:text-red-500" data-id="${tag.id}">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </div>
                    </li>`;
                    });
                    html += '</ul>';
                    $('#tag-list').html(html);
                    lucide.createIcons();
                }

                // ボタンを有効化
                $('#add-tag-btn').prop('disabled', false);
            });
        });

        // タグ追加ボタン押下 → モーダル
        $('#add-tag-btn').on('click', function() {
            if (!selectedGroupId) {
                alert('先にタググループを選択してください');
                return;
            }

            // 値をクリア
            $('#editTagModal-id').val('');
            $('#editTagModal-name').val('');
            $('#editTagModal-slug').val('');
            $('#editTagModal-description').val('');

            $('#editTagModal').data('mode', 'create');

            const modalElement = document.getElementById('editTagModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('モーダルが見つかりません: #editTagModal');
            }
        });

        // タグ編集ボタン → モーダルに値をセット
        $(document).on('click', '.edit-tag-btn', function() {
            $('#editTagModal-id').val($(this).data('id'));
            $('#editTagModal-name').val($(this).data('name'));
            $('#editTagModal-slug').val($(this).data('slug'));
            $('#editTagModal-description').val($(this).data('description') ?? '');
            $('#editTagModal').data('mode', 'edit');
            new bootstrap.Modal(document.getElementById('editTagModal')).show();
        });

        // タグ保存（create / update）
        $('#save-tag-btn').on('click', function() {
            const id = $('#editTagModal-id').val();
            const data = {
                name: $('#editTagModal-name').val(),
                slug: $('#editTagModal-slug').val(),
                description: $('#editTagModal-description').val(),
                _token: '{{ csrf_token() }}'
            };

            if ($('#editTagModal').data('mode') === 'create') {
                data.tag_group_id = selectedGroupId;
                $.post('/tags', data, function() {
                    // location.reload(); はやめて、再読み込みだけにする
                    loadTags(selectedGroupId);
                });
            } else {
                $.ajax({
                    url: `/tags/${id}`,
                    method: 'PATCH',
                    data: data,
                    success: () => location.reload()
                });
            }
        });

        // タグ削除
        $(document).on('click', '.delete-tag-btn', function() {
            const id = $(this).data('id');
            if (confirm('本当に削除しますか？')) {
                $.ajax({
                    url: `/tags/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: () => location.reload()
                });
            }
        });

    });

    function loadTags(groupId) {
        $.get(`/tags/by-group/${groupId}`, function(tags) {
            if (tags.length === 0) {
                $('#tag-list').html('<p class="text-sm text-gray-400">タグがありません</p>');
            } else {
                let html = '<ul class="list-disc pl-5 space-y-2">';
                tags.forEach(tag => {
                    html += `<li class="flex justify-between items-center">
                    <span>${tag.name}</span>
                    <div class="space-x-2">
                        <button class="edit-tag-btn text-yellow-400 hover:text-yellow-500"
                            data-id="${tag.id}"
                            data-name="${tag.name}"
                            data-slug="${tag.slug}"
                            data-description="${tag.description ?? ''}">
                            <i data-lucide="edit"></i>
                        </button>
                        <button class="delete-tag-btn text-red-400 hover:text-red-500" data-id="${tag.id}">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                </li>`;
                });
                html += '</ul>';
                $('#tag-list').html(html);
                lucide.createIcons();
            }

            $('#add-tag-btn').prop('disabled', false);
        });
    }
</script>