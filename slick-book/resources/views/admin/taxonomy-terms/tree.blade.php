@section('title', 'タクソノミー管理')
@php
    $icons = config('icons.list');
    $defaultIcon = config('icons.default', 'folder');
@endphp
<x-app-layout>
    <x-slot name="header">
        <x-admin.ui.site-tabs :sites="$sites" :active-id="$siteId" />
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-[3fr_2fr] gap-4">
        <!-- 左側：タクソノミー階層ツリー -->
        <div class="overflow-hidden">
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <!-- タクソノミー切替 -->
                <div class="d-inline-flex w-100 mb-0 pt-2 space-x-2 border-b border-gray-600">
                    <div class="flex-1 flex" style="overflow-x: auto">
                        @foreach ($taxonomies as $txm)
                        <a href="{{ route('taxonomy-terms.tree', ['site' => $siteId, 'taxonomy' => $txm->id]) }}"
                            class="relative px-4 py-2 text-sm font-medium transition-all
                        {{ $taxonomy->id === $txm->id
                            ? 'text-white border-b-2 border-indigo-500'
                            : 'text-gray-400 hover:text-white hover:border-b-2 hover:border-gray-500' }}">
                            {{ $txm->name }}
                        </a>
                        @endforeach
                    </div>
                    <div class="mt-0 mb-0 pb-2 pe-2">
                        @isset($taxonomy)
                        <button class="edit-taxonomy-btn text-green-500 px-2 py-2 rounded hover:bg-gray-900" data-id="{{ $taxonomy->id }}">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        @endisset
                        <button id="add-taxonomy-btn" class="text-white px-2 py-2 rounded hover:bg-gray-900">
                            <i data-lucide="square-plus" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div class="hierarchy-tree p-4 text-gray-900 dark:text-gray-100">
                    <ul class="tree sortable" id="taxonomy-term-list">
                        @foreach ($terms as $term)
                            <x-admin.taxonomy-terms.term-item :term="$term" />
                        @endforeach
                    </ul>
                    <div class="mt-4 mb-4">
                        <button id="add-term-btn"
                            class="bg-indigo-700 text-white px-4 py-2 rounded hover:bg-indigo-800">
                            {{-- 文言：階層構造モードのときだけ「階層を追加」 --}}
                            @if($taxonomy->is_hierarchical)
                            ＋ 階層を追加
                            @else
                            ＋ 項目を追加
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 右側：編集パネル -->
        <div class="ui-right-panel sm:rounded-lg" style="overflow-x: hidden;">
            <div id="term-edit-panel" class="panel text-gray-900 dark:text-gray-100 dark:bg-gray-800 shadow-sm sm:rounded-lg" style="display: none;">
                <div class="panel-header py-2 px-4 mb-0 border-b border-gray-700">
                    <h3 class="text-lg font-bold">タクソノミー項目<span class="mode-text"></span></h3>
                </div>
                <form id="term-edit-form" class="panel-content p-4">
                    <input type="hidden" id="editPanel-term-id">
                    <input type="hidden" id="editPanel-term-parent_id">
                    <input type="hidden" id="editPanel-taxonomy-id" value="{{ $taxonomy?->id }}">

                    <div class="mb-2">
                        <label for="editPanel-term-name">表示名</label>
                        <input type="text" id="editPanel-term-name" class="form-control dark:bg-gray-900 dark:text-white">
                    </div>

                    <div class="mb-2">
                        <label for="editPanel-term-slug">スラッグ</label>
                        <input type="text" id="editPanel-term-slug" class="form-control dark:bg-gray-900 dark:text-white">
                    </div>

                    <div class="mb-2">
                        <label for="editPanel-term-description">説明</label>
                        <textarea id="editPanel-term-description" class="form-control dark:bg-gray-900 dark:text-white"></textarea>
                    </div>

                    <div class="mb-2">
                        <label for="editPanel-term-is_public">
                            <input type="checkbox" id="editPanel-term-is_public"> 公開する
                        </label>
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" id="term-panel-cancel"
                            class="bg-gray-600 text-white px-4 py-2 rounded">キャンセル</button>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">保存</button>
                    </div>
                </form>
            </div>

            <!-- タクソノミー追加・編集用パネル -->
            <div id="taxonomy-edit-panel" class="panel text-gray-900 dark:text-gray-100 dark:bg-gray-800 shadow-sm sm:rounded-lg" style="display: none;">
                <div class="panel-header py-2 px-4 mb-0 border-b border-gray-700">
                    <h3 class="text-lg font-bold">タクソノミー<span class="mode-text"></span></h3>
                </div>
                <form id="taxonomy-edit-form" class="panel-content p-4">
                    @csrf
                    <input type="hidden" id="editPanel-taxonomy-id">
                    <input type="hidden" id="editPanel-site-id" value="{{ $siteId }}">

                    <div class="mb-2">
                        <label for="editPanel-taxonomy-name">表示名</label>
                        <input type="text" id="editPanel-taxonomy-name" class="form-control dark:bg-gray-900 dark:text-white">
                    </div>

                    <div class="mb-2">
                        <label for="editPanel-taxonomy-slug">スラッグ</label>
                        <input type="text" id="editPanel-taxonomy-slug" class="form-control dark:bg-gray-900 dark:text-white">
                    </div>

                    <div class="mb-2">
                        <label for="editPanel-taxonomy-type">タイプ</label>
                        <input type="text" id="editPanel-taxonomy-type" class="form-control dark:bg-gray-900 dark:text-white">
                    </div>

                    <div class="mb-2">
                        <label for="editPanel-taxonomy-purpose">用途（purpose）</label>
                        <input type="text" id="editPanel-taxonomy-purpose" class="form-control dark:bg-gray-900 dark:text-white">
                    </div>

                    <div class="mb-2">
                        <label>
                            <input type="checkbox" id="editPanel-taxonomy-is_hierarchical"> 階層構造にする
                        </label>
                    </div>

                    <div class="mb-2">
                        <label>
                            <input type="checkbox" id="editPanel-taxonomy-is_public" checked> 公開する
                        </label>
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" id="taxonomy-panel-cancel"
                            class="bg-gray-600 text-white px-4 py-2 rounded">キャンセル</button>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">保存</button>
                    </div>
                </form>
            </div>
        </div>


    </div>

    @push('scripts')
        <script>
            $(function() {
                // 階層並べ替え
                $('.sortable').sortable({
                    connectWith: '.sortable',
                    placeholder: 'ui-state-highlight',
                    cursor: 'move',
                    update: function() {
                        const hierarchy = buildHierarchy($('#taxonomy-term-list'));
                        $.post('{{ route('taxonomy-terms.reorder') }}', {
                            _token: '{{ csrf_token() }}',
                            hierarchy: hierarchy
                        });
                    }
                });

                // 階層追加ボタン
                $('#add-term-btn').on('click', function() {
                    $('#editPanel-term-id').val('');
                    $('#editPanel-term-parent_id').val('');
                    $('#editPanel-term-name').val('');
                    $('#editPanel-term-slug').val('');
                    $('#editPanel-term-description').val('');
                    $('#editPanel-term-is_public').prop('checked', true);
                    $('.ui-right-panel .panel').hide();
                    $('#term-edit-panel .mode-text').text('追加');
                    window.fadeSlideInRight($('#term-edit-panel'));
                });
                // 編集ボタン
                $(document).on('click', '.edit-term-btn', function() {
                    const termId = $(this).data('id');
                    $.get(`/taxonomy-terms/${termId}`, function(term) {
                        $('#editPanel-term-id').val(term.id);
                        $('#editPanel-term-parent_id').val(term.parent_id);
                        $('#editPanel-term-name').val(term.name);
                        $('#editPanel-term-slug').val(term.slug);
                        $('#editPanel-term-description').val(term.description);
                        $('#editPanel-term-is_public').prop('checked', term.is_public);

                        $('.ui-right-panel .panel').hide();
                        $('#term-edit-panel .mode-text').text('編集');
                        window.fadeSlideInRight($('#term-edit-panel'));
                    });
                });
                // 子階層追加ボタン
                $(document).on('click', '.add-child-term-btn', function() {
                    const parentId = $(this).data('id');
                    $('#editPanel-term-id').val('');
                    $('#editPanel-term-parent_id').val(parentId);
                    $('#editPanel-term-name').val('');
                    $('#editPanel-term-slug').val('');
                    $('#editPanel-term-description').val('');
                    $('#editPanel-term-is_public').prop('checked', true);

                    $('.ui-right-panel .panel').hide();
                    $('#term-edit-panel .mode-text').text('子階層追加');
                    window.fadeSlideInRight($('#term-edit-panel'));
                });
                // タクソノミー項目削除
                $(document).on('click', '.delete-term-btn', function() {
                    const termId = $(this).data('id');

                    if (!confirm('この項目を削除してもよろしいですか？')) return;

                    $.ajax({
                        url: `/admin/taxonomy-terms/${termId}`,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: () => location.reload(),
                        error: () => alert('削除に失敗しました')
                    });
                });
                // taxonomy追加用の専用モーダル表示
                $('#add-taxonomy-btn').on('click', function() {
                    $('#taxonomy-add-modal').modal('show'); // Bootstrapモーダル例
                });
                // 編集フォーム保存
                $('#term-edit-form').on('submit', function(e) {
                    e.preventDefault();
                    const id = $('#editPanel-term-id').val();
                    const data = {
                        taxonomy_id: $('#editPanel-taxonomy-id').val(),
                        parent_id: $('#editPanel-term-parent_id').val(),
                        name: $('#editPanel-term-name').val(),
                        slug: $('#editPanel-term-slug').val(),
                        description: $('#editPanel-term-description').val(),
                        is_public: $('#editPanel-term-is_public').prop('checked') ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    };
                    const method = id ? 'PUT' : 'POST';
                    const url = id ? `/taxonomy-terms/${id}` : '/taxonomy-terms';

                    $.ajax({
                        url,
                        method,
                        data,
                        success: () => location.reload()
                    });
                });

                // キャンセルボタン
                $('#term-panel-cancel').on('click', () => $('#term-edit-panel').slideUp());
            });

            function buildHierarchy($list) {
                const items = [];
                $list.children('li').each(function() {
                    const id = parseInt($(this).data('term-id'));
                    const children = buildHierarchy($(this).children('ul'));
                    items.push({
                        id,
                        children
                    });
                });
                return items;
            }

            /* ----------------------------------------
                taxonomy 登録／変更
            ---------------------------------------- */
            // taxonomy追加ボタン
            $('#add-taxonomy-btn').on('click', function() {
                $('#editPanel-taxonomy-id').val('');
                $('#editPanel-taxonomy-name').val('');
                $('#editPanel-taxonomy-slug').val('');
                $('#editPanel-taxonomy-type').val('');
                $('#editPanel-taxonomy-purpose').val('');
                $('#editPanel-taxonomy-is_hierarchical').prop('checked', false);
                $('#editPanel-taxonomy-is_public').prop('checked', true);
                $('.ui-right-panel .panel').hide();
                $('#taxonomy-edit-panel .mode-text').text('追加');
                window.fadeSlideInRight($('#taxonomy-edit-panel'));
            });

            // taxonomy編集ボタン（タブ項目クリックで編集）
            $(document).on('click', '.edit-taxonomy-btn', function(e) {
                e.stopPropagation();
                const taxonomyId = $(this).data('id');

                $.get(`/taxonomies/${taxonomyId}`, function(taxonomy) {
                    $('#editPanel-taxonomy-id').val(taxonomy.id);
                    $('#editPanel-taxonomy-name').val(taxonomy.name);
                    $('#editPanel-taxonomy-slug').val(taxonomy.slug);
                    $('#editPanel-taxonomy-type').val(taxonomy.type);
                    $('#editPanel-taxonomy-purpose').val(taxonomy.purpose);
                    $('#editPanel-taxonomy-is_hierarchical').prop('checked', taxonomy.is_hierarchical);
                    $('#editPanel-taxonomy-is_public').prop('checked', taxonomy.is_public);
                    $('.ui-right-panel .panel').hide();
                    $('#taxonomy-edit-panel .mode-text').text('編集');
                    window.fadeSlideInRight($('#taxonomy-edit-panel'));
                });
            });

            // キャンセルボタン
            $('#taxonomy-panel-cancel').on('click', function() {
                $('#taxonomy-edit-panel').slideUp();
            });

            // taxonomyフォーム送信（右パネル）
            $('#taxonomy-edit-form').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editPanel-taxonomy-id').val();
                const url = id ? `/taxonomies/${id}` : '/taxonomies';
                const method = id ? 'PUT' : 'POST';

                const data = {
                    site_id: $('#editPanel-site-id').val(),
                    name: $('#editPanel-taxonomy-name').val(),
                    slug: $('#editPanel-taxonomy-slug').val(),
                    type: $('#editPanel-taxonomy-type').val(),
                    purpose: $('#editPanel-taxonomy-purpose').val(),
                    is_hierarchical: $('#editPanel-taxonomy-is_hierarchical').prop('checked') ? 1 : 0,
                    is_public: $('#editPanel-taxonomy-is_public').prop('checked') ? 1 : 0,
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url,
                    method,
                    data,
                    success: () => location.reload(),
                    error: () => alert('保存に失敗しました')
                });
            });
        </script>
    @endpush
</x-app-layout>
