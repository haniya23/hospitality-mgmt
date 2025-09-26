<!-- Modern Top Bar -->
<div class="fixed top-0 left-0 right-0 z-40 bg-white/80 backdrop-blur-lg border-b border-gray-200/50 shadow-sm">
    <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4">
        <!-- Left: Menu & Title -->
        <div class="flex items-center space-x-3 sm:space-x-4">
            <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg lg:hidden">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">@yield('page-subtitle', 'Hospitality Management')</p>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center space-x-2 sm:space-x-3">
            <!-- Notifications -->
            <button class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all duration-200 relative group">
                <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5M10.07 2.82l3.12 3.12M7.05 5.84l3.12 3.12M4.03 8.86l3.12 3.12"></path>
                </svg>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center border-2 border-white">
                    <span class="text-xs font-bold text-white">3</span>
                </span>
            </button>

            <!-- Profile -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <div class="w-10 h-10 sm:w-11 sm:h-11 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-sm sm:text-base font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="hidden sm:block">
                    <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</span>
                    <p class="text-xs text-gray-500">{{ auth()->user()->plan_name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>