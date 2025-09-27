<div class="grid grid-cols-2 gap-4">
    <div class="bg-gradient-to-br from-orange-100 to-amber-100 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-orange-200" 
         @click="navigateToRevenue('today')">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full font-medium">+12%</span>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="'₹' + formatNumber(revenue.today)"></div>
        <div class="text-sm text-gray-600">Today's Revenue</div>
    </div>

    <div class="bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-blue-200" 
         @click="navigateToRevenue('month')">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-medium">+8%</span>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="'₹' + formatNumber(revenue.month)"></div>
        <div class="text-sm text-gray-600">This Month</div>
    </div>
</div>