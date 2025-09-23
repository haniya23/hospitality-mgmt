@extends('layouts.app')

@section('title', 'Customer Management')

@section('header')
    @include('partials.customers.header')
@endsection

@section('content')
<div x-data="customerData()" x-init="loadCustomers()" class="space-y-6">
    @include('partials.customers.search')
    @include('partials.customers.list')
</div>
@endsection

@push('scripts')
<script>
function customerData() {
    return {
        searchTerm: '',
        statusFilter: '',
        customers: @json($customers ?? []),
        
        async loadCustomers() {
            // Customers are already loaded from server
        },

        get filteredCustomers() {
            return this.customers.filter(customer => {
                const matchesSearch = customer.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                                    (customer.email && customer.email.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                                    (customer.mobile_number && customer.mobile_number.includes(this.searchTerm));
                return matchesSearch;
            });
        }
    }
}
</script>
@endpush