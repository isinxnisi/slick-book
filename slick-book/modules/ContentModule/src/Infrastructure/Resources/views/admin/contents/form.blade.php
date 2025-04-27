@extends('content-module::layouts.admin')

@section('content')
<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">コンテンツ{{ isset($entity) ? '編集' : '作成' }}</h1>

    <form method="POST" action="{{ isset($entity) ? route('admin.contents.update', $entity->getId()) : route('admin.contents.store') }}">
        @csrf
        @isset($entity)
            @method('PUT')
        @endisset

        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">TYPE</label>
                <select id="form-type" name="content_type" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach($types as $t)
                        <option value="{{ $t }}" @if(old('content_type', $entity?->getContentType() ?? '') === $t) selected @endif>{{ $t }}</option>
                    @endforeach
                </select>
                <x-content-module::input-error class="mt-2" :messages="$errors->get('content_type')" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">KIND</label>
                <select id="form-kind" name="content_kind" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach($kinds as $k)
                        <option value="{{ $k }}" @if(old('content_kind', $entity?->getContentKind() ?? '') === $k) selected @endif>{{ $k }}</option>
                    @endforeach
                </select>
                <x-content-module::input-error class="mt-2" :messages="$errors->get('content_type')" />
            </div>
        </div>

        {{-- 動的フォームフィールド --}}
        <div id="form-fields">
            {!! $strategy->renderFormFields($entity ?? null) !!}
        </div>

        <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">
            {{ isset($entity) ? '更新' : '保存' }}
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
