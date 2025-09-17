<!-- Booking Modal -->
<div x-show="showBookingModal" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-black/40">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl max-h-[95vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 pb-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-t-2xl border-b border-emerald-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-plus text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">New Booking</h3>
                        <p class="text-sm text-emerald-600 font-medium">Create a new reservation</p>
                    </div>
                </div>
                <button @click="closeBookingModal()" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Form -->
            <div class="flex-1 overflow-y-auto p-6">
                <form @submit.prevent="saveBooking()" class="space-y-6">
                    <!-- Property & Accommodation -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Property</label>
                            <select x-model="booking.property_id" @change="loadAccommodations()" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                <option value="">Select Property</option>
                                <template x-for="property in properties" :key="property.id">
                                    <option :value="property.id" x-text="property.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Accommodation</label>
                            <select x-model="booking.accommodation_id" @change="calculateRate()" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                <option value="">Select Accommodation</option>
                                <template x-for="acc in accommodations" :key="acc.id">
                                    <option :value="acc.id" x-text="acc.display_name + ' (₹' + acc.base_price + '/night)'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in</label>
                            <input type="date" x-model="booking.check_in_date" @change="calculateRate()" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out</label>
                            <input type="date" x-model="booking.check_out_date" @change="calculateRate()" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                        </div>
                    </div>

                    <!-- Guests -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Adults</label>
                            <input type="number" x-model="booking.adults" min="1" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Children</label>
                            <input type="number" x-model="booking.children" min="0" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                        </div>
                    </div>

                    <!-- Customer -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-semibold text-gray-700">Customer</label>
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button type="button" @click="createNewGuest = false" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="!createNewGuest ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                    Select
                                </button>
                                <button type="button" @click="createNewGuest = true" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="createNewGuest ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                    Create
                                </button>
                            </div>
                        </div>

                        <div x-show="createNewGuest">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-xl">
                                <input type="text" x-model="booking.guest_name" placeholder="Full Name" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                <input type="tel" x-model="booking.guest_mobile" placeholder="Mobile Number" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                <input type="email" x-model="booking.guest_email" placeholder="Email (Optional)" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                            </div>
                        </div>

                        <div x-show="!createNewGuest">
                            <select x-model="booking.guest_id" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                <option value="">Select Customer</option>
                                <template x-for="guest in guests" :key="guest.id">
                                    <option :value="guest.id" x-text="guest.name + ' (' + guest.mobile_number + ')'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                            <input type="number" x-model="booking.total_amount" step="0.01" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Advance Paid</label>
                            <input type="number" x-model="booking.advance_paid" step="0.01" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Balance</label>
                            <input type="number" :value="booking.total_amount - booking.advance_paid" readonly class="w-full border border-gray-200 rounded-xl py-3 px-4 bg-gray-50">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex gap-3 pt-4 border-t">
                        <button type="button" @click="closeBookingModal()" class="flex-1 py-3 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700">
                            Create Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div x-show="showCancelModal" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-black/50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancel Booking</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <select x-model="cancelReason" class="w-full border border-gray-300 rounded-xl px-3 py-2">
                        <option value="">Select reason</option>
                        <option value="Guest Request">Guest Request</option>
                        <option value="No Show">No Show</option>
                        <option value="Payment Issue">Payment Issue</option>
                        <option value="Property Issue">Property Issue</option>
                        <option value="Overbooking">Overbooking</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea x-model="cancelDescription" rows="3" class="w-full border border-gray-300 rounded-xl px-3 py-2" placeholder="Optional description..."></textarea>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button @click="closeCancelModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button @click="cancelBooking()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">
                    Confirm Cancel
                </button>
            </div>
        </div>
    </div>
</div>