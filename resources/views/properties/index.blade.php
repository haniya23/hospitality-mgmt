@extends('layouts.app')

@section('title', 'Properties Management')
@section('page-title', 'Properties')
@section('page-subtitle', 'Manage your properties and accommodations')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-green-600 hover:text-green-800">
        <i class="fas fa-home text-xs mr-1"></i>
        Home
    </a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Properties</span>
@endsection

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
        properties: @json($activeProperties ?? []),
        archivedProperties: @json($archivedProperties ?? []),
        activeFilter: 'all',
        showArchive: false,
        
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
            const currentProperties = this.showArchive ? this.archivedProperties : this.properties;
            if (this.activeFilter === 'all') return currentProperties;
            return currentProperties.filter(p => p.status === this.activeFilter);
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
            // Properties loaded
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
                // Error loading accommodations
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
                // Error loading B2B partners
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
                // Error loading accommodations
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
        },
        
        // Archive functionality
        toggleArchiveView() {
            this.showArchive = !this.showArchive;
            this.activeFilter = 'all'; // Reset filter when switching views
        },
        
        // Property delete request functionality
        async requestPropertyDeletion(property) {
            // Show confirmation dialog with reason input
            const reason = prompt('Please provide a reason for deleting this property (optional):');
            
            // If user cancelled the prompt
            if (reason === null) {
                return;
            }
            
            // Confirm the action
            if (!confirm(`Are you sure you want to request deletion of "${property.name}"? This will send a request to the admin for review.`)) {
                return;
            }
            
            try {
                const response = await fetch('/api/property-delete-requests', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        property_id: property.id,
                        reason: reason || null
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    this.showNotification('Delete request submitted successfully! An admin will review your request.', 'success');
                    
                    // Optionally reload the page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    this.showNotification(data.message || 'Failed to submit delete request', 'error');
                }
            } catch (error) {
                this.showNotification('An error occurred while submitting the delete request', 'error');
            }
        },
        
        showNotification(message, type = 'info') {
            // Create a simple notification
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
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
            // Property stats initialized
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