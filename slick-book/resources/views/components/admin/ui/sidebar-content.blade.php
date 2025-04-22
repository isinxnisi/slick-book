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
                        記事の投稿
                    </a>
                </li>
            </ul>
        </div>

        <!-- タグ管理メニュー -->
        <div class="mt-1">
            <button @click="open.tags = !open.tags" class="w-full text-left ps-2 py-2 text-gray-400 hover:text-gray-200 flex justify-between items-center">
                マスタ管理
                <svg x-bind:class="{ 'rotate-180': open.tags }" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <ul x-show="open.tags" class="mt-2 space-y-1">
                <li class="">
                    <a href="{{ route('sites.index') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('sites.index') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        サイト一覧
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('tag-groups.index') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('tag-groups.index') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        タグ設定（用途別）
                    </a>
                </li>
            </ul>
        </div>

        <!-- サイト設定メニュー -->
        <div class="mt-1">
            <button @click="open.sites = !open.sites" class="w-full text-left ps-2 py-2 text-gray-400 hover:text-gray-200 flex justify-between items-center">
                サイト設定
                <svg x-bind:class="{ 'rotate-180': open.sites }" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <ul x-show="open.sites" class="mt-2 space-y-1">
                <li class="">
                    <a href="{{ route('site-seo-settings.edit', [], false) }}"
                        class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                               {{ request()->routeIs('site-seo-settings.edit') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                         SEO設定（サイト別）
                     </a>
                </li>
                <li class="">
                    <a href="{{ route('site-banners.index') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('site-banners.index') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        バナー設定
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('categories.tree') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('categories.tree') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        カテゴリ設定
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('site-tag-groups.index') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('site-tag-groups.index') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        タグ設定（サイト別）
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('taxonomy-terms.tree') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                              {{ request()->routeIs('taxonomy-terms.tree') ? 'bg-gray-100 dark:bg-gray-700 font-semibold border-r-4 border-indigo-600' : '' }}">
                        タクソノミー設定
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
