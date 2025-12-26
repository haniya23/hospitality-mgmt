@extends('layouts.app')

@section('title', 'Completed Bookings')

@section('header')
<div x-data="completedBookingData()" x-init="init()">
    @include('partials.bookings.completed-header')
</div>
@endsection

@section('content')
<div x-data="completedBookingData()" x-init="init()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    @include('partials.bookings.completed-filters')
    @include('partials.bookings.completed-list')
    
    <!-- Payment Collection Modal -->
    <div x-show="showPaymentModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 overflow-y-auto z-50"
         style="background-color: rgba(0,0,0,0.5);"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" @click="closePaymentModal()">
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" @click.stop>
                <form @submit.prevent="collectPayment()">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Collect Payment</h3>
                                    <p class="text-sm text-gray-600" x-text="selectedBooking ? selectedBooking.guest.name : ''"></p>
                                </div>
                            </div>
                            <button type="button" @click="closePaymentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        
                        <div class="bg-red-50 rounded-xl p-4 mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-red-800">Current Balance:</span>
                                <span class="text-2xl font-bold text-red-600" x-text="selectedBooking ? '₹' + selectedBooking.balance_pending : '₹0'"></span>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount to Collect *</label>
                                <input type="number" 
                                       x-model="paymentAmount" 
                                       step="0.01" 
                                       min="0" 
                                       :max="selectedBooking ? selectedBooking.balance_pending : 0"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                       placeholder="Enter amount"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Notes (Optional)</label>
                                <textarea x-model="paymentNotes" 
                                          rows="3" 
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                          placeholder="Add any notes about this payment..."></textarea>
                            </div>
                            
                            <div class="bg-blue-50 rounded-lg p-3" x-show="paymentAmount > 0">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-blue-800">Remaining Balance:</span>
                                    <span class="font-bold text-blue-600" x-text="'₹' + (selectedBooking ? Math.max(0, selectedBooking.balance_pending - paymentAmount) : 0)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex space-x-3">
                        <button type="button" 
                                @click="closePaymentModal()" 
                                class="flex-1 bg-white text-gray-700 py-3 px-4 rounded-xl font-medium text-sm hover:bg-gray-100 transition border border-gray-300">
                            Cancel
                        </button>
                        <button type="submit" 
                                :disabled="!paymentAmount || paymentAmount <= 0"
                                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-blue-700 hover:to-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                            <i class="fas fa-check mr-2"></i>
                            Collect Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function completedBookingData() {
    return {
        completedBookings: @json($completedBookings->items() ?? []),
        properties: @json($properties ?? []),
        filteredBookings: [],
        search: '',
        selectedProperty: '',
        currentPage: {{ $completedBookings->currentPage() }},
        lastPage: {{ $completedBookings->lastPage() }},
        total: {{ $completedBookings->total() }},
        perPage: {{ $completedBookings->perPage() }},
        from: {{ $completedBookings->firstItem() ?? 0 }},
        to: {{ $completedBookings->lastItem() ?? 0 }},
        showPaymentModal: false,
        selectedBooking: null,
        paymentAmount: 0,
        paymentNotes: '',

        init() {
            this.filteredBookings = this.completedBookings;
            this.loadProperties();
        },

        async loadProperties() {
            try {
                const response = await fetch('/api/properties');
                const data = await response.json();
                this.properties = data.properties || [];
            } catch (error) {
                // Error loading properties
            }
        },

        filterBookings() {
            let filtered = this.completedBookings;

            if (this.search) {
                const searchLower = this.search.toLowerCase();
                filtered = filtered.filter(booking => 
                    booking.guest.name.toLowerCase().includes(searchLower) ||
                    booking.accommodation.property.name.toLowerCase().includes(searchLower) ||
                    booking.accommodation.custom_name.toLowerCase().includes(searchLower)
                );
            }

            if (this.selectedProperty) {
                filtered = filtered.filter(booking => 
                    booking.accommodation.property_id == this.selectedProperty
                );
            }

            this.filteredBookings = filtered;
        },

        goToPage(page) {
            if (page >= 1 && page <= this.lastPage) {
                const url = new URL(window.location);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
            }
        },

        get paginationLinks() {
            const links = [];
            const current = this.currentPage;
            const last = this.lastPage;
            
            // Previous page
            if (current > 1) {
                links.push({
                    page: current - 1,
                    label: 'Previous',
                    active: false,
                    disabled: false
                });
            }
            
            // Page numbers
            const start = Math.max(1, current - 2);
            const end = Math.min(last, current + 2);
            
            for (let i = start; i <= end; i++) {
                links.push({
                    page: i,
                    label: i.toString(),
                    active: i === current,
                    disabled: false
                });
            }
            
            // Next page
            if (current < last) {
                links.push({
                    page: current + 1,
                    label: 'Next',
                    active: false,
                    disabled: false
                });
            }
            
            return links;
        },

        showMessage(message, type = 'success') {
            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed top-4 right-4 z-50 max-w-sm w-full rounded-lg shadow-lg p-4 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(messageDiv);
            
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.parentNode.removeChild(messageDiv);
                }
            }, 5000);
        },

        openPaymentModal(booking) {
            this.selectedBooking = booking;
            this.paymentAmount = booking.balance_pending;
            this.paymentNotes = '';
            this.showPaymentModal = true;
        },

        closePaymentModal() {
            this.showPaymentModal = false;
            this.selectedBooking = null;
            this.paymentAmount = 0;
            this.paymentNotes = '';
        },

        async collectPayment() {
            if (!this.selectedBooking || !this.paymentAmount || this.paymentAmount <= 0) {
                this.showMessage('Please enter a valid payment amount', 'error');
                return;
            }

            try {
                const response = await fetch(`/bookings/${this.selectedBooking.uuid}/payment`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        amount_paid: this.paymentAmount,
                        payment_notes: this.paymentNotes
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.showMessage('Payment collected successfully!', 'success');
                    this.closePaymentModal();
                    
                    // Reload the page to refresh booking data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.showMessage(data.message || 'Failed to collect payment', 'error');
                }
            } catch (error) {
                this.showMessage('Error collecting payment', 'error');
            }
        },

        exportCompletedBookings() {
            const headers = ['Guest Name', 'Property', 'Accommodation', 'Check-in', 'Check-out', 'Completed Date', 'Total Amount', 'Final Bill'];
            const csvContent = [
                headers.join(','),
                ...this.filteredBookings.map(booking => [
                    `"${booking.guest.name}"`,
                    `"${booking.accommodation.property.name}"`,
                    `"${booking.accommodation.custom_name}"`,
                    booking.check_in_date,
                    booking.check_out_date,
                    booking.check_out_record?.check_out_time || 'N/A',
                    booking.total_amount,
                    booking.check_out_record?.final_bill || 'N/A'
                ].join(','))
            ].join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `completed-bookings-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            this.showMessage('Completed bookings exported successfully!', 'success');
        }
    }
}
</script>
@endpush
