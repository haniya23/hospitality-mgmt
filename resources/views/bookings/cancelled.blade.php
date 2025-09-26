@extends('layouts.app')

@section('title', 'Cancelled Bookings')

@section('header')
<div x-data="cancelledBookingData()" x-init="init()">
    @include('partials.bookings.cancelled-header')
</div>
@endsection

@section('content')
<div x-data="cancelledBookingData()" x-init="init()" class="space-y-6">
    @include('partials.bookings.cancelled-filters')
    @include('partials.bookings.cancelled-list')
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

        init() {
            this.filteredBookings = this.cancelledBookings;
            this.loadProperties();
        },

        async loadProperties() {
            try {
                const response = await fetch('/api/properties');
                const data = await response.json();
                this.properties = data.properties || [];
            } catch (error) {
                console.error('Error loading properties:', error);
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
        }
    }
}
</script>
@endpush
