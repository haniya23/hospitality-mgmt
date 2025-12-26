@push('styles')
<style>
    .completed-booking-card { 
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); 
        transition: all 0.3s ease; 
        border: 1px solid #e5e7eb;
    }
    .completed-booking-card:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
        border-color: #d1d5db;
    }
    .status-completed { 
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); 
        color: #059669; 
        border: 1px solid #6ee7b7;
    }
</style>
@endpush

<div class="space-y-6">
    <template x-for="booking in filteredBookings" :key="booking.id">
        <div class="completed-booking-card rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <!-- Booking Header -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center text-white font-bold">
                        <span x-text="booking.guest.name.charAt(0).toUpperCase()"></span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900" x-text="booking.guest.name"></h3>
                        <p class="text-sm text-gray-500" x-text="booking.guest.email"></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium status-completed">
                        <i class="fas fa-check mr-1"></i>
                        Completed
                    </span>
                    <p class="text-sm text-gray-500 mt-1" x-show="booking.check_out_record" x-text="booking.check_out_record ? 'Checked out ' + new Date(booking.check_out_record.check_out_time).toLocaleDateString() : ''"></p>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Property</div>
                    <div class="font-medium text-gray-900" x-text="booking.accommodation.property.name"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Accommodation</div>
                    <div class="font-medium text-gray-900" x-text="booking.accommodation.custom_name"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Check-in</div>
                    <div class="font-medium text-gray-900" x-text="new Date(booking.check_in_date).toLocaleDateString()"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Check-out</div>
                    <div class="font-medium text-gray-900" x-text="new Date(booking.check_out_date).toLocaleDateString()"></div>
                </div>
            </div>

            <!-- B2B Partner Badge (if applicable) -->
            <div x-show="booking.b2b_partner" class="bg-blue-50 rounded-lg p-3 mb-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-handshake text-blue-600"></i>
                    <div>
                        <span class="text-sm font-medium text-blue-800">B2B Booking</span>
                        <span class="text-sm text-blue-600 ml-2" x-text="booking.b2b_partner ? '- ' + booking.b2b_partner.partner_name : ''"></span>
                    </div>
                </div>
            </div>

            <!-- Amount Information -->
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-gray-500">
                    <span>Total Amount: </span>
                    <span class="font-medium text-gray-900" x-text="'₹' + booking.total_amount"></span>
                </div>
                <div class="text-sm text-gray-500" x-show="booking.check_out_record">
                    <span>Final Bill: </span>
                    <span class="font-medium text-green-600" x-text="booking.check_out_record ? '₹' + booking.check_out_record.final_bill : 'N/A'"></span>
                </div>
            </div>
            
            <!-- Balance Due Warning -->
            <div x-show="booking.balance_pending > 0" class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-red-800">Balance Due:</span>
                        <span class="text-lg font-bold text-red-600 ml-2" x-text="'₹' + booking.balance_pending"></span>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Payment Pending
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a :href="`/bookings/${booking.uuid}`" 
                   class="flex-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white py-3 px-6 rounded-xl font-semibold text-sm hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                    <i class="fas fa-eye"></i>
                    <span>View Details</span>
                </a>
                <button x-show="booking.balance_pending > 0" 
                        @click="openPaymentModal(booking)"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-3 px-6 rounded-xl font-semibold text-sm hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Collect Payment</span>
                </button>
            </div>
        </div>
    </template>
    
    <template x-if="filteredBookings.length === 0">
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-200 p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-green-400 to-emerald-500 rounded-3xl flex items-center justify-center text-white text-4xl shadow-lg">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Completed Bookings Found</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">There are no completed bookings matching your current search criteria. Try adjusting your filters or search terms.</p>
            <button @click="search = ''; selectedProperty = ''; filterBookings();" 
                    class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
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
            Showing <span class="font-bold text-green-600" x-text="from"></span> to <span class="font-bold text-green-600" x-text="to"></span> of <span class="font-bold text-green-600" x-text="total"></span> results
        </div>
        
        <div class="flex items-center space-x-2">
            <template x-for="link in paginationLinks" :key="link.page">
                <button @click="goToPage(link.page)" 
                        :disabled="link.disabled"
                        :class="link.active ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'"
                        class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-md">
                    <span x-text="link.label"></span>
                </button>
            </template>
        </div>
    </div>
</div>
