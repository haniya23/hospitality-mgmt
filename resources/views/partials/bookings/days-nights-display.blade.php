<!-- Days and Nights Display -->
<div x-show="days > 0" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     class="mt-6 p-3 sm:p-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border-2 border-purple-200 shadow-sm hover:shadow-md transition-shadow duration-300">

    <!-- Desktop Layout -->
    <div class="hidden sm:flex items-center justify-between gap-6">
        <!-- Days & Nights Section -->
        <div class="flex items-center gap-6">
            <!-- Days Counter -->
            <div class="relative">
                <div class="flex items-center gap-3 bg-white rounded-lg px-4 py-2 shadow-sm">
                    <button type="button" 
                            @click="decreaseDays()" 
                            x-show="days > 1"
                            class="group w-9 h-9 bg-gradient-to-br from-purple-600 to-indigo-700 text-white rounded-lg hover:from-purple-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm">
                        <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                        </svg>
                    </button>
                    
                    <div class="text-center min-w-[3rem]">
                        <div class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent" x-text="days"></div>
                        <div class="text-xs font-medium text-purple-600 uppercase tracking-wider mt-0.5">Days</div>
                    </div>
                    
                    <button type="button" 
                            @click="increaseDays()" 
                            x-show="canIncreaseDays()"
                            class="group w-9 h-9 bg-gradient-to-br from-purple-600 to-indigo-700 text-white rounded-lg hover:from-purple-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm">
                        <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Nights Display -->
            <div class="flex items-center gap-3 bg-white rounded-lg px-5 py-3 shadow-sm">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-800" x-text="nights"></div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nights</div>
                </div>
            </div>
        </div>
        
        <!-- Total Guests Section -->
        <div class="flex items-center gap-3 bg-white rounded-lg px-5 py-3 shadow-sm">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-0.5">Total Guests</div>
                <div class="text-2xl font-bold text-gray-800" x-text="totalGuests"></div>
            </div>
        </div>
    </div>

    <!-- Mobile Layout -->
    <div class="sm:hidden space-y-3">
        <!-- Days Counter - Mobile -->
        <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2.5 shadow-sm">
            <button type="button" 
                    @click="decreaseDays()" 
                    x-show="days > 1"
                    class="w-8 h-8 bg-gradient-to-br from-purple-600 to-indigo-700 text-white rounded-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-purple-500 flex items-center justify-center shadow-sm transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                </svg>
            </button>
            
            <div class="flex-1 text-center">
                <div class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent" x-text="days"></div>
                <div class="text-xs font-medium text-purple-600 uppercase tracking-wider">Days</div>
            </div>
            
            <button type="button" 
                    @click="increaseDays()" 
                    x-show="canIncreaseDays()"
                    class="w-8 h-8 bg-gradient-to-br from-purple-600 to-indigo-700 text-white rounded-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-purple-500 flex items-center justify-center shadow-sm transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </button>
        </div>

        <!-- Nights & Guests - Mobile -->
        <div class="grid grid-cols-2 gap-3">
            <!-- Nights -->
            <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2.5 shadow-sm">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider truncate">Nights</div>
                    <div class="text-xl font-bold text-gray-800" x-text="nights"></div>
                </div>
            </div>

            <!-- Total Guests -->
            <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2.5 shadow-sm">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 01 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider truncate">Guests</div>
                    <div class="text-xl font-bold text-gray-800" x-text="totalGuests"></div>
                </div>
            </div>
        </div>
    </div>
</div>
