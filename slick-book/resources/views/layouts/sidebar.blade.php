<aside class="w-64 h-screen dark:bg-gray-800 shadow-lg" x-data="{ open: { dashboard: true, sites: true, tags: true, categories: true, hierarchies: true, posts: true, settings: true } }">
    <div>
        <h2 class="flex h-16 font-semibold text-gray-800 dark:text-white p-4 ps-2 border-b border-gray-100 dark:border-gray-700">
            <!-- Logo -->
            <div class="shrink-0 flex items-center me-2">
                <a href="{{ route('dashboard') }}">
                    <x-admin.application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                </a>
            </div>
            SLICK BOOK
        </h2>

        <!-- ダッシュボード -->
        <div>
            <button @click="open.dashboard = !open.dashboard" class="w-full text-left ps-0 py-2 text-gray-400 hover:text-gray-200 flex justify-between items-center">
                <a href="{{ route('dashboard') }}"
                    class="block w-100 p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                            {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                    ダッシュボード
                </a>
            </button>
        </div>

        <!-- サイト管理メニュー -->
        <div class="mt-1">
            <button @click="open.sites = !open.sites" class="w-full text-left ps-2 py-2 text-gray-400 hover:text-gray-200 flex justify-between items-center">
                サイト管理
                <svg x-bind:class="{ 'rotate-180': open.sites }" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <ul x-show="open.sites" class="mt-2 space-y-1">
                <li class="">
                    <a href="{{ route('sites.index') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('sites.index') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        サイト設定
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('categories.tree') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('categories.tree') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        カテゴリ設定
                    </a>
                </li>
            </ul>
        </div>

        <!-- タグ管理メニュー -->
        <div class="mt-1">
            <button @click="open.tags = !open.tags" class="w-full text-left ps-2 py-2 text-gray-400 hover:text-gray-200 flex justify-between items-center">
                タグ管理
                <svg x-bind:class="{ 'rotate-180': open.tags }" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <ul x-show="open.tags" class="mt-2 space-y-1">
                <li class="">
                    <a href="{{ route('tag-groups.index') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('tag-groups.index') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        タググループ設定
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('categories.tree') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('categories.tree') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        カテゴリ設定
                    </a>
                </li>
            </ul>
        </div>

        <!-- 階層管理メニュー -->
        <!-- <div class="mt-1">
            <button @click="open.hierarchies = !open.hierarchies" class="w-full text-left ps-2 py-2 text-gray-400 hover:text-gray-200 flex justify-between items-center">
                階層管理
                <svg x-bind:class="{ 'rotate-180': open.hierarchies }" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <ul x-show="open.hierarchies" class="mt-2 space-y-1">
                <li class="">
                    <a href="{{ route('hierarchies.index') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('hierarchies.index') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        階層設定
                    </a>
                </li>
            </ul>
        </div> -->

        <!-- 記事メニュー -->
        <div class="mt-1">
            <button @click="open.posts = !open.posts" class="w-full text-left ps-2 py-2 text-gray-400 hover:text-gray-200 flex justify-between items-center">
                記事管理
                <svg x-bind:class="{ 'rotate-180': open.posts }" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <ul x-show="open.posts" class="mt-2 space-y-1">
                <li class="">
                    <a href="{{ route('posts.index') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('posts.index') ? 'border-r-4 border-indigo-600 bg-gray-100 dark:bg-gray-700 font-semibold' : '' }}">
                        記事一覧
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('posts.create') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('posts.create') ? 'border-r-4 border-indigo-600 bg-gray-100 dark:bg-gray-700 font-semibold' : '' }}">
                        新規記事
                    </a>
                </li>
            </ul>
        </div>

        <!-- 設定メニュー -->
        <div class="mt-1">
            <button @click="open.settings = !open.settings" class="w-full text-left ps-2 py-2 text-gray-400 hover:text-gray-200 flex justify-between items-center">
                ユーザー設定
                <svg x-bind:class="{ 'rotate-180': open.settings }" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <ul x-show="open.settings" class="mt-2 space-y-1">
                <li class="">
                    <a href="{{ route('profile.edit') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('profile.edit') ? 'border-r-4 border-indigo-600 bg-gray-100 dark:bg-gray-700 font-semibold' : '' }}">
                        プロフィール
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
