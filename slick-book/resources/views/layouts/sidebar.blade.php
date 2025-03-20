<aside class="w-64 h-screen dark:bg-gray-800 shadow-lg">
    <div class="p-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">メニュー</h2>
        <ul class="mt-4">
            <li class="mb-2">
                <a href="{{ route('dashboard') }}" class="block p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">ダッシュボード</a>
            </li>
            <li class="mb-2">
                <a href="{{ route('posts.index') }}" class="block p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">記事一覧</a>
            </li>
            <li class="mb-2">
                <a href="{{ route('posts.create') }}" class="block p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">新規記事</a>
            </li>
            <li class="mb-2">
                <a href="{{ route('profile.edit') }}" class="block p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">プロフィール</a>
            </li>
        </ul>
    </div>
</aside>
