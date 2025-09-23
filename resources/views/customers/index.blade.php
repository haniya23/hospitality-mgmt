@extends('layouts.app')

@section('title', 'Customer Management')

@section('header')
    @include('partials.customers.header')
@endsection

@section('content')
<div x-data="customerData()" x-init="init()" class="space-y-6">
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
        message: '',
        messageType: 'success',
        customers: @json($customers ?? []),

        get filteredCustomers() {
            return this.customers.filter(customer => {
                const matchesSearch = customer.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                                    (customer.email && customer.email.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                                    (customer.mobile_number && customer.mobile_number.includes(this.searchTerm));
                return matchesSearch;
            });
        },

        async init() {
            // Customers are already loaded from server
        },

        showMessage(msg, type = 'success') {
            this.message = msg;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
            }, 5000);
        }
    }
}
</script>
@endpush