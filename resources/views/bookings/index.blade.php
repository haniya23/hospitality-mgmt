@extends('layouts.app')

@section('title', 'Booking Management')

@section('header')
    @include('partials.bookings.header')
@endsection

@section('content')
<div x-data="bookingData()" x-init="init()" class="space-y-6">
    @include('partials.bookings.filters')
    @include('partials.bookings.list')
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
        message: '',
        messageType: 'success',
        bookings: [],

        get filteredBookings() {
            return this.bookings.filter(booking => {
                const matchesStatus = !this.statusFilter || booking.status === this.statusFilter;
                const matchesProperty = !this.selectedProperty || booking.accommodation.property.name.includes(this.selectedProperty);
                return matchesStatus && matchesProperty;
            });
        },

        async init() {
            await this.loadBookings();
        },

        async loadBookings() {
            try {
                const response = await fetch('/api/bookings');
                const data = await response.json();
                this.bookings = [...data.pending, ...data.active];
            } catch (error) {
                console.error('Error loading bookings:', error);
                this.showMessage('Error loading bookings', 'error');
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

        async toggleBookingStatus(bookingId) {
            try {
                const response = await fetch(`/api/bookings/${bookingId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadBookings();
                    this.showMessage(data.message, 'success');
                } else {
                    this.showMessage(data.message, 'error');
                }
            } catch (error) {
                this.showMessage('Error updating booking status', 'error');
            }
        },

        openCancelModal(bookingId) {
            this.cancelBookingId = bookingId;
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
        }
    }
}
</script>
@endpush