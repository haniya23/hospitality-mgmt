@extends('layouts.app')

@section('title', 'Booking Management')

@section('header')
    @include('partials.bookings.header')
@endsection

@section('content')
<div x-data="bookingData()" x-init="init()" class="space-y-6">
    @include('partials.bookings.filters')
    @include('partials.bookings.list')
    @include('partials.bookings.modal')
</div>
@endsection

@push('scripts')
<script>
function bookingData() {
    return {
        statusFilter: '',
        dateFilter: '',
        selectedProperty: '',
        showBookingModal: false,
        showCancelModal: false,
        cancelBookingId: null,
        cancelReason: '',
        cancelDescription: '',
        message: '',
        messageType: 'success',
        createNewGuest: true,
        properties: [],
        accommodations: [],
        guests: [],
        booking: {
            property_id: '',
            accommodation_id: '',
            check_in_date: '',
            check_out_date: '',
            adults: 1,
            children: 0,
            guest_id: '',
            guest_name: '',
            guest_mobile: '',
            guest_email: '',
            total_amount: 0,
            advance_paid: 0
        },
        bookings: [
            {
                id: 1,
                guest: { name: 'John Doe', mobile_number: '+91 9876543210' },
                accommodation: {
                    property: { name: 'Ocean View Resort' },
                    display_name: 'Deluxe Room'
                },
                check_in_date: '2024-01-15',
                check_out_date: '2024-01-18',
                adults: 2,
                children: 0,
                status: 'confirmed',
                total_amount: 15000,
                balance_pending: 0,
                b2b_partner: null
            },
            {
                id: 2,
                guest: { name: 'Jane Smith', mobile_number: '+91 9876543211' },
                accommodation: {
                    property: { name: 'Mountain Lodge' },
                    display_name: 'Standard Room'
                },
                check_in_date: '2024-01-20',
                check_out_date: '2024-01-22',
                adults: 1,
                children: 1,
                status: 'pending',
                total_amount: 8000,
                balance_pending: 3000,
                b2b_partner: 'Travel Agency A'
            },
            {
                id: 3,
                guest: { name: 'Mike Johnson', mobile_number: '+91 9876543212' },
                accommodation: {
                    property: { name: 'City Center Hotel' },
                    display_name: 'Suite'
                },
                check_in_date: '2024-01-10',
                check_out_date: '2024-01-12',
                adults: 2,
                children: 0,
                status: 'cancelled',
                total_amount: 12000,
                balance_pending: 0,
                b2b_partner: null
            }
        ],

        get filteredBookings() {
            return this.bookings.filter(booking => {
                const matchesStatus = !this.statusFilter || booking.status === this.statusFilter;
                const matchesProperty = !this.selectedProperty || booking.accommodation.property.name.includes(this.selectedProperty);
                return matchesStatus && matchesProperty;
            });
        },

        async init() {
            this.loadProperties();
            this.loadGuests();
        },

        loadProperties() {
            this.properties = [
                { id: 1, name: 'Ocean View Resort' },
                { id: 2, name: 'Mountain Lodge' },
                { id: 3, name: 'City Center Hotel' }
            ];
        },

        loadGuests() {
            this.guests = [
                { id: 1, name: 'John Doe', mobile_number: '+91 9876543210', email: 'john@example.com' },
                { id: 2, name: 'Jane Smith', mobile_number: '+91 9876543211', email: 'jane@example.com' }
            ];
        },

        loadAccommodations() {
            this.accommodations = [
                { id: 1, display_name: 'Deluxe Room', base_price: 2500 },
                { id: 2, display_name: 'Standard Room', base_price: 2000 },
                { id: 3, display_name: 'Suite', base_price: 4000 }
            ];
        },

        openBookingModal() {
            this.showBookingModal = true;
            this.resetBookingForm();
        },

        closeBookingModal() {
            this.showBookingModal = false;
        },

        resetBookingForm() {
            this.booking = {
                property_id: this.selectedProperty || '',
                accommodation_id: '',
                check_in_date: '',
                check_out_date: '',
                adults: 1,
                children: 0,
                guest_id: '',
                guest_name: '',
                guest_mobile: '',
                guest_email: '',
                total_amount: 0,
                advance_paid: 0
            };
            this.createNewGuest = true;
            if (this.booking.property_id) {
                this.loadAccommodations();
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

        calculateRate() {
            if (this.booking.accommodation_id && this.booking.check_in_date && this.booking.check_out_date) {
                const acc = this.accommodations.find(a => a.id == this.booking.accommodation_id);
                if (acc) {
                    const nights = this.calculateNights(this.booking.check_in_date, this.booking.check_out_date);
                    this.booking.total_amount = acc.base_price * nights;
                }
            }
        },

        saveBooking() {
            if (!this.booking.property_id || !this.booking.accommodation_id || !this.booking.guest_name || !this.booking.guest_mobile || !this.booking.check_in_date || !this.booking.check_out_date) {
                this.showMessage('Please fill all required fields', 'error');
                return;
            }

            const newBooking = {
                id: this.bookings.length + 1,
                guest: {
                    name: this.booking.guest_name,
                    mobile_number: this.booking.guest_mobile
                },
                accommodation: {
                    property: { name: this.properties.find(p => p.id == this.booking.property_id)?.name || 'Unknown' },
                    display_name: this.accommodations.find(a => a.id == this.booking.accommodation_id)?.display_name || 'Unknown'
                },
                check_in_date: this.booking.check_in_date,
                check_out_date: this.booking.check_out_date,
                adults: this.booking.adults,
                children: this.booking.children,
                status: 'pending',
                total_amount: this.booking.total_amount,
                balance_pending: this.booking.total_amount - this.booking.advance_paid,
                b2b_partner: null
            };

            this.bookings.push(newBooking);
            this.showMessage('Booking created successfully!', 'success');
            this.closeBookingModal();
        },

        toggleBookingStatus(bookingId) {
            const booking = this.bookings.find(b => b.id === bookingId);
            if (booking && booking.status === 'pending') {
                booking.status = 'confirmed';
                this.showMessage('Booking confirmed successfully', 'success');
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

        cancelBooking() {
            if (!this.cancelReason) {
                this.showMessage('Please select a reason for cancellation.', 'error');
                return;
            }

            const booking = this.bookings.find(b => b.id === this.cancelBookingId);
            if (booking) {
                booking.status = 'cancelled';
                this.showMessage('Booking cancelled successfully', 'success');
                this.closeCancelModal();
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