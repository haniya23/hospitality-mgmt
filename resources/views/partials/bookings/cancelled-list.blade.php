@push('styles')
<style>
    .cancelled-booking-card { background: white; transition: all 0.3s ease; }
    .cancelled-booking-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .status-cancelled { background: #fee2e2; color: #dc2626; }
</style>
@endpush

<div class="space-y-4">
    <template x-for="booking in filteredBookings" :key="booking.id">
        <div class="cancelled-booking-card rounded-2xl p-4 shadow-sm">
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
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <span>Total Amount: </span>
                    <span class="font-medium text-gray-900" x-text="'₹' + booking.reservation.total_amount"></span>
                </div>
                <div class="text-sm text-gray-500">
                    <span>Refund Amount: </span>
                    <span class="font-medium text-green-600" x-text="'₹' + (booking.refund_amount || 0)"></span>
                </div>
            </div>
        </div>
    </template>
    
    <template x-if="filteredBookings.length === 0">
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-red-400 to-pink-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                <i class="fas fa-times"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No cancelled bookings found</h3>
            <p class="text-gray-500">There are no cancelled bookings matching your criteria.</p>
        </div>
    </template>
</div>

<!-- Pagination -->
<div x-show="lastPage > 1" class="mt-8 flex items-center justify-between">
    <div class="text-sm text-gray-700">
        Showing <span x-text="from"></span> to <span x-text="to"></span> of <span x-text="total"></span> results
    </div>
    
    <div class="flex items-center space-x-2">
        <template x-for="link in paginationLinks" :key="link.page">
            <button @click="goToPage(link.page)" 
                    :disabled="link.disabled"
                    :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-text="link.label"></span>
            </button>
        </template>
    </div>
</div>
