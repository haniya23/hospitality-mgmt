@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
                    <p class="text-gray-600 mt-1">Confirmation: {{ $booking->confirmation_number }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                    <a href="{{ route('bookings.calendar') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Back to Calendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Booking Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Property & Accommodation -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Property & Accommodation</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Property</label>
                            <p class="text-gray-900">{{ $booking->accommodation->property->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Accommodation</label>
                            <p class="text-gray-900">{{ $booking->accommodation->custom_name ?? $booking->accommodation->predefinedType->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Guest Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Guest Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Guest Name</label>
                            <p class="text-gray-900">{{ $booking->guest->name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Mobile Number</label>
                            <p class="text-gray-900">{{ $booking->guest->mobile_number ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $booking->guest->email ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">ID Type</label>
                            <p class="text-gray-900">{{ $booking->guest->id_type ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Dates -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking Dates</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Check-in Date</label>
                            <p class="text-gray-900">{{ $booking->check_in_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Check-out Date</label>
                            <p class="text-gray-900">{{ $booking->check_out_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Duration</label>
                            <p class="text-gray-900">{{ $booking->check_in_date->diffInDays($booking->check_out_date) }} days</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Booking Date</label>
                            <p class="text-gray-900">{{ $booking->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Occupancy -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Occupancy</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Adults</label>
                            <p class="text-gray-900">{{ $booking->adults }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Children</label>
                            <p class="text-gray-900">{{ $booking->children }}</p>
                        </div>
                    </div>
                </div>

                @if($booking->special_requests || $booking->notes)
                <!-- Special Requests & Notes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Special Requests & Notes</h2>
                    @if($booking->special_requests)
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-500">Special Requests</label>
                        <p class="text-gray-900">{{ $booking->special_requests }}</p>
                    </div>
                    @endif
                    @if($booking->notes)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Notes</label>
                        <p class="text-gray-900">{{ $booking->notes }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Financial Summary -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Amount</span>
                            <span class="font-semibold">₹{{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Advance Paid</span>
                            <span class="font-semibold">₹{{ number_format($booking->advance_paid, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-3">
                            <span class="text-gray-600">Balance Pending</span>
                            <span class="font-semibold text-red-600">₹{{ number_format($booking->balance_pending, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                    <div class="space-y-3">
                        @if($booking->status === 'pending')
                        <button onclick="confirmBooking('{{ $booking->uuid }}')" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Confirm Booking
                        </button>
                        @endif
                        
                        @if(in_array($booking->status, ['pending', 'confirmed']))
                        <button onclick="cancelBooking('{{ $booking->uuid }}')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Cancel Booking
                        </button>
                        @endif
                        
                        <a href="{{ route('bookings.edit', $booking) }}" class="w-full block text-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Edit Booking
                        </a>
                    </div>
                </div>

                <!-- B2B Partner Information -->
                @if($booking->b2bPartner)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">B2B Partner</h2>
                    <div class="space-y-2">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Partner Name</label>
                            <p class="text-gray-900">{{ $booking->b2bPartner->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Contact</label>
                            <p class="text-gray-900">{{ $booking->b2bPartner->contact_person }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function confirmBooking(uuid) {
    if (confirm('Are you sure you want to confirm this booking?')) {
        fetch(`/api/bookings/${uuid}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: 'confirmed' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            // Error occurred
            alert('An error occurred while confirming the booking.');
        });
    }
}

function cancelBooking(uuid) {
    const reason = prompt('Please enter the reason for cancellation:');
    if (reason && reason.trim()) {
        fetch(`/api/bookings/${uuid}/cancel`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason: reason.trim() })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            // Error occurred
            alert('An error occurred while cancelling the booking.');
        });
    }
}
</script>
@endsection
