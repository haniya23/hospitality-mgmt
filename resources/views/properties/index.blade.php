@extends('layouts.app')

@section('title', 'Properties Management')

@section('header')
    @include('partials.properties.header')
@endsection

@section('content')
<div x-data="propertyManager()" x-init="init()" class="space-y-6">
    @include('partials.properties.list')
</div>
@endsection

@push('scripts')
<script>
function propertyManager() {
    return {
        properties: @json($properties ?? []),
        activeFilter: 'all',

        get filteredProperties() {
            if (this.activeFilter === 'all') return this.properties;
            return this.properties.filter(p => p.status === this.activeFilter);
        },

        get stats() {
            return {
                total: this.properties.length,
                active: this.properties.filter(p => p.status === 'active').length,
                accommodations: this.properties.reduce((sum, p) => sum + (p.property_accommodations_count || 0), 0),
                bookings: this.properties.reduce((sum, p) => sum + (p.bookings_count || 0), 0)
            };
        },

        init() {
            console.log('Properties loaded:', this.properties.length);
        }
    }
}

function propertyStats() {
    return {
        properties: @json($properties ?? []),
        
        get stats() {
            return {
                total: this.properties.length,
                active: this.properties.filter(p => p.status === 'active').length,
                accommodations: this.properties.reduce((sum, p) => sum + (p.property_accommodations_count || 0), 0),
                bookings: this.properties.reduce((sum, p) => sum + (p.bookings_count || 0), 0)
            };
        },

        init() {
            console.log('Property stats initialized');
        },

        // Navigation functions for clickable stats
        navigateToAllProperties() {
            window.location.href = '/properties';
        },

        navigateToActiveProperties() {
            window.location.href = '/properties?status=active';
        },

        navigateToAccommodations() {
            window.location.href = '/accommodations';
        },

        navigateToBookings() {
            window.location.href = '/bookings';
        }
    }
}
</script>
@endpush