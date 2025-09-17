<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="divide-y divide-gray-100">
        <template x-for="customer in filteredCustomers" :key="customer.id">
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                            <span x-text="customer.name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800" x-text="customer.name"></h3>
                            <p class="text-sm text-gray-500" x-text="customer.email"></p>
                            <p class="text-sm text-gray-500" x-text="customer.phone"></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-xs font-medium"
                              :class="customer.status === 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'"
                              x-text="customer.status.charAt(0).toUpperCase() + customer.status.slice(1)"></span>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-800" x-text="customer.totalBookings + ' bookings'"></div>
                            <div class="text-xs text-gray-500" x-text="'Last: ' + customer.lastBooking"></div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>