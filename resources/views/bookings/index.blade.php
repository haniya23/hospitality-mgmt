@extends('layouts.app')

@section('title', 'Booking Management')

@section('header')
    <div x-data="bookingData()" x-init="init()">
        @include('partials.bookings.header')
    </div>
@endsection

@section('content')
<div x-data="bookingData()" x-init="init()" class="space-y-6">
    @include('partials.bookings.filters')
    @include('partials.bookings.list')
    
    <!-- Cancel Booking Modal -->
    <div x-show="showCancelModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 overflow-y-auto"
         style="z-index: 99999 !important;"
         x-cloak
         style="background-color: rgba(0,0,0,0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" @click="closeCancelModal()">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" @click.stop>
                <form @submit.prevent="cancelBooking()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Booking</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Are you sure you want to cancel this booking? This action cannot be undone.</p>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason</label>
                                    <select x-model="cancelReason" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                                        <option value="">Select a reason</option>
                                        <option value="Guest Request">Guest Request</option>
                                        <option value="No Show">No Show</option>
                                        <option value="Property Issue">Property Issue</option>
                                        <option value="Weather Conditions">Weather Conditions</option>
                                        <option value="Emergency">Emergency</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                                    <textarea x-model="cancelDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Provide additional details..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                :disabled="!cancelReason">
                            Cancel Booking
                        </button>
                        <button type="button" 
                                @click="closeCancelModal()" 
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Keep Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Confirm Modal -->
    <div x-show="showConfirmModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 overflow-y-auto"
         style="z-index: 99999 !important;"
         x-cloak
         style="background-color: rgba(0,0,0,0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" @click="closeConfirmModal()">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" @click.stop>
                <form @submit.prevent="confirmBooking()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Booking</h3>
                                
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                    <div class="text-sm text-gray-600 mb-1">Guest: <span x-text="selectedBooking?.guest?.name"></span></div>
                                    <div class="text-sm text-gray-600 mb-1">Property: <span x-text="selectedBooking?.accommodation?.property?.name"></span></div>
                                    <div class="text-sm text-gray-600">Check-in: <span x-text="formatDate(selectedBooking?.check_in_date)"></span></div>
                                    <div class="text-sm text-gray-600">Check-out: <span x-text="formatDate(selectedBooking?.check_out_date)"></span></div>
                                </div>
                                
                                <div class="mt-4 grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Rate (₹/day)</label>
                                        <input type="number" x-model="confirmRate" step="0.01" min="0" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Set Amount (₹)</label>
                                        <input type="number" x-model="confirmAmount" step="0.01" min="0" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Advance Paid (₹)</label>
                                    <input type="number" x-model="confirmAdvance" step="0.01" min="0" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                </div>
                                
                                <div class="mt-4 p-3 bg-green-50 rounded-lg">
                                    <div class="text-sm text-gray-600">Balance Pending: <span class="font-semibold text-green-600" x-text="'₹' + (confirmAmount - confirmAdvance).toLocaleString()"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm Booking
                        </button>
                        <button type="button" 
                                @click="closeConfirmModal()" 
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Invoice Download Modal -->
    <div x-show="showInvoiceModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 overflow-y-auto"
         style="z-index: 99999 !important;"
         x-cloak
         style="background-color: rgba(0,0,0,0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" @click="closeInvoiceModal()">
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full" @click.stop>
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                                <i class="fas fa-file-pdf text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Download Invoice</h3>
                                <p class="text-sm text-gray-600">Get your booking invoice</p>
                            </div>
                        </div>
                        <button @click="closeInvoiceModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    
                    <div x-show="selectedInvoiceBooking" class="bg-gray-50 rounded-xl p-4 mb-6">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                                <span x-text="selectedInvoiceBooking?.guest?.name?.charAt(0)?.toUpperCase()"></span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900" x-text="selectedInvoiceBooking?.guest?.name"></h4>
                                <p class="text-sm text-gray-600" x-text="selectedInvoiceBooking?.confirmation_number"></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Property:</span>
                                <p class="font-medium" x-text="selectedInvoiceBooking?.accommodation?.property?.name"></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Amount:</span>
                                <p class="font-medium text-green-600" x-text="'₹' + formatNumber(selectedInvoiceBooking?.total_amount)"></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Check-in:</span>
                                <p class="font-medium" x-text="formatDate(selectedInvoiceBooking?.check_in_date)"></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Check-out:</span>
                                <p class="font-medium" x-text="formatDate(selectedInvoiceBooking?.check_out_date)"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex space-x-3">
                    <button @click="downloadInvoice(selectedInvoiceBooking)" 
                            class="flex-1 bg-purple-600 text-white py-3 px-4 rounded-xl font-medium text-sm hover:bg-purple-700 transition flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Download PDF
                    </button>
                    <button @click="shareToWhatsApp(selectedInvoiceBooking)" 
                            class="flex-1 bg-green-500 text-white py-3 px-4 rounded-xl font-medium text-sm hover:bg-green-600 transition flex items-center justify-center">
                        <i class="fab fa-whatsapp mr-2"></i>
                        Share
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Download Modal -->
    <div x-show="showBulkDownloadModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 overflow-y-auto"
         style="z-index: 99999 !important;"
         x-cloak
         style="background-color: rgba(0,0,0,0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" @click="closeBulkDownloadModal()">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" @click.stop>
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-download text-purple-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Bulk Invoice Download</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Select which booking statuses to include in the bulk invoice download.</p>
                            </div>
                            
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center">
                                    <input type="checkbox" x-model="bulkDownloadOptions.all" @change="toggleAllStatuses()" 
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <label class="ml-3 text-sm font-medium text-gray-700">All Bookings</label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" x-model="bulkDownloadOptions.pending" 
                                           class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                    <label class="ml-3 text-sm font-medium text-gray-700">Pending Bookings</label>
                                    <span class="ml-2 text-xs text-gray-500" x-text="'(' + bookingStats.pending + ')'"></span>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" x-model="bulkDownloadOptions.confirmed" 
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <label class="ml-3 text-sm font-medium text-gray-700">Confirmed Bookings</label>
                                    <span class="ml-2 text-xs text-gray-500" x-text="'(' + bookingStats.confirmed + ')'"></span>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" x-model="bulkDownloadOptions.cancelled" 
                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    <label class="ml-3 text-sm font-medium text-gray-700">Cancelled Bookings</label>
                                    <span class="ml-2 text-xs text-gray-500" x-text="'(' + bookingStats.cancelled + ')'"></span>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Selected bookings will be compiled into a single PDF document with all invoice details.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="downloadBulkInvoices()" 
                            :disabled="!hasSelectedStatuses()"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-download mr-2"></i>
                        Download Invoices
                    </button>
                    <button @click="closeBulkDownloadModal()" 
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Display -->
    <div x-show="message" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" class="fixed top-4 right-4 z-50 max-w-sm w-full" style="display: none;">
        <div class="rounded-lg shadow-lg p-4" :class="messageType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'">
            <div class="flex items-center">
                <i :class="messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'" class="mr-2"></i>
                <span x-text="message"></span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bookingData() {
    return {
        statusFilter: '',
        dateFilter: '',
        selectedProperty: '',
        showCancelModal: false,
        cancelBookingId: null,
        cancelReason: '',
        cancelDescription: '',
        showConfirmModal: false,
        selectedBooking: null,
        confirmRate: 0,
        confirmAmount: 0,
        confirmAdvance: 0,
        showInvoiceModal: false,
        selectedInvoiceBooking: null,
        showBulkDownloadModal: false,
        bulkDownloadOptions: {
            all: false,
            pending: false,
            confirmed: false,
            cancelled: false
        },
        message: '',
        messageType: 'success',
        bookings: [],
        properties: [],

        get filteredBookings() {
            return this.bookings.filter(booking => {
                const matchesStatus = !this.statusFilter || booking.status === this.statusFilter;
                const matchesProperty = !this.selectedProperty || booking.accommodation.property.name.includes(this.selectedProperty);
                return matchesStatus && matchesProperty;
            });
        },

        get bookingStats() {
            const all = this.bookings.length;
            const pending = this.bookings.filter(b => b.status === 'pending').length;
            const confirmed = this.bookings.filter(b => b.status === 'confirmed').length;
            const cancelled = this.bookings.filter(b => b.status === 'cancelled').length;
            
            return { all, pending, confirmed, cancelled };
        },

        async init() {
            await this.loadBookings();
            await this.loadProperties();
        },

        async loadBookings() {
            try {
                const response = await fetch('/api/bookings');
                const data = await response.json();
                this.bookings = [...data.pending, ...data.active, ...data.cancelled];
            } catch (error) {
                // Error loading bookings
                this.showMessage('Error loading bookings', 'error');
            }
        },

        async loadProperties() {
            try {
                const response = await fetch('/api/properties');
                this.properties = await response.json();
            } catch (error) {
                // Error loading properties
            }
        },

        calculateNights(checkIn, checkOut) {
            if (checkIn && checkOut) {
                const start = new Date(checkIn);
                const end = new Date(checkOut);
                const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                return diff > 0 ? diff : 0;
            }
            return 0;
        },

        formatDate(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            const days = ['SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'];
            
            const month = months[date.getMonth()];
            const day = date.getDate().toString().padStart(2, '0');
            const dayName = days[date.getDay()];
            const year = date.getFullYear();
            
            return `${month} - ${day} - ${dayName} - ${year}`;
        },


        openConfirmModal(booking) {
            this.selectedBooking = booking;
            this.confirmRate = booking.accommodation?.base_price || 0;
            this.confirmAmount = booking.total_amount || 0;
            this.confirmAdvance = booking.advance_paid || 0;
            this.showConfirmModal = true;
        },

        closeConfirmModal() {
            this.showConfirmModal = false;
            this.selectedBooking = null;
            this.confirmRate = 0;
            this.confirmAmount = 0;
            this.confirmAdvance = 0;
        },

        async confirmBooking() {
            try {
                const response = await fetch(`/api/bookings/${this.selectedBooking.uuid}/confirm`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        rate: this.confirmRate,
                        total_amount: this.confirmAmount,
                        advance_paid: this.confirmAdvance
                    })
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadBookings();
                    this.closeConfirmModal();
                    this.showMessage(data.message, 'success');
                } else {
                    this.showMessage(data.message, 'error');
                }
            } catch (error) {
                this.showMessage('Error confirming booking', 'error');
            }
        },

        openCancelModal(bookingUuid) {
            this.cancelBookingId = bookingUuid;
            this.showCancelModal = true;
            this.cancelReason = '';
            this.cancelDescription = '';
        },

        closeCancelModal() {
            this.showCancelModal = false;
            this.cancelBookingId = null;
        },

        async cancelBooking() {
            if (!this.cancelReason) {
                this.showMessage('Please select a reason for cancellation.', 'error');
                return;
            }

            try {
                const response = await fetch(`/api/bookings/${this.cancelBookingId}/cancel`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        reason: this.cancelReason,
                        description: this.cancelDescription
                    })
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadBookings();
                    this.showMessage(data.message, 'success');
                    this.closeCancelModal();
                } else {
                    this.showMessage(data.message, 'error');
                }
            } catch (error) {
                this.showMessage('Error cancelling booking', 'error');
            }
        },

        showMessage(msg, type = 'success') {
            this.message = msg;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
            }, 5000);
        },

        formatNumber(num) {
            if (num === null || num === undefined) return '0';
            return new Intl.NumberFormat('en-IN').format(num);
        },

        formatDateRange(checkIn, checkOut) {
            const options = { month: 'short', day: 'numeric' };
            const start = new Date(checkIn).toLocaleDateString('en-GB', options);
            const end = new Date(checkOut).toLocaleDateString('en-GB', options);
            return `${start} - ${end}`;
        },

        shareToWhatsApp(booking) {
            const invoiceUrl = `${window.location.origin}/public/invoice/${booking.uuid}`;
            const message = `🏨 *Booking Invoice*\n\n` +
                `📋 *Confirmation:* ${booking.confirmation_number}\n` +
                `👤 *Guest:* ${booking.guest.name}\n` +
                `🏠 *Property:* ${booking.accommodation.property.name}\n` +
                `📅 *Check-in:* ${this.formatDate(booking.check_in_date)}\n` +
                `📅 *Check-out:* ${this.formatDate(booking.check_out_date)}\n` +
                `💰 *Amount:* ₹${this.formatNumber(booking.total_amount)}\n` +
                `📱 *Contact:* ${booking.guest.mobile_number}\n\n` +
                `📄 *Invoice PDF:* ${invoiceUrl}\n\n` +
                `Thank you for choosing our hospitality services! 🎉`;
            
            const whatsappUrl = `https://wa.me/send?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
            
            // Close the modal after sharing
            this.closeInvoiceModal();
            this.showMessage('WhatsApp share opened!', 'success');
        },

        openInvoiceModal(booking) {
            this.selectedInvoiceBooking = booking;
            this.showInvoiceModal = true;
        },

        closeInvoiceModal() {
            this.showInvoiceModal = false;
            this.selectedInvoiceBooking = null;
        },

        downloadInvoice(booking) {
            const downloadUrl = `/bookings/${booking.uuid}/invoice/download`;
            
            // Create a temporary iframe for silent download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = downloadUrl;
            document.body.appendChild(iframe);
            
            // Remove iframe after download starts
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);
            
            // Close the modal after starting download
            this.closeInvoiceModal();
            this.showMessage('Invoice download started!', 'success');
        },

        // Bulk Download Functions
        openBulkDownloadModal() {
            this.showBulkDownloadModal = true;
            this.resetBulkDownloadOptions();
        },

        closeBulkDownloadModal() {
            this.showBulkDownloadModal = false;
            this.resetBulkDownloadOptions();
        },

        resetBulkDownloadOptions() {
            this.bulkDownloadOptions = {
                all: false,
                pending: false,
                confirmed: false,
                cancelled: false
            };
        },

        toggleAllStatuses() {
            if (this.bulkDownloadOptions.all) {
                this.bulkDownloadOptions.pending = true;
                this.bulkDownloadOptions.confirmed = true;
                this.bulkDownloadOptions.cancelled = true;
            } else {
                this.bulkDownloadOptions.pending = false;
                this.bulkDownloadOptions.confirmed = false;
                this.bulkDownloadOptions.cancelled = false;
            }
        },

        hasSelectedStatuses() {
            return this.bulkDownloadOptions.pending || 
                   this.bulkDownloadOptions.confirmed || 
                   this.bulkDownloadOptions.cancelled;
        },

        downloadBulkInvoices() {
            if (!this.hasSelectedStatuses()) {
                this.showMessage('Please select at least one booking status to download.', 'error');
                return;
            }

            const selectedStatuses = [];
            if (this.bulkDownloadOptions.pending) selectedStatuses.push('pending');
            if (this.bulkDownloadOptions.confirmed) selectedStatuses.push('confirmed');
            if (this.bulkDownloadOptions.cancelled) selectedStatuses.push('cancelled');

            const params = new URLSearchParams();
            selectedStatuses.forEach(status => params.append('statuses[]', status));

            const downloadUrl = `/bookings/bulk-invoice/download?${params.toString()}`;
            
            // Create a temporary iframe for silent download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = downloadUrl;
            document.body.appendChild(iframe);
            
            // Remove iframe after download starts
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);
            
            this.closeBulkDownloadModal();
            this.showMessage('Bulk invoice download started successfully!', 'success');
        },

        // Navigation functions for clickable stats
        navigateToAllBookings() {
            window.location.href = '/bookings';
        },

        navigateToPendingBookings() {
            window.location.href = '/bookings?status=pending';
        },

        navigateToConfirmedBookings() {
            window.location.href = '/bookings?status=confirmed';
        },

        navigateToCancelledBookings() {
            window.location.href = '/bookings-cancelled';
        }
    }
}
</script>
@endpush