@extends('layouts.app')

@section('title', 'Ready for Check-in')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">📅 Ready for Check-in</h1>
                        <p class="text-sm text-gray-600 mt-1">Confirmed bookings ready for guest check-in</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Total Bookings</div>
                            <div class="text-2xl font-bold text-green-600">{{ $confirmedBookings->flatten()->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($confirmedBookings->count() > 0)
            <!-- Debug info -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Debug: Found {{ $confirmedBookings->flatten()->count() }} confirmed bookings across {{ $confirmedBookings->count() }} properties.
                        </p>
                    </div>
                </div>
            </div>
            @foreach($confirmedBookings as $propertyName => $bookings)
            <!-- Property Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ $propertyName }}</h2>
                            <p class="text-sm text-gray-600">{{ $bookings->count() }} booking{{ $bookings->count() > 1 ? 's' : '' }} ready for check-in</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $bookings->count() }} Ready
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @foreach($bookings as $booking)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-calendar-check text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $booking->guest->name }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Confirmed
                                            </span>
                                            @if($booking->checkInRecord)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Already Checked In
                                            </span>
                                            @endif
                                        </div>
                                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-bed mr-1"></i>{{ $booking->accommodation->display_name }}</span>
                                            <span><i class="fas fa-calendar mr-1"></i>{{ $booking->check_in_date->format('M d, Y') }}</span>
                                            <span><i class="fas fa-users mr-1"></i>{{ $booking->adults }} adults, {{ $booking->children }} children</span>
                                        </div>
                                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-phone mr-1"></i>{{ $booking->guest->mobile_number }}</span>
                                            @if($booking->guest->email)
                                            <span><i class="fas fa-envelope mr-1"></i>{{ $booking->guest->email }}</span>
                                            @endif
                                            <span><i class="fas fa-dollar-sign mr-1"></i>₹{{ number_format($booking->total_amount, 2) }}</span>
                                        </div>
                                        @if($booking->special_requests)
                                        <div class="mt-2">
                                            <span class="text-sm text-gray-600"><i class="fas fa-sticky-note mr-1"></i>{{ Str::limit($booking->special_requests, 100) }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">Booking Ref</div>
                                    <div class="font-mono text-sm font-semibold text-blue-600">{{ $booking->confirmation_number }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Check-in: {{ $booking->check_in_date->format('M d') }}
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <!-- Customer Details Update Modal Trigger -->
                                    <button onclick="openCustomerModal('{{ $booking->uuid }}')" 
                                            class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-edit mr-1"></i>Update Details
                                    </button>
                                    
                                    @if(!$booking->checkInRecord)
                                    <a href="{{ route('checkin.show', $booking->uuid) }}" 
                                       class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                        <i class="fas fa-sign-in-alt mr-1"></i>Start Check-in
                                    </a>
                                    @else
                                    <a href="{{ route('checkin.details', $booking->checkInRecord->uuid) }}" 
                                       class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        View Check-in
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-check text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No confirmed bookings</h3>
                    <p class="text-gray-500 mb-6">Confirmed bookings ready for check-in will appear here.</p>
                    <a href="{{ route('bookings.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        View All Bookings
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Customer Details Update Modal -->
<div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" onclick="closeCustomerModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Update Customer Details</h3>
                    <button onclick="closeCustomerModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form id="customerForm" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" id="guest_name" name="guest_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="guest_mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number *</label>
                        <input type="tel" id="guest_mobile" name="guest_mobile" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="guest_email" name="guest_email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="guest_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea id="guest_address" name="guest_address" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="id_type" class="block text-sm font-medium text-gray-700 mb-1">ID Type</label>
                            <select id="id_type" name="id_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select ID Type</option>
                                <option value="passport">Passport</option>
                                <option value="aadhaar">Aadhaar Card</option>
                                <option value="driving_license">Driving License</option>
                                <option value="pan">PAN Card</option>
                                <option value="voter_id">Voter ID</option>
                            </select>
                        </div>
                        <div>
                            <label for="id_number" class="block text-sm font-medium text-gray-700 mb-1">ID Number</label>
                            <input type="text" id="id_number" name="id_number"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCustomerModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Update Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentBookingUuid = null;

function openCustomerModal(bookingUuid) {
    currentBookingUuid = bookingUuid;
    
    // Fetch current customer details
    fetch(`/api/checkin/${bookingUuid}/booking-details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('guest_name').value = data.guest.name || '';
            document.getElementById('guest_mobile').value = data.guest.mobile_number || '';
            document.getElementById('guest_email').value = data.guest.email || '';
            document.getElementById('guest_address').value = data.guest.address || '';
            document.getElementById('id_type').value = data.guest.id_type || '';
            document.getElementById('id_number').value = data.guest.id_number || '';
            
            document.getElementById('customerModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching customer details:', error);
            document.getElementById('customerModal').classList.remove('hidden');
        });
}

function closeCustomerModal() {
    document.getElementById('customerModal').classList.add('hidden');
    currentBookingUuid = null;
}

document.getElementById('customerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentBookingUuid) return;
    
    const formData = new FormData(this);
    
    fetch(`/checkin/${currentBookingUuid}/update-customer`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Customer details updated successfully!');
            closeCustomerModal();
            // Optionally reload the page to show updated details
            location.reload();
        } else {
            alert('Error updating customer details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating customer details');
    });
});
</script>
@endpush
@endsection
