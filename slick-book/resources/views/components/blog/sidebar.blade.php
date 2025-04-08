<aside id="site-side-menu" class="w-64 sticky overflow-y-auto bg-white"
    x-data="{ open: { dashboard: true, sites: true, tags: true, categories: true, hierarchies: true, posts: true, settings: true } }">
    <div>
        <h2 class="flex h-16 font-semibold p-4 ps-2 border-b border-gray-100">
            <a href="{{ route('blog.home') }}">
                <!-- Logo -->
                <div class="shrink-0 flex items-center me-2">
                    <x-admin.application-logo class="block h-9 w-auto fill-current" />
                    <span class="flex-1 px-2">SLICK BOOK</span>
                </div>
            </a>
        </h2>

        <!-- 記事メニュー -->
        <div class="my-4">
            <button @click="open.posts = !open.posts" class="w-full text-left ps-2 py-2 dark:text-gray-700 hover:bg-gray-200 dark:hover:bg-gray-200 flex justify-between items-center">
                記事管理
                <svg x-bind:class="{ 'rotate-180': open.posts }" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <ul x-show="open.posts" class="mt-2 space-y-1">
                <li class="">
                    <a href="{{ route('blog.home') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-700 hover:bg-gray-200 dark:hover:bg-gray-200
                              {{ request()->routeIs('blog.home') ? 'border-r-4 border-indigo-100 bg-gray-100 dark:bg-gray-200 font-semibold' : '' }}">
                        HOME
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('posts.create') }}"
                       class="block p-2 ps-4 text-gray-600 dark:text-gray-700 hover:bg-gray-200 dark:hover:bg-gray-200
                              {{ request()->routeIs('posts.create') ? 'border-r-4 border-indigo-100 bg-gray-100 dark:bg-gray-200 font-semibold' : '' }}">
                        記事の投稿
                    </a>
                </li>
            </ul>
        </div>

    </div>
</aside>
