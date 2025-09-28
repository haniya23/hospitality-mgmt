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
        
        // Booking modal state
        showBookingModal: false,
        selectedProperty: null,
        selectedAccommodation: null,
        propertyAccommodations: [],
        customPrice: null,
        
        // B2B booking modal state
        showB2BBookingModal: false,
        selectedB2BProperty: null,
        selectedB2BPartner: null,
        selectedB2BAccommodation: null,
        b2bPropertyAccommodations: [],
        b2bCustomPrice: null,
        b2bPartners: [],
        b2bCommissionType: 'percentage',
        b2bCommissionValue: 10,

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
            this.loadB2BPartners();
        },
        
        async openBookingModal(property) {
            this.selectedProperty = property;
            this.selectedAccommodation = null;
            this.showBookingModal = true;
            
            try {
                const response = await fetch(`/api/properties/${property.id}/accommodations`);
                const accommodations = await response.json();
                this.propertyAccommodations = accommodations;
            } catch (error) {
                console.error('Error loading accommodations:', error);
                this.propertyAccommodations = [];
            }
        },
        
        closeBookingModal() {
            this.showBookingModal = false;
            this.selectedProperty = null;
            this.selectedAccommodation = null;
            this.propertyAccommodations = [];
            this.customPrice = null;
        },
        
        selectAccommodation(accommodation) {
            this.selectedAccommodation = accommodation;
        },
        
        // B2B booking methods
        async loadB2BPartners() {
            try {
                const response = await fetch('/api/partners');
                const partners = await response.json();
                this.b2bPartners = partners;
            } catch (error) {
                console.error('Error loading B2B partners:', error);
                this.b2bPartners = [];
            }
        },
        
        async openB2BBookingModal(property) {
            this.selectedB2BProperty = property;
            this.selectedB2BPartner = null;
            this.selectedB2BAccommodation = null;
            this.b2bCustomPrice = null;
            this.b2bCommissionType = 'percentage';
            this.b2bCommissionValue = 10;
            this.showB2BBookingModal = true;
            
            try {
                const response = await fetch(`/api/properties/${property.id}/accommodations`);
                const accommodations = await response.json();
                this.b2bPropertyAccommodations = accommodations;
            } catch (error) {
                console.error('Error loading accommodations:', error);
                this.b2bPropertyAccommodations = [];
            }
        },
        
        closeB2BBookingModal() {
            this.showB2BBookingModal = false;
            this.selectedB2BProperty = null;
            this.selectedB2BPartner = null;
            this.selectedB2BAccommodation = null;
            this.b2bPropertyAccommodations = [];
            this.b2bCustomPrice = null;
            this.b2bCommissionType = 'percentage';
            this.b2bCommissionValue = 10;
        },
        
        selectB2BPartner(partner) {
            this.selectedB2BPartner = partner;
            this.b2bCommissionValue = partner.commission_rate || 10;
        },
        
        selectB2BAccommodation(accommodation) {
            this.selectedB2BAccommodation = accommodation;
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