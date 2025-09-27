@extends('layouts.app')

@section('title', 'Accommodations')

@section('header')
<div x-data="accommodationData()" x-init="init()">
    @include('partials.accommodations.header')
</div>
@endsection

@section('content')
<div x-data="accommodationData()" x-init="init()" class="space-y-6">
    @include('partials.accommodations.filters')
    @include('partials.accommodations.list')
</div>
@endsection

@push('scripts')
<script>
function accommodationData() {
    return {
        accommodations: @json($accommodations->items() ?? []),
        properties: @json($properties ?? []),
        filteredAccommodations: [],
        search: '',
        selectedProperty: '',
        currentPage: {{ $accommodations->currentPage() }},
        lastPage: {{ $accommodations->lastPage() }},
        total: {{ $accommodations->total() }},
        perPage: {{ $accommodations->perPage() }},
        from: {{ $accommodations->firstItem() ?? 0 }},
        to: {{ $accommodations->lastItem() ?? 0 }},

        init() {
            this.filteredAccommodations = this.accommodations;
        },

        filterAccommodations() {
            let filtered = this.accommodations;

            if (this.search) {
                const searchLower = this.search.toLowerCase();
                filtered = filtered.filter(accommodation => 
                    accommodation.custom_name.toLowerCase().includes(searchLower) ||
                    accommodation.description?.toLowerCase().includes(searchLower) ||
                    accommodation.property.name.toLowerCase().includes(searchLower)
                );
            }

            if (this.selectedProperty) {
                filtered = filtered.filter(accommodation => 
                    accommodation.property_id == this.selectedProperty
                );
            }

            this.filteredAccommodations = filtered;
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

        get stats() {
            return {
                total: this.accommodations.length,
                active: this.accommodations.filter(a => a.property.status === 'active').length,
                totalPrice: this.accommodations.reduce((sum, a) => sum + parseFloat(a.base_price), 0),
                avgPrice: this.accommodations.length > 0 ? this.accommodations.reduce((sum, a) => sum + parseFloat(a.base_price), 0) / this.accommodations.length : 0
            };
        },

        // Navigation functions for clickable stats
        navigateToAllAccommodations() {
            window.location.href = '/accommodations';
        },

        navigateToActiveAccommodations() {
            window.location.href = '/accommodations?status=active';
        },

        navigateToProperties() {
            window.location.href = '/properties';
        },

        navigateToBookings() {
            window.location.href = '/bookings';
        },

        bookAccommodation(accommodation) {
            // Store accommodation details in sessionStorage for booking page
            sessionStorage.setItem('selectedAccommodation', JSON.stringify({
                id: accommodation.id,
                uuid: accommodation.uuid,
                name: accommodation.custom_name,
                property_id: accommodation.property_id,
                property_name: accommodation.property.name,
                base_price: accommodation.base_price,
                max_occupancy: accommodation.max_occupancy,
                size: accommodation.size,
                description: accommodation.description,
                amenities: accommodation.amenities || []
            }));
            
            // Navigate to booking page with accommodation pre-selected
            window.location.href = '/booking-dashboard?accommodation=' + accommodation.uuid;
        },

        deleteAccommodation(accommodationUuid) {
            if (confirm('Are you sure you want to delete this accommodation? This action cannot be undone.')) {
                fetch(`/accommodations/${accommodationUuid}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting accommodation');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting accommodation');
                });
            }
        }
    }
}
</script>
@endpush
