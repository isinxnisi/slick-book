<li id="term-{{ $term->id }}" data-term-id="{{ $term->id }}" class="pl-2">
    <div class="flex items-center justify-between">
        <!-- 左寄せ：タイトル＋編集＋追加 -->
        <div class="space-x-1">
            <span class="title">{{ $term->name }}</span>
            <button class="edit-term-btn text-green-500" data-id="{{ $term->id }}">
                <i data-lucide="edit"></i>
            </button>
            <button class="add-child-term-btn text-sm text-green-400 hover:text-green-500" data-id="{{ $term->id }}">
                <i data-lucide="square-plus"></i>
            </button>
        </div>
        <div class="flex items-center justify-between w-auto">

            <!-- 右寄せ：削除ボタン -->
            <div class="ml-4">
                <button class="delete-term-btn text-sm text-red-400 hover:text-red-500" data-id="{{ $term->id }}">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>
    </div>
    <ul class="sortable ml-4">
    @if($term->children->isNotEmpty())
        @foreach($term->children as $child)
            <x-admin.taxonomy-terms.term-item :term="$child" />
        @endforeach
    @endif
    </ul>
</li>
