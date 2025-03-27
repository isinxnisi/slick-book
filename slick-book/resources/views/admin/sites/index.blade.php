@section('title', 'サイト一覧')
<x-app-layout>
    <x-slot name="header">
        <!-- 検索フォーム -->
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('sites.create') }}" class="btn bg-indigo-800 text-white px-4 py-2 rounded hover:bg-indigo-900">
            新規サイトを追加
        </a>
    </div>

    <div class="max-w-7xl">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="text-left">ID</th>
                            <th class="text-left">名前</th>
                            <th class="text-left">スラッグ</th>
                            <th class="text-left">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sites as $site)
                        <tr>
                            <td class="py-2">{{ $site->id }}</td>
                            <td class="py-2">{{ $site->name }}</td>
                            <td class="py-2">{{ $site->slug }}</td>
                            <td class="py-2">
                                <a href="{{ route('sites.edit', $site) }}" class="text-sm text-green-400 hover:text-green-500">
                                    <i data-lucide="edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>