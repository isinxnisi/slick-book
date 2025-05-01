{{-- resources/views/admin/contents/form.blade.php --}}
@extends('content-module::layouts.admin')

@section('content')
<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">
        コンテンツ{{ $entity && $entity->getId() ? '編集' : '作成' }}
    </h1>

    @php
        // 編集モードかどうか
        $isEdit = $entity && $entity->getId();
    @endphp

    <form method="POST"
          action="{{ $isEdit
              ? route('admin.contents.update', $entity->getId())
              : route('admin.contents.store')
          }}">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="mb-4 grid grid-cols-2 gap-4">
            {{-- TYPE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">TYPE</label>
                <select id="form-type" name="content_type" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach($types as $t => $label)
                        <option value="{{ $t }}"
                            {{ old('content_type', $entity?->getContentType() ?? '') === $t ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <x-content-module::input-error :messages="$errors->get('content_type')" class="mt-2"/>
            </div>

            {{-- KIND --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">KIND</label>
                <select id="form-kind" name="content_kind" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach($kinds as $k => $label)
                        <option value="{{ $k }}"
                            {{ old('content_kind', $entity?->getContentKind() ?? '') === $k ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <x-content-module::input-error :messages="$errors->get('content_kind')" class="mt-2"/>
            </div>
        </div>

        {{-- 動的フォームフィールド --}}
        <div id="form-fields">
            {!! $strategy->renderFormFields($entity) !!}
        </div>

        <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">
            {{ $isEdit ? '更新' : '保存' }}
        </button>
    </form>
</div>

<script>
    function loadFormFields() {
        const type = document.getElementById('form-type').value;
        const kind = document.getElementById('form-kind').value;
        fetch(`{{ route('admin.contents.form-fields') }}?type=${type}&kind=${kind}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('form-fields').innerHTML = html;
            });
    }

    document.getElementById('form-type').addEventListener('change', loadFormFields);
    document.getElementById('form-kind').addEventListener('change', loadFormFields);
</script>
@endsection
