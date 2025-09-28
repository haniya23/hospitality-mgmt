@extends('layouts.app')

@section('title', 'Dashboard - Stay loops')

@section('header')
    @include('partials.dashboard.header')
@endsection

@section('content')
<div x-data="dashboardData()" x-init="init()" class="space-y-4">
    @include('partials.dashboard.revenue-cards')
    @include('partials.dashboard.quick-stats')
    @include('partials.dashboard.quick-links')
    @include('partials.dashboard.properties-section')
    @include('partials.dashboard.recent-activity')
</div>
@endsection

@include('partials.dashboard.motivational-quotes')

@push('scripts')
<script>
function dashboardData() {
    return {
        notifications: 3,
        properties: @json($properties ?? []),
        nextBooking: @json($nextBooking ?? null),
        upcomingBookingsThisWeek: @json($upcomingBookingsThisWeek ?? 0),
        upcomingBookingsThisMonth: @json($upcomingBookingsThisMonth ?? 0),
        topB2bPartner: @json($topB2bPartner ?? null),
        recentBookings: @json($recentBookings ?? []),
        pendingBookings: @json($pendingBookings ?? []),
        activeBookings: @json($activeBookings ?? []),
        motivationalQuotes: @json($motivationalQuotes ?? []),
        currentQuoteIndex: 0,

        get currentQuote() {
            if (this.motivationalQuotes.length === 0) return 'No quotes available';
            return this.motivationalQuotes[this.currentQuoteIndex];
        },

        init() {
            console.log('Dashboard initialized');
            // Set random initial quote
            this.currentQuoteIndex = Math.floor(Math.random() * this.motivationalQuotes.length);
        },

        formatNumber(num) {
            return new Intl.NumberFormat('en-IN').format(num);
        },

        formatDate(dateString) {
            if (!dateString) return 'No date';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-IN', { 
                day: 'numeric', 
                month: 'short', 
                year: 'numeric' 
            });
        },

        nextQuote() {
            this.currentQuoteIndex = (this.currentQuoteIndex + 1) % this.motivationalQuotes.length;
        },

        getTotalBookings(property) {
            if (!property.property_accommodations) return 0;
            let total = 0;
            property.property_accommodations.forEach(accommodation => {
                if (accommodation.reservations) {
                    total += accommodation.reservations.length;
                }
            });
            return total;
        },

        // Navigation functions for clickable stats
        navigateToBookings() {
            window.location.href = '/bookings';
        },

        navigateToPendingBookings() {
            window.location.href = '/bookings?status=pending';
        },

        navigateToActiveBookings() {
            window.location.href = '/bookings?status=confirmed,checked_in';
        },

        navigateToB2bPartners() {
            window.location.href = '/b2b';
        },

        navigateToProperties() {
            window.location.href = '/properties';
        },

        navigateToAccommodations() {
            window.location.href = '/properties';
        },

        navigateToNewBooking() {
            window.location.href = '/bookings/create';
        }
    }
}
</script>
@endpush