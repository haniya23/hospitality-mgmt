@extends('layouts.app')

@section('title', 'Dashboard - Stay loops')
@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-green-600 hover:text-green-800">
        <i class="fas fa-home text-xs mr-1"></i>
        Home
    </a>
@endsection

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
            if (this.motivationalQuotes.length === 0) return {quote: 'No quotes available', author: ''};
            return this.motivationalQuotes[this.currentQuoteIndex];
        },

        init() {
            console.log('Dashboard initialized');
            // Set quote based on day of year (one quote per day)
            const today = new Date();
            const startOfYear = new Date(today.getFullYear(), 0, 0);
            const dayOfYear = Math.floor((today - startOfYear) / (1000 * 60 * 60 * 24));
            this.currentQuoteIndex = dayOfYear % this.motivationalQuotes.length;
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