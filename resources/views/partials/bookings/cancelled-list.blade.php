@push('styles')
<style>
    .cancelled-booking-card { 
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); 
        transition: all 0.3s ease; 
        border: 1px solid #e5e7eb;
    }
    .cancelled-booking-card:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
        border-color: #d1d5db;
    }
    .status-cancelled { 
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); 
        color: #dc2626; 
        border: 1px solid #fca5a5;
    }
</style>
@endpush

<div class="space-y-6">
    <template x-for="booking in filteredBookings" :key="booking.id">
        <div class="cancelled-booking-card rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <!-- Booking Header -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-red-500 to-pink-500 flex items-center justify-center text-white font-bold">
                        <span x-text="booking.reservation.guest.name.charAt(0).toUpperCase()"></span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900" x-text="booking.reservation.guest.name"></h3>
                        <p class="text-sm text-gray-500" x-text="booking.reservation.guest.email"></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium status-cancelled">
                        <i class="fas fa-times mr-1"></i>
                        Cancelled
                    </span>
                    <p class="text-sm text-gray-500 mt-1" x-text="'Cancelled on ' + new Date(booking.cancelled_at).toLocaleDateString()"></p>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Property</div>
                    <div class="font-medium text-gray-900" x-text="booking.reservation.accommodation.property.name"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Accommodation</div>
                    <div class="font-medium text-gray-900" x-text="booking.reservation.accommodation.custom_name"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Check-in</div>
                    <div class="font-medium text-gray-900" x-text="new Date(booking.reservation.check_in_date).toLocaleDateString()"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Check-out</div>
                    <div class="font-medium text-gray-900" x-text="new Date(booking.reservation.check_out_date).toLocaleDateString()"></div>
                </div>
            </div>

            <!-- Cancellation Details -->
            <div class="bg-red-50 rounded-lg p-4 mb-4">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-red-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-red-800 mb-2">Cancellation Details</h4>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-red-700">Reason: </span>
                                <span class="text-sm text-red-600" x-text="booking.reason || 'Not specified'"></span>
                            </div>
                            <div x-show="booking.description">
                                <span class="text-sm font-medium text-red-700">Description: </span>
                                <span class="text-sm text-red-600" x-text="booking.description"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Information -->
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-gray-500">
                    <span>Total Amount: </span>
                    <span class="font-medium text-gray-900" x-text="'₹' + booking.reservation.total_amount"></span>
                </div>
                <div class="text-sm text-gray-500">
                    <span>Refund Amount: </span>
                    <span class="font-medium text-green-600" x-text="'₹' + (booking.refund_amount || 0)"></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button @click="reactivateBooking(booking.reservation.uuid, 'pending')" 
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-amber-500 text-white py-3 px-6 rounded-xl font-semibold text-sm hover:from-yellow-600 hover:to-amber-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                    <i class="fas fa-clock"></i>
                    <span>Reactivate as Pending</span>
                </button>
                <button @click="reactivateBooking(booking.reservation.uuid, 'confirmed')" 
                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white py-3 px-6 rounded-xl font-semibold text-sm hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                    <i class="fas fa-check"></i>
                    <span>Reactivate as Confirmed</span>
                </button>
            </div>
        </div>
    </template>
    
    <template x-if="filteredBookings.length === 0">
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-200 p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-red-400 to-pink-500 rounded-3xl flex items-center justify-center text-white text-4xl shadow-lg">
                <i class="fas fa-times-circle"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Cancelled Bookings Found</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">There are no cancelled bookings matching your current search criteria. Try adjusting your filters or search terms.</p>
            <button @click="search = ''; selectedProperty = ''; filterBookings();" 
                    class="bg-gradient-to-r from-red-600 to-pink-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                <i class="fas fa-refresh mr-2"></i>
                Clear Filters
            </button>
        </div>
    </template>
</div>

<!-- Pagination -->
<div x-show="lastPage > 1" class="mt-8 bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-6">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm font-medium text-gray-700">
            Showing <span class="font-bold text-red-600" x-text="from"></span> to <span class="font-bold text-red-600" x-text="to"></span> of <span class="font-bold text-red-600" x-text="total"></span> results
        </div>
        
        <div class="flex items-center space-x-2">
            <template x-for="link in paginationLinks" :key="link.page">
                <button @click="goToPage(link.page)" 
                        :disabled="link.disabled"
                        :class="link.active ? 'bg-gradient-to-r from-red-600 to-pink-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'"
                        class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-md">
                    <span x-text="link.label"></span>
                </button>
            </template>
        </div>
    </div>
</div>
