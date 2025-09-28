<div class="grid grid-cols-2 gap-4">
    <div class="bg-gradient-to-br from-yellow-100 to-orange-100 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-yellow-200" 
         @click="navigateToPendingBookings()">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full font-medium">Pending</span>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="pendingBookings.length"></div>
        <div class="text-sm text-gray-600">Pending Bookings</div>
    </div>

    <div class="bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-green-200" 
         @click="navigateToActiveBookings()">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full font-medium">Active</span>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="activeBookings.length"></div>
        <div class="text-sm text-gray-600">Active Bookings</div>
    </div>
</div>