@props([
    'siteId',
    'selectedTagIdsByPurpose' => [],
    'purpose' => 'public',
])

@php
    $purposes = config('tags.purposes');
    $purposeStyles = config('tags.purpose_styles');
@endphp

<div class="border border-gray-700 rounded-md p-3">
    {{-- hidden input（タグ選択状態を保存） --}}
    <input type="hidden" name="selected_tag_ids[{{ $purpose }}]" id="selected-tags-{{ $purpose }}" 
        value='@json($selectedTagIdsByPurpose[$purpose] ?? [])'>
    
    {{-- タブ切り替えナビ --}}
    <div class="mb-4 border-b border-gray-600">
        <nav class="flex space-x-2" aria-label="タグ目的切替">
            @foreach ($purposes as $key => $label)
                <a href="?purpose={{ $key }}"
                   class="purpose-tab px-2 py-1 text-sm font-medium rounded-t
                          {{ $purpose === $key ? 'text-white bg-gray-800 border-t border-l border-r border-indigo-500' : 'text-gray-400 hover:text-white' }}"
                   style="border-color: {{ $purpose === $key ? ($purposeStyles[$key]['bg'] ?? '#4f46e5') : 'transparent' }};">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- タグセレクタ本体 --}}
    <div id="tag-selector-panel" class="space-y-2">
        <div id="tag-selector-content">
            読み込み中...
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.fetchTagSelector = function () {
        const siteId = @json($siteId);
        const purpose = @json($purpose);

        $.get(`/site-tag-groups/tags`, { site: siteId, purpose }, function (html) {
            $('#tag-selector-content').html(html);
            if (typeof window.refreshLucideAndBindEvents === 'function') {
                window.refreshLucideAndBindEvents();
            }
        });
    };

    $(function () {
        fetchTagSelector();
    });

    document.addEventListener("DOMContentLoaded", function () {
        const updateSelectedTags = function (purpose) {
            let selectedTags = [];
            document.querySelectorAll(`.tag-toggle-btn[data-purpose="${purpose}"].active`).forEach(el => {
                selectedTags.push(el.dataset.tagId);
            });
            document.getElementById(`selected-tags-${purpose}`).value = JSON.stringify(selectedTags);
        };

        document.querySelectorAll(".tag-toggle-btn").forEach(el => {
            el.addEventListener("click", function () {
                const purpose = this.dataset.purpose;
                updateSelectedTags(purpose);
            });
        });
    });
</script>
@endpush
