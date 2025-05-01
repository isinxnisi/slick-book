@extends('content-module::layouts.admin')

@section('content')
<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">コンテンツ一覧</h1>

    <div class="mb-4 flex items-center space-x-4">
        <div>
            <label>TYPE:</label>
            <select id="filter-type" class="border rounded p-1">
                @foreach($types as $t => $label)
                    <option value="{{ $t }}" @if($t === $type) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>KIND:</label>
            <select id="filter-kind" class="border rounded p-1">
                <option value="">全て</option>
                @foreach($kinds as $k => $label)
                    <option value="{{ $k }}" @if($k === $kind) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <a href="{{ route('admin.contents.create') }}" class="bg-blue-500 text-white px-3 py-1 rounded">新規作成</a>
        </div>
    </div>

    <table class="w-full table-auto border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">ID</th>
                <th class="border px-2 py-1">TITLE</th>
                <th class="border px-2 py-1">TYPE</th>
                <th class="border px-2 py-1">KIND</th>
                <th class="border px-2 py-1">STATUS</th>
                <th class="border px-2 py-1">操作</th>
            </tr>
        </thead>
        <tbody id="contents-table-body">
            @foreach($items as $item)
                <tr>
                    <td class="border px-2 py-1">{{ $item->getId() }}</td>
                    <td class="border px-2 py-1">{{ $item->getTitle() }}</td>
                    <td class="border px-2 py-1">{{ $item->getContentType() }}</td>
                    <td class="border px-2 py-1">{{ $item->getContentKind() }}</td>
                    <td class="border px-2 py-1">{{ $item->getStatus() }}</td>
                    <td class="border px-2 py-1">
                        {{-- 編集 --}}
                        <a href="{{ route('admin.contents.edit', ['id' => $item->getId()]) }}" class="text-blue-600">編集</a>
                        {{-- 削除 --}}
                        <form method="POST" action="{{ route('admin.contents.destroy', ['id' => $item->getId()]) }}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('本当に削除しますか？')" class="text-red-600 ml-2">
                                削除
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    document.getElementById('filter-type').addEventListener('change', function() {
        const type = this.value;
        const kind = document.getElementById('filter-kind').value;
        location.search = `?type=${type}&kind=${kind}`;
    });
    document.getElementById('filter-kind').addEventListener('change', function() {
        const type = document.getElementById('filter-type').value;
        const kind = this.value;
        location.search = `?type=${type}&kind=${kind}`;
    });
</script>
