<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="p-3 sm:p-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">Recent Bookings</h3>
    </div>
    
    <div class="divide-y divide-gray-100">
        <template x-for="booking in recentBookings" :key="booking.id">
            <div class="p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm"
                         :class="booking.status === 'confirmed' ? 'bg-green-100 text-green-600' : 
                                booking.status === 'pending' ? 'bg-yellow-100 text-yellow-600' :
                                booking.status === 'checked_in' ? 'bg-blue-100 text-blue-600' :
                                'bg-gray-100 text-gray-600'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate" x-text="booking.guest ? booking.guest.name : 'Guest' + ' - ' + (booking.accommodation ? booking.accommodation.property.name : 'Property')"></p>
                        <p class="text-xs text-gray-500" x-text="'Check-in: ' + formatDate(booking.check_in_date) + ' | Status: ' + booking.status.charAt(0).toUpperCase() + booking.status.slice(1)"></p>
                    </div>
                </div>
            </div>
        </template>
        <div x-show="recentBookings.length === 0" class="p-3 sm:p-4 text-center text-gray-500">
            <p>No recent bookings found</p>
        </div>
    </div>
</div>