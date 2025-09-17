<!-- Top Bar -->
<div class="fixed top-0 left-0 right-0 z-40" style="background: linear-gradient(135deg, rgba(245, 73, 144, 0.9) 0%, rgba(138, 43, 226, 0.9) 100%); backdrop-filter: blur(10px);">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Left: Menu & Title -->
        <div class="flex items-center space-x-3">
            <button @click="$dispatch('toggle-sidebar')" class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition-all lg:hidden">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div>
                <h1 class="text-lg font-bold text-white">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-white opacity-75 hidden sm:block">@yield('page-subtitle', 'Hospitality Management')</p>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center space-x-2">
            <!-- Notifications -->
            <button class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition-all relative">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5M10.07 2.82l3.12 3.12M7.05 5.84l3.12 3.12M4.03 8.86l3.12 3.12"></path>
                </svg>
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                    <span class="text-xs font-bold text-white">3</span>
                </span>
            </button>

            <!-- Profile -->
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <span class="text-sm font-medium text-white hidden sm:block">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>
</div>