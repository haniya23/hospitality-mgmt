@extends('layouts.app')

@section('title', 'B2B Management')

@section('header')
    @include('partials.b2b.header')
@endsection

@section('content')
<div x-data="b2bData()" class="space-y-6">
    @include('partials.b2b.search')
    @include('partials.b2b.partners')
    @include('partials.b2b.modal')
</div>
@endsection

@push('scripts')
<script>
function b2bData() {
    return {
        search: '',
        showCreateModal: false,
        partner_name: '',
        contact_person: '',
        mobile_number: '',
        email: '',
        partners: [
            {
                id: 1,
                partner_name: 'Travel Agency A',
                contact_person: 'John Smith',
                phone: '+91 9876543210',
                email: 'john@travelagency.com',
                status: 'active',
                reservations_count: 25,
                created_at: '2024-01-15'
            },
            {
                id: 2,
                partner_name: 'Hotel Chain B',
                contact_person: 'Sarah Johnson',
                phone: '+91 9876543211',
                email: 'sarah@hotelchain.com',
                status: 'pending',
                reservations_count: 12,
                created_at: '2024-01-10'
            },
            {
                id: 3,
                partner_name: 'Tour Operator C',
                contact_person: 'Mike Wilson',
                phone: '+91 9876543212',
                email: 'mike@touroperator.com',
                status: 'active',
                reservations_count: 8,
                created_at: '2024-01-05'
            }
        ],

        get filteredPartners() {
            return this.partners.filter(partner => 
                partner.partner_name.toLowerCase().includes(this.search.toLowerCase()) ||
                partner.contact_person.toLowerCase().includes(this.search.toLowerCase())
            );
        },

        openCreateModal() {
            this.showCreateModal = true;
            this.resetForm();
        },

        closeCreateModal() {
            this.showCreateModal = false;
        },

        resetForm() {
            this.partner_name = '';
            this.contact_person = '';
            this.mobile_number = '';
            this.email = '';
        },

        createPartner() {
            if (!this.partner_name || !this.contact_person || !this.mobile_number) {
                alert('Please fill all required fields');
                return;
            }

            const newPartner = {
                id: this.partners.length + 1,
                partner_name: this.partner_name,
                contact_person: this.contact_person,
                phone: this.mobile_number,
                email: this.email,
                status: 'pending',
                reservations_count: 0,
                created_at: new Date().toISOString().split('T')[0]
            };

            this.partners.push(newPartner);
            this.closeCreateModal();
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    }
}
</script>
@endpush