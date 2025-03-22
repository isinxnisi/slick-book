@section('title', '階層設定')
<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="mx-auto sm:px-6 lg:px-8">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="hierarchy-tree">
                    <ul class="tree sortable" id="hierarchy-list">
                        @foreach($hierarchies as $hierarchy)
                            <x-admin.hierarchies.hierarchy-item :hierarchy="$hierarchy" />
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- jQuery & jQuery UI -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script>
    $(function() {
        $(".sortable").sortable({
            connectWith: ".sortable",
            placeholder: "ui-state-highlight",
            cursor: "move",
            update: function(event, ui) {

                const hierarchyData = buildHierarchy($('#hierarchy-list'));

                $.ajax({
                    url: '/hierarchies/reorder',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        hierarchy: hierarchyData
                    },
                    success: function (response) {
                        // alert(response.message);
                    }
                });
            }
        }).disableSelection();

        // 編集（名前変更）
        $(document).on('click', '.edit-btn', function () {
            const id = $(this).data('id');
            const $title = $(`.title[data-id="${id}"]`);
            const currentName = $title.text();

            const newName = prompt("新しい名前を入力してください", currentName);
            if (newName && newName !== currentName) {
                $.ajax({
                    url: `/hierarchies/${id}`,
                    method: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}',
                        title: newName
                    },
                    success: () => location.reload()
                });
            }
        });

        // 削除
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            if (confirm("本当に削除しますか？")) {
                $.ajax({
                    url: `/hierarchies/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: () => location.reload()
                });
            }
        });

        // 子の追加
        $(document).on('click', '.add-btn', function () {
            const parentId = $(this).data('id');
            const title = prompt("新しい子ノードの名前を入力してください");
            if (title) {
                $.ajax({
                    url: `/hierarchies`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        title: title,
                        parent_id: parentId
                    },
                    success: () => location.reload()
                });
            }
        });
    });
    function buildHierarchy($list) {
        const items = [];
        $list.children('li').each(function () {
            const $li = $(this);
            const id = $li.attr('id')?.replace('hierarchy-', '');

            // 再帰的に子階層も構築
            const children = buildHierarchy($li.children('ul'));

            items.push({
                id: parseInt(id),
                children: children
            });
        });
        return items;
    }

</script>
