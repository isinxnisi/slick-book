{{-- modules/ContentModule/resources/views/admin/contents/form.blade.php --}}
@extends('content-module::layouts.admin')

@section('content')
@php
    $isEdit       = isset($entity) && $entity->getId();
    $initialType  = old('content_type', $entity?->getContentType() ?? array_key_first(config('content.types')));
    $initialKind  = old('content_kind', $entity?->getContentKind() ?? null);
    $mapping      = config('meta_schema.mapping');
    $kindsAll     = config('content.kinds');
    $sets         = config('meta_schema.sets');
@endphp

<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">
        コンテンツ{{ $isEdit ? '編集' : '作成' }}
    </h1>

    <form id="content-form" method="POST"
          action="{{ $isEdit
              ? route('admin.contents.update', $entity->getId())
              : route('admin.contents.store')
          }}">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="mb-4 grid grid-cols-3 gap-4">
            {{-- TYPE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">TYPE</label>
                <select id="form-type" name="content_type" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach(config('content.types') as $t => $label)
                        <option value="{{ $t }}" {{ $initialType === $t ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- KIND --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">KIND</label>
                <select id="form-kind" name="content_kind" class="mt-1 block w-full rounded-md border-gray-300">
                    {{-- JS で初期化 --}}
                </select>
            </div>

        </div>

        {{-- フォームフィールド描画エリア --}}
        <div id="form-fields">
            {!! $strategy->renderFormFields($entity ?? null) !!}
        </div>

        <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">
            {{ $isEdit ? '更新' : '保存' }}
        </button>
    </form>

    @if($isEdit)
    <div class="mt-6 space-x-2">
        {{-- ステータスごとの操作 --}}
        @if($entity->getStatus() === 'draft')
            <form method="POST"
                  action="{{ route('admin.contents.to-review', $entity->getId()) }}"
                  class="inline">
                @csrf
                <button type="submit"
                        class="bg-indigo-600 text-white px-3 py-1 rounded">
                    レビュー申請
                </button>
            </form>
        @endif

        @if($entity->getStatus() === 'review')
            <form method="POST"
                  action="{{ route('admin.contents.publish', $entity->getId()) }}"
                  class="inline">
                @csrf
                <button type="submit"
                        class="bg-green-600 text-white px-3 py-1 rounded">
                    公開
                </button>
            </form>
        @endif

        @if($entity->getStatus() === 'published')
            <form method="POST"
                  action="{{ route('admin.contents.archive', $entity->getId()) }}"
                  class="inline">
                @csrf
                <button type="submit"
                        class="bg-gray-600 text-white px-3 py-1 rounded">
                    アーカイブ
                </button>
            </form>
        @endif
    </div>
    @endif
</div>

<script>
(() => {
    const mapping = @json($mapping);
    const kindsAll = @json($kindsAll);
    const typeEl = document.getElementById('form-type');
    const kindEl = document.getElementById('form-kind');

    // Type選択に応じてKindを絞り込む
    function populateKinds(selectedType, selectedKind = null) {
        const keys = Object.keys(mapping)
            .filter(k => k.split('.')[0] === selectedType)
            .map(k => k.split('.')[1]);
        const unique = [...new Set(keys)];
        kindEl.innerHTML = '';
        unique.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k;
            opt.textContent = kindsAll[k] || k;
            if (k === selectedKind) opt.selected = true;
            kindEl.appendChild(opt);
        });
    }

    // フォームフィールド再描画
    function loadFormFields() {
        const type = typeEl.value;
        const kind = kindEl.value;

        fetch(`{{ route('admin.contents.form-fields') }}?type=${type}&kind=${kind}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (res.status === 404) {
                document.getElementById('form-fields').innerHTML =
                    '<p class="text-red-500">この組み合わせのフォームは存在しません。</p>';
                return Promise.reject();
            }
            if (!res.ok) {
                document.getElementById('form-fields').innerHTML =
                    '<p class="text-red-500">フォームの読み込みに失敗しました。</p>';
                return Promise.reject();
            }
            return res.text();
        })
        .then(html => {
            document.getElementById('form-fields').innerHTML = html;
        })
        .catch(() => {
            // エラー時は既にメッセージ表示済み
        });
    }

    // 初期描画
    populateKinds("{{ $initialType }}", "{{ $initialKind }}");

    // イベント登録
    typeEl.addEventListener('change', () => {
        populateKinds(typeEl.value);
        loadFormFields();
    });
    kindEl.addEventListener('change', loadFormFields);
})();
</script>
@endsection

