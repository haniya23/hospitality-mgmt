@extends('layouts.app')

@section('title', 'Guest Check-In')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">🏨 Guest Check-In</h1>
                        <p class="text-sm text-gray-600 mt-1">Complete guest check-in process</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Booking Reference</div>
                        <div class="font-mono text-lg font-semibold text-blue-600">{{ $reservation->confirmation_number }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto-fetch Booking Details Section -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">📋 Booking & Payment Details (Auto-fetched)</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="font-medium">Property:</span> {{ $reservation->accommodation->property->name }}<br>
                                <span class="font-medium">Room:</span> {{ $reservation->accommodation->display_name }}
                            </div>
                            <div>
                                <span class="font-medium">Guests:</span> {{ $reservation->adults }} adults, {{ $reservation->children }} children<br>
                                <span class="font-medium">Stay:</span> {{ $reservation->check_in_date->format('M d') }} - {{ $reservation->check_out_date->format('M d, Y') }}
                            </div>
                            <div>
                                <span class="font-medium">Total:</span> ₹{{ number_format($reservation->total_amount, 2) }}<br>
                                <span class="font-medium">Paid:</span> ₹{{ number_format($reservation->advance_paid, 2) }} | <span class="font-medium">Balance:</span> ₹{{ number_format($reservation->balance_pending, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Check-in Form -->
        <form action="{{ route('checkin.store', $reservation->uuid) }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Guest Information Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">1. Guest Information</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" id="guest_name" name="guest_name" value="{{ old('guest_name', $reservation->guest->name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @error('guest_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="guest_contact" class="block text-sm font-medium text-gray-700 mb-1">Contact Number *</label>
                            <input type="tel" id="guest_contact" name="guest_contact" value="{{ old('guest_contact', $reservation->guest->mobile_number) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @error('guest_contact')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-1">Email ID</label>
                            <input type="email" id="guest_email" name="guest_email" value="{{ old('guest_email', $reservation->guest->email) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('guest_email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                            <input type="text" id="nationality" name="nationality" value="{{ old('nationality') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('nationality')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="guest_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea id="guest_address" name="guest_address" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('guest_address', $reservation->guest->address) }}</textarea>
                        @error('guest_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="id_proof_type" class="block text-sm font-medium text-gray-700 mb-1">ID Proof Type</label>
                            <select id="id_proof_type" name="id_proof_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select ID Type</option>
                                <option value="passport" {{ old('id_proof_type', $reservation->guest->id_type) == 'passport' ? 'selected' : '' }}>Passport</option>
                                <option value="aadhaar" {{ old('id_proof_type', $reservation->guest->id_type) == 'aadhaar' ? 'selected' : '' }}>Aadhaar Card</option>
                                <option value="driving_license" {{ old('id_proof_type', $reservation->guest->id_type) == 'driving_license' ? 'selected' : '' }}>Driving License</option>
                                <option value="pan" {{ old('id_proof_type', $reservation->guest->id_type) == 'pan' ? 'selected' : '' }}>PAN Card</option>
                                <option value="voter_id" {{ old('id_proof_type', $reservation->guest->id_type) == 'voter_id' ? 'selected' : '' }}>Voter ID</option>
                            </select>
                            @error('id_proof_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="id_proof_number" class="block text-sm font-medium text-gray-700 mb-1">ID Proof Number</label>
                            <input type="text" id="id_proof_number" name="id_proof_number" value="{{ old('id_proof_number', $reservation->guest->id_number) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('id_proof_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stay Details Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">2. Stay Details</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="check_in_time" class="block text-sm font-medium text-gray-700 mb-1">Check-in Date & Time *</label>
                            <input type="datetime-local" id="check_in_time" name="check_in_time" 
                                   value="{{ old('check_in_time', now()->format('Y-m-d\TH:i')) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @error('check_in_time')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="expected_check_out_date" class="block text-sm font-medium text-gray-700 mb-1">Expected Check-out Date *</label>
                            <input type="date" id="expected_check_out_date" name="expected_check_out_date" 
                                   value="{{ old('expected_check_out_date', $reservation->check_out_date->format('Y-m-d')) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @error('expected_check_out_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-1">Special Requests</label>
                        <textarea id="special_requests" name="special_requests" rows="3" 
                                  placeholder="Early check-in, late check-out, extra bed, dietary requirements, etc."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('special_requests', $reservation->special_requests) }}</textarea>
                        @error('special_requests')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3" 
                                  placeholder="Any additional information or notes..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Confirmation Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">3. Confirmation</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="guest_signature" class="block text-sm font-medium text-gray-700 mb-1">Guest Signature</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 h-24 flex items-center justify-center">
                                <span class="text-gray-500 text-sm">Digital signature pad would go here</span>
                            </div>
                            <input type="hidden" id="guest_signature" name="guest_signature" value="{{ old('guest_signature') }}">
                        </div>
                        <div>
                            <label for="staff_signature" class="block text-sm font-medium text-gray-700 mb-1">Staff Signature</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 h-24 flex items-center justify-center">
                                <span class="text-gray-500 text-sm">Staff signature pad would go here</span>
                            </div>
                            <input type="hidden" id="staff_signature" name="staff_signature" value="{{ old('staff_signature') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('bookings.show', $reservation->uuid) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Complete Check-in
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Check-in form loaded for booking:', '{{ $reservation->confirmation_number }}');
    });
</script>
@endpush
@endsection
