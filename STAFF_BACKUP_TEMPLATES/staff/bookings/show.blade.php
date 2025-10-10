@extends('layouts.staff')

@section('title', 'Booking Details - ' . $booking->confirmation_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
            <p class="text-gray-600">Confirmation: {{ $booking->confirmation_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('staff.bookings') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Bookings
            </a>
        </div>
    </div>

    <!-- Booking Status Alert -->
    @if($booking->status === 'pending')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Booking Pending Confirmation</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>This booking is still pending confirmation. Please contact the property owner for confirmation.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Booking Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Property & Accommodation Details -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Property & Accommodation</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Property</label>
                        <p class="text-gray-900">{{ $booking->accommodation->property->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Accommodation</label>
                        <p class="text-gray-900">{{ $booking->accommodation->display_name }}</p>
                    </div>
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
                        <p class="text-gray-900">{{ $booking->check_in_date->diffInDays($booking->check_out_date) }} nights</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Guests</label>
                        <p class="text-gray-900">{{ $booking->adults }} adults, {{ $booking->children }} children</p>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                    <button onclick="openCustomerEditModal()" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-edit mr-2"></i>
                        Update Details
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Customer Name</label>
                        <p class="text-gray-900" id="customer-name">
                            @if($booking->isB2bBooking())
                                {{ $booking->b2bPartner->getOrCreateReservedCustomer()->name }}
                            @else
                                {{ $booking->guest->name }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Mobile Number</label>
                        <p class="text-gray-900" id="customer-mobile">
                            @if($booking->isB2bBooking())
                                {{ $booking->b2bPartner->getOrCreateReservedCustomer()->mobile_number }}
                            @else
                                {{ $booking->guest->mobile_number }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900" id="customer-email">
                            @if($booking->isB2bBooking())
                                {{ $booking->b2bPartner->getOrCreateReservedCustomer()->email ?: 'Not provided' }}
                            @else
                                {{ $booking->guest->email ?: 'Not provided' }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">ID Type</label>
                        <p class="text-gray-900" id="customer-id-type">
                            @if($booking->isB2bBooking())
                                {{ $booking->b2bPartner->getOrCreateReservedCustomer()->id_type ?: 'Not provided' }}
                            @else
                                {{ $booking->guest->id_type ?: 'Not provided' }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">ID Number</label>
                        <p class="text-gray-900" id="customer-id-number">
                            @if($booking->isB2bBooking())
                                {{ $booking->b2bPartner->getOrCreateReservedCustomer()->id_number ?: 'Not provided' }}
                            @else
                                {{ $booking->guest->id_number ?: 'Not provided' }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Address</label>
                        <p class="text-gray-900" id="customer-address">
                            @if($booking->isB2bBooking())
                                {{ $booking->b2bPartner->getOrCreateReservedCustomer()->address ?: 'Not provided' }}
                            @else
                                {{ $booking->guest->address ?: 'Not provided' }}
                            @endif
                        </p>
                    </div>
                </div>

                @if($booking->isB2bBooking())
                    <div class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-handshake text-purple-600 mr-2"></i>
                            <span class="text-sm font-medium text-purple-800">B2B Booking</span>
                        </div>
                        <p class="text-sm text-purple-700 mt-1">
                            Partner: {{ $booking->b2bPartner->partner_name }}
                        </p>
                        <p class="text-xs text-purple-600 mt-1">
                            This is a B2B booking. Customer details shown are for the reserved customer.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Special Requests -->
            @if($booking->special_requests)
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Special Requests</h3>
                    <p class="text-gray-700">{{ $booking->special_requests }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar Information -->
        <div class="space-y-6">
            <!-- Booking Status -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                            @elseif($booking->status === 'checked_out') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Confirmation</span>
                        <span class="text-sm font-medium text-gray-900">{{ $booking->confirmation_number }}</span>
                    </div>
                    @if($booking->confirmed_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Confirmed At</span>
                            <span class="text-sm text-gray-900">{{ $booking->confirmed_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Amount</span>
                        <span class="text-sm font-medium text-gray-900">₹{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Advance Paid</span>
                        <span class="text-sm font-medium text-gray-900">₹{{ number_format($booking->advance_paid, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Balance Pending</span>
                        <span class="text-sm font-medium text-gray-900">₹{{ number_format($booking->balance_pending, 2) }}</span>
                    </div>
                    @if($booking->rate_override)
                        <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-xs text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Rate Override Applied
                            </p>
                            @if($booking->override_reason)
                                <p class="text-xs text-yellow-700 mt-1">{{ $booking->override_reason }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Check-in/Check-out Records -->
            @if($booking->checkInRecord || $booking->checkOutRecord)
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Check-in/Check-out</h3>
                    <div class="space-y-3">
                        @if($booking->checkInRecord)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Checked In</span>
                                <span class="text-sm text-gray-900">{{ $booking->checkInRecord->created_at->format('M d, Y H:i') }}</span>
                            </div>
                        @endif
                        @if($booking->checkOutRecord)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Checked Out</span>
                                <span class="text-sm text-gray-900">{{ $booking->checkOutRecord->created_at->format('M d, Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Customer Edit Modal -->
<div id="customerEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Customer Details</h3>
                <button onclick="closeCustomerEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="customerEditForm" class="space-y-4">
                @csrf
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name *</label>
                    <input type="text" id="customer_name" name="customer_name" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="customer_mobile" class="block text-sm font-medium text-gray-700">Mobile Number *</label>
                    <input type="text" id="customer_mobile" name="customer_mobile" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="customer_email" name="customer_email"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="customer_id_type" class="block text-sm font-medium text-gray-700">ID Type</label>
                    <select id="customer_id_type" name="customer_id_type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select ID Type</option>
                        <option value="aadhar">Aadhar Card</option>
                        <option value="pan">PAN Card</option>
                        <option value="passport">Passport</option>
                        <option value="driving_license">Driving License</option>
                        <option value="voter_id">Voter ID</option>
                    </select>
                </div>
                
                <div>
                    <label for="customer_id_number" class="block text-sm font-medium text-gray-700">ID Number</label>
                    <input type="text" id="customer_id_number" name="customer_id_number"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="customer_address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="customer_address" name="customer_address" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeCustomerEditModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openCustomerEditModal() {
    // Load current customer details
    fetch(`{{ route('staff.bookings.customer-details', $booking) }}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('customer_name').value = data.customer.name;
            document.getElementById('customer_mobile').value = data.customer.mobile_number;
            document.getElementById('customer_email').value = data.customer.email || '';
            document.getElementById('customer_id_type').value = data.customer.id_type || '';
            document.getElementById('customer_id_number').value = data.customer.id_number || '';
            document.getElementById('customer_address').value = data.customer.address || '';
            
            document.getElementById('customerEditModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading customer details:', error);
            alert('Failed to load customer details');
        });
}

function closeCustomerEditModal() {
    document.getElementById('customerEditModal').classList.add('hidden');
}

// Handle form submission
document.getElementById('customerEditForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`{{ route('staff.bookings.update-customer', $booking) }}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the displayed customer details
            document.getElementById('customer-name').textContent = formData.get('customer_name');
            document.getElementById('customer-mobile').textContent = formData.get('customer_mobile');
            document.getElementById('customer-email').textContent = formData.get('customer_email') || 'Not provided';
            document.getElementById('customer-id-type').textContent = formData.get('customer_id_type') || 'Not provided';
            document.getElementById('customer-id-number').textContent = formData.get('customer_id_number') || 'Not provided';
            document.getElementById('customer-address').textContent = formData.get('customer_address') || 'Not provided';
            
            closeCustomerEditModal();
            
            // Show success message
            alert('Customer details updated successfully!');
        } else {
            alert('Failed to update customer details: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error updating customer details:', error);
        alert('Failed to update customer details');
    });
});
</script>
@endpush
