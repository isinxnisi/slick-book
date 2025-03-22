@section('title', 'カテゴリ階層設定')
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
            'description' => '説明'
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
        // // 編集ボタン
        // $(document).on('click', '.edit-btn', function () {
        //     const id = $(this).data('id');
        //     // TODO: モーダルを開く
        // });

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
            const id = $(this).data('id');
            const title = $(this).data('title');
            const slug = $(this).data('slug');
            const description = $(this).data('description');

            // 各 input/textarea に値をセット（modal id: editCategoryModal）
            $('#editCategoryModal-id').val(id);
            $('#editCategoryModal-title').val(title);
            $('#editCategoryModal-slug').val(slug);
            $('#editCategoryModal-description').val(description);

            const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            modal.show();
        });

        $('#save-category-btn').on('click', function() {
            const id = $('#editCategoryModal-id').val();
            const title = $('#editCategoryModal-title').val();
            const slug = $('#editCategoryModal-slug').val();
            const description = $('#editCategoryModal-description').val();

            $.ajax({
                url: `/categories/${id}`,
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                data: {
                    title,
                    slug,
                    description
                },
                success: () => location.reload()
            });
        });
    });
</script>