<nav x-data="{ open: false }" id="nav-header" class="border-b border-gray-100 bg-white">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-content-center h-16">
            <div class="hidden sm:flex sm:items-center sm:ms-0">
                <h2 class="flex h-16 font-semibold text-xl p-2 ps-2 border-b border-gray-100 leading-tight align-items-center">
                    <a href="{{ route('blog.home') }}">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center me-2">
                            <img src="{{asset('img/my-lab-charactor.png')}}" alt="" class="rounded shadow-sm" style="height:33px;">
                            <span class="flex-1 px-2">{{$currentSite->name}}</span>
                        </div>
                    </a>
                </h2>
            </div>
        </div>
    </div>

</nav>