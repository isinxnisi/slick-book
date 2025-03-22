@props([
    'id' => 'modal',
    'title' => 'モーダルタイトル',
    'fields' => [], // 例: ['title' => 'タイトル', 'slug' => 'スラッグ']
    'saveButtonId' => 'save-btn',
])

<!-- 共通モーダル -->
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            <div class="modal-header dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="{{ $id }}-id">
                @foreach($fields as $field => $label)
                    <div class="mb-3">
                        <label class="form-label">{{ $label }}</label>
                        @if ($field === 'description')
                            <textarea id="{{ $id }}-{{ $field }}" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700"></textarea>
                        @else
                            <input type="text" id="{{ $id }}-{{ $field }}" class="form-control dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="modal-footer dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" id="{{ $saveButtonId }}">保存</button>
            </div>
        </div>
    </div>
</div>