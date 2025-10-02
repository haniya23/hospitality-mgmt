@extends('layouts.app')

@section('title', 'B2B Management')

@section('header')
    <div x-data="b2bData()" x-init="init()">
        @include('partials.b2b.header')
    </div>
@endsection

@section('content')
<div x-data="b2bData()" x-init="init()" class="space-y-6">
    @include('partials.b2b.search')
    
    <!-- Partners List with Property-style Cards -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">B2B Partners (<span x-text="filteredPartners.length"></span>)</h3>
        </div>
        
        <div class="p-4 sm:p-6">
            @include('partials.b2b.partners')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function b2bData() {
    return {
        search: '',
        partners: @json($partners ?? []),

        get filteredPartners() {
            return this.partners.filter(partner => 
                partner.partner_name.toLowerCase().includes(this.search.toLowerCase()) ||
                (partner.contact_user && partner.contact_user.name.toLowerCase().includes(this.search.toLowerCase()))
            );
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        get activePartners() {
            return this.partners.filter(partner => partner.status === 'active').length;
        },

        get totalBookings() {
            return this.partners.reduce((total, partner) => total + (partner.reservations_count || 0), 0);
        },

        get totalPartners() {
            return this.partners.length;
        },

        async init() {
            // Partners are already loaded from server
        },

        openB2BBookingModal(partner) {
            // For now, redirect to booking create page with partner UUID
            window.location.href = `/bookings/create?b2b_partner_uuid=${partner.uuid}`;
        }
    }
}
</script>
@endpush