<aside id="site-side-menu" class="sticky"
    x-data="{ open: { dashboard: true, sites: true, tags: true, categories: true, hierarchies: true, posts: true, settings: true } }"
    style="width: 300px">
    <div>
        <div class="flex font-semibold px-2 py-2 ps-2 border-b border-gray-300 justify-content-center text-gray-600">
            GOOD / SHARE
        </div>
        <div class="flex font-semibold px-2 py-4 ps-2 border-b border-gray-300 gap-4 justify-content-center mb-4">
            {{-- 👍 GOODアイコン --}}
            <i data-lucide="thumbs-up" class="w-6 h-6 text-gray-600 hover:text-indigo-500 cursor-pointer"></i>

            {{-- 🐦 Twitter --}}
            <a href="https://twitter.com/share?url={{ urlencode(url()->current()) }}" target="_blank">
                <i data-lucide="twitter" class="w-6 h-6 text-gray-600 hover:text-blue-500"></i>
            </a>

            {{-- 📘 Facebook --}}
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank">
                <i data-lucide="facebook" class="w-6 h-6 text-gray-600 hover:text-blue-700"></i>
            </a>

            {{-- 🔗 Copy Link --}}
            <button onclick="navigator.clipboard.writeText('{{ url()->current() }}')" title="リンクをコピー">
                <i data-lucide="link" class="w-6 h-6 text-gray-600 hover:text-emerald-600"></i>
            </button>
        </div>

        <!-- 記事メニュー -->
        <div class="py-4 rounded bg-white shadow-sm">
            <a href="{{ route('blog.home') }}"
               class="block p-2 ps-2 text-gray-600 hover:bg-gray-200
                      {{ request()->routeIs('blog.home') ? 'border-r-4 border-indigo-100 bg-indigo-100 dark:bg-gray-200 font-semibold' : '' }}">
                HOME
            </a>
            <button @click="open.posts = !open.posts" class="w-full text-left mt-1 px-2 py-2 hover:bg-gray-200 flex justify-between items-center">
                記事カテゴリ
                <svg x-bind:class="{ 'rotate-180': open.posts }" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <ul x-show="open.posts" class="mt-2 space-y-1">
                <li class="">
                    <a href="{{ route('blog.home') }}"
                       class="block p-2 ps-4 text-gray-600 hover:bg-gray-200">
                        HOME
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('posts.create') }}"
                       class="block p-2 ps-4 text-gray-600 hover:bg-gray-200
                              {{ request()->routeIs('posts.create') ? 'border-r-4 border-indigo-100 bg-indigo-100 dark:bg-gray-200 font-semibold' : '' }}">
                        記事の投稿
                    </a>
                </li>
            </ul>
        </div>

    </div>
</aside>
