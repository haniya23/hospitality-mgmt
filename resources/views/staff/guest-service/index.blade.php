@extends('layouts.staff')

@section('title', 'Guest Service Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Guest Service Dashboard</h1>
            <p class="text-gray-600">Manage guest check-ins, check-outs, and services</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('staff.guest-service.calendar') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-calendar-alt mr-2"></i>
                View Calendar
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-sign-in-alt text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Check-ins</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingCheckins->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-sign-out-alt text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Check-outs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingCheckouts->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed Check-ins</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $completedCheckins->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-check text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Today's Bookings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayBookings->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Check-ins -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Pending Check-ins</h3>
            <p class="text-sm text-gray-600">Guests ready to check in today</p>
        </div>
        
        <div class="overflow-x-auto">
            @if($pendingCheckins->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingCheckins as $booking)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">{{ substr($booking->guest->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->guest->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->guest->mobile_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->property->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->accommodation->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->check_in_date)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openCheckInModal({{ $booking->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                        <i class="fas fa-sign-in-alt mr-1"></i>Check In
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-sign-in-alt text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No pending check-ins</h3>
                    <p class="text-sm text-gray-500">All guests have been checked in for today.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending Check-outs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Pending Check-outs</h3>
            <p class="text-sm text-gray-600">Guests ready to check out today</p>
        </div>
        
        <div class="overflow-x-auto">
            @if($pendingCheckouts->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingCheckouts as $booking)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-red-600">{{ substr($booking->guest->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->guest->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->guest->mobile_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->property->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->accommodation->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->check_out_date)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openCheckOutModal({{ $booking->id }})" 
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt mr-1"></i>Check Out
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-sign-out-alt text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No pending check-outs</h3>
                    <p class="text-sm text-gray-500">All guests have been checked out for today.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Check-in Modal -->
<div id="checkInModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Process Check-in</h3>
            </div>
            <form id="checkInForm" class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="id_verified" name="id_verified" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" required>
                        <label for="id_verified" class="text-sm font-medium text-gray-700">ID Verified</label>
                    </div>
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="payment_verified" name="payment_verified" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" required>
                        <label for="payment_verified" class="text-sm font-medium text-gray-700">Payment Verified</label>
                    </div>
                    <div>
                        <label for="checkin_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea id="checkin_notes" name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCheckInModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200">
                        Complete Check-in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Check-out Modal -->
<div id="checkOutModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Process Check-out</h3>
            </div>
            <form id="checkOutForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label for="room_condition" class="block text-sm font-medium text-gray-700 mb-2">Room Condition</label>
                        <select id="room_condition" name="room_condition" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                            <option value="">Select condition</option>
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="amenities_returned" name="amenities_returned" class="rounded border-gray-300 text-red-600 focus:ring-red-500" required>
                        <label for="amenities_returned" class="text-sm font-medium text-gray-700">All amenities returned</label>
                    </div>
                    <div>
                        <label for="checkout_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea id="checkout_notes" name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCheckOutModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200">
                        Complete Check-out
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentReservationId = null;

function openCheckInModal(reservationId) {
    currentReservationId = reservationId;
    document.getElementById('checkInModal').classList.remove('hidden');
}

function closeCheckInModal() {
    document.getElementById('checkInModal').classList.add('hidden');
    document.getElementById('checkInForm').reset();
    currentReservationId = null;
}

function openCheckOutModal(reservationId) {
    currentReservationId = reservationId;
    document.getElementById('checkOutModal').classList.remove('hidden');
}

function closeCheckOutModal() {
    document.getElementById('checkOutModal').classList.add('hidden');
    document.getElementById('checkOutForm').reset();
    currentReservationId = null;
}

// Check-in form submission
document.getElementById('checkInForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        id_verified: formData.get('id_verified') === 'on',
        payment_verified: formData.get('payment_verified') === 'on',
        notes: formData.get('notes')
    };
    
    try {
        const response = await fetch(`/staff/guest-service/check-in/${currentReservationId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Check-in completed successfully!');
            closeCheckInModal();
            location.reload(); // Refresh to update the lists
        } else {
            alert('Failed to complete check-in: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while processing check-in.');
    }
});

// Check-out form submission
document.getElementById('checkOutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        room_condition: formData.get('room_condition'),
        amenities_returned: formData.get('amenities_returned') === 'on',
        notes: formData.get('notes')
    };
    
    try {
        const response = await fetch(`/staff/guest-service/check-out/${currentReservationId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Check-out completed successfully!');
            closeCheckOutModal();
            location.reload(); // Refresh to update the lists
        } else {
            alert('Failed to complete check-out: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while processing check-out.');
    }
});
</script>
@endsection
