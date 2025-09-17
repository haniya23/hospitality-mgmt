@extends('layouts.app')

@section('title', 'Customer Management')

@section('header')
    @include('partials.customers.header')
@endsection

@section('content')
<div x-data="customerData()" class="space-y-6">
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
        showAddModal: false,
        customers: [
            {
                id: 1,
                name: 'John Doe',
                email: 'john@example.com',
                phone: '+91 9876543210',
                status: 'active',
                totalBookings: 5,
                lastBooking: '2 days ago'
            },
            {
                id: 2,
                name: 'Jane Smith',
                email: 'jane@example.com',
                phone: '+91 9876543211',
                status: 'active',
                totalBookings: 3,
                lastBooking: '1 week ago'
            },
            {
                id: 3,
                name: 'Mike Johnson',
                email: 'mike@example.com',
                phone: '+91 9876543212',
                status: 'inactive',
                totalBookings: 1,
                lastBooking: '2 months ago'
            }
        ],

        get filteredCustomers() {
            return this.customers.filter(customer => {
                const matchesSearch = customer.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                                    customer.email.toLowerCase().includes(this.searchTerm.toLowerCase());
                const matchesStatus = !this.statusFilter || customer.status === this.statusFilter;
                return matchesSearch && matchesStatus;
            });
        }
    }
}
</script>
@endpush