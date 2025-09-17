@push('styles')
<style>
    .booking-card { background: white; transition: all 0.3s ease; }
    .booking-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-confirmed { background: #d1fae5; color: #059669; }
    .status-cancelled { background: #fee2e2; color: #dc2626; }
</style>
@endpush

<div class="space-y-4">
    <template x-for="booking in filteredBookings" :key="booking.id">
        <div class="booking-card rounded-2xl p-4 shadow-sm">
            <!-- Booking Header -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                        <span x-text="booking.guest.name.charAt(0).toUpperCase()"></span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800" x-text="booking.guest.name"></h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 rounded-full font-medium"
                                  :class="booking.status === 'pending' ? 'status-pending' : 
                                         booking.status === 'confirmed' ? 'status-confirmed' : 
                                         'status-cancelled'"
                                  x-text="booking.status.charAt(0).toUpperCase() + booking.status.slice(1)"></span>
                            <span x-show="booking.b2b_partner" 
                                  class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded-full font-medium">
                                B2B
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-gray-800" x-text="'₹' + formatNumber(booking.total_amount)"></div>
                    <div x-show="booking.balance_pending > 0" 
                         class="text-xs text-orange-600 font-medium" 
                         x-text="'₹' + formatNumber(booking.balance_pending) + ' pending'"></div>
                </div>
            </div>

            <!-- Property Details -->
            <div class="bg-gray-50 rounded-xl p-3 mb-3">
                <div class="flex items-center space-x-2 mb-2">
                    <i class="fas fa-map-marker-alt text-gray-500 text-sm"></i>
                    <span class="text-sm font-medium text-gray-700" 
                          x-text="booking.accommodation.property.name + ' - ' + booking.accommodation.display_name"></span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-calendar-alt text-gray-500 text-sm"></i>
                    <span class="text-sm text-gray-600" 
                          x-text="formatDateRange(booking.check_in_date, booking.check_out_date)"></span>
                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full" 
                          x-text="calculateNights(booking.check_in_date, booking.check_out_date) + ' nights'"></span>
                </div>
            </div>

            <!-- Guest Info -->
            <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                <span x-text="booking.adults + ' adults' + (booking.children > 0 ? ', ' + booking.children + ' children' : '')"></span>
                <span x-text="booking.guest.mobile_number"></span>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <template x-if="booking.status === 'pending'">
                    <div class="flex space-x-2 w-full">
                        <button @click="toggleBookingStatus(booking.id)" 
                                class="flex-1 bg-green-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-green-600 transition">
                            <i class="fas fa-check mr-1"></i>
                            Confirm
                        </button>
                        <button @click="openCancelModal(booking.id)" 
                                class="flex-1 bg-red-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-red-600 transition">
                            <i class="fas fa-times mr-1"></i>
                            Cancel
                        </button>
                    </div>
                </template>
                <template x-if="booking.status !== 'pending'">
                    <div class="flex space-x-2 w-full">
                        <button class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-blue-600 transition">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </button>
                        <button @click="openCancelModal(booking.id)" 
                                class="bg-red-100 text-red-600 py-2 px-4 rounded-xl font-medium text-sm hover:bg-red-200 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </template>
    
    <template x-if="filteredBookings.length === 0">
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Bookings Found</h3>
            <p class="text-gray-500 text-sm mb-4">Start by creating your first booking.</p>
            <button @click="openBookingModal()" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl font-medium">
                <i class="fas fa-plus mr-2"></i>Create Booking
            </button>
        </div>
    </template>
</div>