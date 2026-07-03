@extends('layouts.app')

@section('title', 'Cancelled Bookings')

@section('header')
<div x-data="cancelledBookingData()" x-init="init()">
    @include('partials.bookings.cancelled-header')
</div>
@endsection

@section('content')
<div x-data="cancelledBookingData()" x-init="init()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    @include('partials.bookings.cancelled-filters')
    @include('partials.bookings.cancelled-list')

    <!-- Refund Modal -->
    <template x-if="showRefundModal">
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/50 transition-opacity" @click="showRefundModal = false"></div>
            
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative z-10 transform transition-all">
                <div class="flex items-center justify-between mb-4 border-b pb-3">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-undo text-red-600 mr-2"></i>
                        <span>Record Booking Refund</span>
                    </h3>
                    <button @click="showRefundModal = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <form @submit.prevent="submitRefund" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Refundable Amount</label>
                        <div class="text-lg font-bold text-gray-900" x-text="'₹' + maxRefundAmount"></div>
                    </div>

                    <div>
                        <label for="refund_amount" class="block text-sm font-medium text-gray-700 mb-1">Refund Amount *</label>
                        <div class="relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₹</span>
                            </div>
                            <input type="number" id="refund_amount" x-model="refundAmount" step="0.01" min="0.01" :max="maxRefundAmount"
                                   class="focus:ring-red-500 focus:border-red-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-lg" required>
                        </div>
                    </div>

                    <div>
                        <label for="refund_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason / Notes</label>
                        <textarea id="refund_reason" x-model="refundReason" rows="3"
                                  class="focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                  placeholder="e.g. Booking cancelled by guest, full refund approved"></textarea>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="button" @click="showRefundModal = false"
                                class="flex-1 px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700">
                            Record Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function cancelledBookingData() {
    return {
        cancelledBookings: @json($cancelledBookings->items() ?? []),
        properties: @json($properties ?? []),
        filteredBookings: [],
        search: '',
        selectedProperty: '',
        currentPage: {{ $cancelledBookings->currentPage() }},
        lastPage: {{ $cancelledBookings->lastPage() }},
        total: {{ $cancelledBookings->total() }},
        perPage: {{ $cancelledBookings->perPage() }},
        from: {{ $cancelledBookings->firstItem() ?? 0 }},
        to: {{ $cancelledBookings->lastItem() ?? 0 }},
        showRefundModal: false,
        selectedBookingForRefund: null,
        refundAmount: 0,
        refundReason: '',
        maxRefundAmount: 0,

        init() {
            this.filteredBookings = this.cancelledBookings;
            this.loadProperties();
        },

        openRefundModal(booking) {
            this.selectedBookingForRefund = booking;
            this.maxRefundAmount = Math.max(
                0,
                parseFloat(booking.reservation.advance_paid || 0) - parseFloat(booking.refund_amount || 0)
            );
            this.refundAmount = this.maxRefundAmount;
            this.refundReason = '';
            this.showRefundModal = true;
        },

        async submitRefund() {
            if (this.refundAmount <= 0 || this.refundAmount > this.maxRefundAmount) {
                alert(`Please enter a valid refund amount up to ₹${this.maxRefundAmount}`);
                return;
            }
            try {
                const response = await fetch(`/api/bookings/${this.selectedBookingForRefund.reservation.uuid}/refund`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        amount: this.refundAmount,
                        reason: this.refundReason
                    })
                });
                const data = await response.json();
                if (data.success) {
                    this.selectedBookingForRefund.refund_amount = parseFloat(data.refund_amount || 0);
                    this.maxRefundAmount = parseFloat(data.remaining_refundable_amount || 0);
                    this.showRefundModal = false;
                    this.showMessage('Refund recorded successfully!', 'success');
                } else {
                    this.showMessage(data.message || 'Error recording refund', 'error');
                }
            } catch (error) {
                this.showMessage('Error recording refund', 'error');
            }
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
            let filtered = this.cancelledBookings;

            if (this.search) {
                const searchLower = this.search.toLowerCase();
                filtered = filtered.filter(booking => 
                    booking.reservation.guest.name.toLowerCase().includes(searchLower) ||
                    booking.reservation.accommodation.property.name.toLowerCase().includes(searchLower) ||
                    booking.reservation.accommodation.custom_name.toLowerCase().includes(searchLower)
                );
            }

            if (this.selectedProperty) {
                filtered = filtered.filter(booking => 
                    booking.reservation.accommodation.property_id == this.selectedProperty
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

        getStatValue(statType) {
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const thisWeek = new Date(today.getTime() - (today.getDay() * 24 * 60 * 60 * 1000));
            const thisMonth = new Date(now.getFullYear(), now.getMonth(), 1);

            switch(statType) {
                case 'total':
                    return this.total;
                case 'today':
                    return this.cancelledBookings.filter(booking => 
                        new Date(booking.cancelled_at) >= today
                    ).length;
                case 'thisWeek':
                    return this.cancelledBookings.filter(booking => 
                        new Date(booking.cancelled_at) >= thisWeek
                    ).length;
                case 'thisMonth':
                    return this.cancelledBookings.filter(booking => 
                        new Date(booking.cancelled_at) >= thisMonth
                    ).length;
                default:
                    return 0;
            }
        },

        filterByPeriod(period) {
            // Reset other filters
            this.search = '';
            this.selectedProperty = '';
            
            // Apply period filter
            const now = new Date();
            let startDate;
            
            switch(period) {
                case 'today':
                    startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    break;
                case 'thisWeek':
                    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    startDate = new Date(today.getTime() - (today.getDay() * 24 * 60 * 60 * 1000));
                    break;
                case 'thisMonth':
                    startDate = new Date(now.getFullYear(), now.getMonth(), 1);
                    break;
                case 'total':
                default:
                    this.filteredBookings = this.cancelledBookings;
                    return;
            }
            
            this.filteredBookings = this.cancelledBookings.filter(booking => 
                new Date(booking.cancelled_at) >= startDate
            );
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

        async reactivateBooking(bookingUuid, newStatus) {
            if (!confirm(`Are you sure you want to reactivate this booking as ${newStatus}?`)) {
                return;
            }

            try {
                const response = await fetch(`/api/bookings/${bookingUuid}/reactivate`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Remove the booking from the cancelled list
                    this.cancelledBookings = this.cancelledBookings.filter(booking => 
                        booking.reservation.uuid !== bookingUuid
                    );
                    this.filteredBookings = this.filteredBookings.filter(booking => 
                        booking.reservation.uuid !== bookingUuid
                    );
                    
                    // Show success message
                    this.showMessage(`Booking reactivated as ${newStatus} successfully!`, 'success');
                } else {
                    this.showMessage(data.message || 'Error reactivating booking', 'error');
                }
            } catch (error) {
                // Error reactivating booking
                this.showMessage('Error reactivating booking', 'error');
            }
        },

        showMessage(message, type = 'success') {
            // Create a temporary message element
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
            
            // Remove the message after 5 seconds
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.parentNode.removeChild(messageDiv);
                }
            }, 5000);
        },

        exportCancelledBookings() {
            // Create CSV content
            const headers = ['Guest Name', 'Property', 'Accommodation', 'Check-in', 'Check-out', 'Cancelled Date', 'Reason', 'Total Amount'];
            const csvContent = [
                headers.join(','),
                ...this.filteredBookings.map(booking => [
                    `"${booking.reservation.guest.name}"`,
                    `"${booking.reservation.accommodation.property.name}"`,
                    `"${booking.reservation.accommodation.custom_name}"`,
                    booking.reservation.check_in_date,
                    booking.reservation.check_out_date,
                    booking.cancelled_at,
                    `"${booking.reason || 'N/A'}"`,
                    booking.reservation.total_amount
                ].join(','))
            ].join('\n');

            // Create and download file
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `cancelled-bookings-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            this.showMessage('Cancelled bookings exported successfully!', 'success');
            
            // Redirect to same page after download
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }
}
</script>
@endpush
