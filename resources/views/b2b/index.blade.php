@extends('layouts.app')

@section('title', 'B2B Management')

@section('header')
    @include('partials.b2b.header')
@endsection

@section('content')
<div x-data="b2bData()" x-init="init()" class="space-y-6">
    @include('partials.b2b.search')
    @include('partials.b2b.partners')
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
        }
    }
}
</script>
@endpush