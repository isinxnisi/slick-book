@section('title', 'カテゴリ設定')
<x-app-layout>
    <x-slot name="header">
        <!-- サイト切替タブ -->
        <x-admin.ui.site-tabs :sites="$sites" :active-id="$siteId" />
    </x-slot>

    <!-- ルート階層の追加 -->
    <div class="mb-4">
        <button id="add-root-btn" class="bg-indigo-800 text-white px-4 py-2 rounded hover:bg-indigo-900">
            ＋ ルートカテゴリを追加
        </button>
    </div>

    <div class="max-w-4xl">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <!-- カテゴリ階層ツリー -->
                <div class="hierarchy-tree">
                    <ul class="tree sortable" id="category-list">
                        @foreach($categories as $category)
                        <x-admin.categories.category-item :category="$category" />
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- 編集モーダル -->
    <x-admin.categories.edit-modal
        id="editCategoryModal"
        title="カテゴリ編集"
        :fields="[
            'title' => 'タイトル',
            'slug' => 'スラッグ',
            'description' => '説明',
            'image_path' => '画像URL',
            'icon' => 'アイコン名（例：folder）',
            'color' => 'カラーコード（例：#ff0000）',
            'is_visible' => '公開フラグ',
        ]"
        saveButtonId="save-category-btn" />
</x-app-layout>

<script>
    $(function() {
        $('.sortable').sortable({
            connectWith: '.sortable',
            placeholder: 'ui-state-highlight',
            cursor: 'move',
            update: function() {
                const hierarchyData = buildHierarchy($('#category-list'));
                $.post("{{ route('categories.reorder') }}", {
                    _token: '{{ csrf_token() }}',
                    hierarchy: hierarchyData
                });
            }
        }).disableSelection();
        // 追加
        $('#add-root-btn').on('click', function() {
            const title = prompt("新しいルートカテゴリ名を入力してください");
            if (title) {
                $.post("{{ route('categories.store') }}", {
                    _token: '{{ csrf_token() }}',
                    title: title,
                    site_id: '{{ $siteId }}'
                }, () => location.reload());
            }
        });

        // 削除ボタン
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            if (confirm('本当に削除しますか？')) {
                $.ajax({
                    url: `/categories/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: () => location.reload()
                });
            }
        });

        // 子の追加
        $(document).on('click', '.add-btn', function() {
            const parentId = $(this).data('id');
            const title = prompt('新しいカテゴリ名を入力してください');
            if (title) {
                $.post("{{ route('categories.store') }}", {
                    _token: '{{ csrf_token() }}',
                    title: title,
                    parent_id: parentId,
                    site_id: '{{ $siteId }}'
                }, () => location.reload());
            }
        });

        function buildHierarchy($list) {
            const items = [];
            $list.children('li').each(function() {
                const id = $(this).attr('id')?.replace('category-', '');
                const children = buildHierarchy($(this).children('ul'));
                items.push({
                    id: parseInt(id),
                    children
                });
            });
            return items;
        }
        $(document).on('click', '.edit-btn', function() {
            const fields = ['id', 'title', 'slug', 'description', 'image_path', 'icon', 'color'];
            fields.forEach(field => {
                $(`#editCategoryModal-${field}`).val($(this).data(field));
            });

            $('#editCategoryModal-is_visible').prop('checked', $(this).data('is_visible') == true);

            const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            modal.show();
        });
        $('#save-category-btn').on('click', function() {
            const id = $('#editCategoryModal-id').val();
            const fields = ['title', 'slug', 'description', 'image_path', 'icon', 'color'];
            const data = {};

            fields.forEach(field => {
                data[field] = $(`#editCategoryModal-${field}`).val();
            });

            // 保存時
            data['is_visible'] = $('#editCategoryModal-is_visible').is(':checked') ? 1 : 0;

            $.ajax({
                url: `/categories/${id}`,
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                data,
                success: () => location.reload()
            });
        });
    });
</script>