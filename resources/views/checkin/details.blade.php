@extends('layouts.app')

@section('title', 'Check-in Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">🏨 Check-in Details</h1>
                        <p class="text-sm text-gray-600 mt-1">Complete check-in information</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Check-in ID</div>
                        <div class="font-mono text-lg font-semibold text-blue-600">{{ $checkIn->uuid }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guest Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Guest Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Personal Details</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Full Name</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->guest_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Contact Number</dt>
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
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Identification</h3>
                        <dl class="space-y-2">
                            @if($checkIn->id_proof_type)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">ID Proof Type</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $checkIn->id_proof_type)) }}</dd>
                            </div>
                            @endif
                            @if($checkIn->id_proof_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">ID Proof Number</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $checkIn->id_proof_number }}</dd>
                            </div>
                            @endif
                            @if($checkIn->guest_address)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Address</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->guest_address }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stay Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Stay Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Accommodation</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Property</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->reservation->accommodation->property->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Room Type</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->reservation->accommodation->display_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Booking Reference</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $checkIn->reservation->confirmation_number }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Timing</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Check-in Time</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->check_in_time->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Expected Check-out</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->expected_check_out_date->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Guests</dt>
                                <dd class="text-sm text-gray-900">{{ $checkIn->reservation->adults }} adults, {{ $checkIn->reservation->children }} children</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Special Requests & Notes -->
        @if($checkIn->special_requests || $checkIn->notes)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Special Requests & Notes</h2>
            </div>
            <div class="p-6">
                @if($checkIn->special_requests)
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Special Requests</h3>
                    <p class="text-sm text-gray-900">{{ $checkIn->special_requests }}</p>
                </div>
                @endif
                @if($checkIn->notes)
                <div>
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Additional Notes</h3>
                    <p class="text-sm text-gray-900">{{ $checkIn->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Staff Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Staff Information</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-green-600"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $checkIn->staff->name ?? 'Unknown Staff' }}</h3>
                        <p class="text-sm text-gray-600">Processed check-in on {{ $checkIn->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('checkin.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Back to Check-ins
            </a>
            @if($checkIn->reservation->status === 'checked_in')
            <a href="{{ route('checkout.show', $checkIn->reservation->uuid) }}" 
               class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Proceed to Check-out
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
