@extends('layouts.staff')

@section('title', 'Bookings Calendar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bookings Calendar</h1>
            <p class="text-gray-600">View all bookings and reservations</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('staff.guest-service.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Calendar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Monthly Calendar</h3>
            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
        </div>
        
        <div class="p-6">
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 mb-4">
                <!-- Days of week -->
                <div class="text-center text-sm font-medium text-gray-500 py-2">Sun</div>
                <div class="text-center text-sm font-medium text-gray-500 py-2">Mon</div>
                <div class="text-center text-sm font-medium text-gray-500 py-2">Tue</div>
                <div class="text-center text-sm font-medium text-gray-500 py-2">Wed</div>
                <div class="text-center text-sm font-medium text-gray-500 py-2">Thu</div>
                <div class="text-center text-sm font-medium text-gray-500 py-2">Fri</div>
                <div class="text-center text-sm font-medium text-gray-500 py-2">Sat</div>
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 gap-1">
                @php
                    $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
                    $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
                    $startOfWeek = $startOfMonth->startOfWeek();
                    $endOfWeek = $endOfMonth->endOfWeek();
                    $current = $startOfWeek->copy();
                @endphp

                @while($current->lte($endOfWeek))
                    @php
                        $isCurrentMonth = $current->month === \Carbon\Carbon::now()->month;
                        $isToday = $current->isToday();
                        $dayBookings = $bookings->filter(function($booking) use ($current) {
                            return $booking->check_in_date->format('Y-m-d') === $current->format('Y-m-d') ||
                                   $booking->check_out_date->format('Y-m-d') === $current->format('Y-m-d');
                        });
                    @endphp

                    <div class="min-h-[100px] border border-gray-200 p-2 {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50' }} {{ $isToday ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="text-sm font-medium {{ $isCurrentMonth ? 'text-gray-900' : 'text-gray-400' }} {{ $isToday ? 'text-blue-600' : '' }}">
                            {{ $current->day }}
                        </div>
                        
                        @if($dayBookings->count() > 0)
                            <div class="mt-1 space-y-1">
                                @foreach($dayBookings->take(3) as $booking)
                                    @php
                                        $isCheckIn = $booking->check_in_date->format('Y-m-d') === $current->format('Y-m-d');
                                        $isCheckOut = $booking->check_out_date->format('Y-m-d') === $current->format('Y-m-d');
                                    @endphp
                                    <div class="text-xs p-1 rounded cursor-pointer {{ $isCheckIn ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }} hover:opacity-75"
                                         onclick="showBookingDetails({{ $booking->id }})"
                                         title="{{ $booking->guest->name }} - {{ $isCheckIn ? 'Check-in' : 'Check-out' }}">
                                        <div class="truncate">{{ $booking->guest->name }}</div>
                                        <div class="text-xs opacity-75">{{ $isCheckIn ? 'Check-in' : 'Check-out' }}</div>
                                    </div>
                                @endforeach
                                
                                @if($dayBookings->count() > 3)
                                    <div class="text-xs text-gray-500 text-center">
                                        +{{ $dayBookings->count() - 3 }} more
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    @php $current->addDay(); @endphp
                @endwhile
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Legend</h3>
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-blue-100 rounded"></div>
                <span class="text-sm text-gray-700">Check-in</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-red-100 rounded"></div>
                <span class="text-sm text-gray-700">Check-out</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 border-2 border-blue-500 rounded"></div>
                <span class="text-sm text-gray-700">Today</span>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div id="bookingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Booking Details</h3>
            </div>
            <div id="bookingDetails" class="p-6">
                <!-- Booking details will be loaded here -->
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button onclick="closeBookingModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showBookingDetails(reservationId) {
    fetch(`/staff/guest-service/booking/${reservationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const booking = data.reservation;
                const detailsHtml = `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-lg font-medium text-blue-600">${booking.guest.name.charAt(0)}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">${booking.guest.name}</h4>
                                <p class="text-sm text-gray-600">${booking.guest.mobile_number}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Property</label>
                                <p class="text-sm text-gray-900">${booking.property.name}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Room</label>
                                <p class="text-sm text-gray-900">${booking.accommodation ? booking.accommodation.name : 'N/A'}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Check-in</label>
                                <p class="text-sm text-gray-900">${new Date(booking.check_in_date).toLocaleDateString()} at ${new Date(booking.check_in_time).toLocaleTimeString()}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Check-out</label>
                                <p class="text-sm text-gray-900">${new Date(booking.check_out_date).toLocaleDateString()} at ${new Date(booking.check_out_time).toLocaleTimeString()}</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                booking.status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                booking.status === 'checked_in' ? 'bg-blue-100 text-blue-800' :
                                booking.status === 'checked_out' ? 'bg-gray-100 text-gray-800' :
                                'bg-red-100 text-red-800'
                            }">
                                ${booking.status.replace('_', ' ').toUpperCase()}
                            </span>
                        </div>
                        
                        ${booking.guest.email ? `
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-sm text-gray-900">${booking.guest.email}</p>
                        </div>
                        ` : ''}
                        
                        ${booking.guest.id_number ? `
                        <div>
                            <label class="text-sm font-medium text-gray-500">ID Number</label>
                            <p class="text-sm text-gray-900">${booking.guest.id_number}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                document.getElementById('bookingDetails').innerHTML = detailsHtml;
                document.getElementById('bookingModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load booking details.');
        });
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}
</script>
@endsection
