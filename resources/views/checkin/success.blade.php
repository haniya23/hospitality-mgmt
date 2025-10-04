@extends('layouts.app')

@section('title', 'Check-in Successful')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-8 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">✅ Check-in Successful!</h1>
                <p class="text-gray-600">Guest has been successfully checked in</p>
            </div>
        </div>

        <!-- Check-in Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Check-in Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Guest Information</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Name</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->guest_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Contact</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->guest_contact }}</dd>
                            </div>
                            @if($checkIn->guest_email)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Email</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->guest_email }}</dd>
                            </div>
                            @endif
                            @if($checkIn->nationality)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Nationality</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->nationality }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Stay Information</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Property</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->reservation->accommodation->property->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Room</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->reservation->accommodation->display_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Check-in Time</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->check_in_time->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Expected Check-out</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->expected_check_out_date->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                @if($checkIn->special_requests)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Special Requests</h3>
                    <p class="text-sm text-gray-900">{{ $checkIn->special_requests }}</p>
                </div>
                @endif
                
                @if($checkIn->notes)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Notes</h3>
                    <p class="text-sm text-gray-900">{{ $checkIn->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Booking Summary</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $checkIn->reservation->confirmation_number }}</div>
                        <div class="text-sm text-gray-500">Booking Reference</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">₹{{ number_format($checkIn->reservation->total_amount, 2) }}</div>
                        <div class="text-sm text-gray-500">Total Amount</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">₹{{ number_format($checkIn->reservation->balance_pending, 2) }}</div>
                        <div class="text-sm text-gray-500">Balance Pending</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('checkout.show', $checkIn->reservation->uuid) }}" 
               class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Proceed to Check-out
            </a>
            <a href="{{ route('bookings.show', $checkIn->reservation->uuid) }}" 
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                View Booking Details
            </a>
            <a href="{{ route('bookings.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Back to Bookings
            </a>
        </div>
    </div>
</div>
@endsection
