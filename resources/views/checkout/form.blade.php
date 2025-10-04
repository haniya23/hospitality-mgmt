@extends('layouts.app')

@section('title', 'Guest Check-Out')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">🚪 Guest Check-Out</h1>
                        <p class="text-sm text-gray-600 mt-1">Complete guest check-out process</p>
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
                                <span class="font-medium">Guest:</span> {{ $reservation->guest->name }}<br>
                                <span class="font-medium">Property:</span> {{ $reservation->accommodation->property->name }}
                            </div>
                            <div>
                                <span class="font-medium">Room:</span> {{ $reservation->accommodation->display_name }}<br>
                                <span class="font-medium">Check-in:</span> {{ $reservation->checkInRecord->check_in_time->format('M d, g:i A') }}
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

        <!-- Check-out Form -->
        <form action="{{ route('checkout.store', $reservation->uuid) }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Guest & Room Details Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">1. Guest & Room Details</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-1">Guest Name *</label>
                            <input type="text" id="guest_name" name="guest_name" value="{{ old('guest_name', $reservation->guest->name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @error('guest_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                            <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('room_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stay Review Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">2. Stay Review</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="check_out_time" class="block text-sm font-medium text-gray-700 mb-1">Check-out Date & Time *</label>
                        <input type="datetime-local" id="check_out_time" name="check_out_time" 
                               value="{{ old('check_out_time', now()->format('Y-m-d\TH:i')) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        @error('check_out_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Services Used</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="services_used[]" value="restaurant" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Restaurant</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="services_used[]" value="spa" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Spa</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="services_used[]" value="minibar" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Minibar</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="services_used[]" value="transport" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Transport</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="services_used[]" value="laundry" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Laundry</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="services_used[]" value="room_service" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Room Service</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="services_used[]" value="parking" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Parking</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="services_used[]" value="wifi" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">WiFi</span>
                            </label>
                        </div>
                        @error('services_used')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="late_checkout_charges" class="block text-sm font-medium text-gray-700 mb-1">Late Check-out Charges</label>
                            <input type="number" id="late_checkout_charges" name="late_checkout_charges" value="{{ old('late_checkout_charges', 0) }}" 
                                   min="0" step="0.01" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('late_checkout_charges')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="service_notes" class="block text-sm font-medium text-gray-700 mb-1">Service Notes</label>
                            <textarea id="service_notes" name="service_notes" rows="3" 
                                      placeholder="Additional service charges, damages, etc."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('service_notes') }}</textarea>
                            @error('service_notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Final Settlement Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">3. Final Settlement</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Auto-calculated from booking system:</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Original Total:</span>
                                <span class="font-semibold">₹{{ number_format($reservation->total_amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Advance Paid:</span>
                                <span class="font-semibold">₹{{ number_format($reservation->advance_paid, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Balance Pending:</span>
                                <span class="font-semibold">₹{{ number_format($reservation->balance_pending, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="final_bill" class="block text-sm font-medium text-gray-700 mb-1">Final Bill Amount *</label>
                            <input type="number" id="final_bill" name="final_bill" value="{{ old('final_bill', $reservation->total_amount) }}" 
                                   min="0" step="0.01" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @error('final_bill')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="deposit_refund" class="block text-sm font-medium text-gray-700 mb-1">Deposit Refund</label>
                            <input type="number" id="deposit_refund" name="deposit_refund" value="{{ old('deposit_refund', 0) }}" 
                                   min="0" step="0.01" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('deposit_refund')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status *</label>
                            <select id="payment_status" name="payment_status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Select Status</option>
                                <option value="completed" {{ old('payment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="refunded" {{ old('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                            @error('payment_status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-1">Payment Notes</label>
                            <textarea id="payment_notes" name="payment_notes" rows="3" 
                                      placeholder="Payment method, transaction details, etc."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('payment_notes') }}</textarea>
                            @error('payment_notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">4. Feedback</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex space-x-2">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="flex items-center">
                                <input type="radio" name="rating" value="{{ $i }}" class="text-yellow-400 focus:ring-yellow-500">
                                <svg class="w-6 h-6 text-yellow-400 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="feedback_comments" class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                        <textarea id="feedback_comments" name="feedback_comments" rows="3" 
                                  placeholder="Share your experience..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('feedback_comments') }}</textarea>
                        @error('feedback_comments')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Completion Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">5. Completion</h2>
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
            <div class="flex justify-end space-x-4 pb-20 lg:pb-8">
                <a href="{{ route('bookings.show', $reservation->uuid) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Complete Check-out
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-calculate final bill when late checkout charges change
    document.addEventListener('DOMContentLoaded', function() {
        const lateCheckoutInput = document.getElementById('late_checkout_charges');
        const finalBillInput = document.getElementById('final_bill');
        const originalTotal = {{ $reservation->total_amount }};
        
        lateCheckoutInput.addEventListener('input', function() {
            const lateCharges = parseFloat(this.value) || 0;
            const newTotal = originalTotal + lateCharges;
            finalBillInput.value = newTotal.toFixed(2);
        });
        
        console.log('Check-out form loaded for booking:', '{{ $reservation->confirmation_number }}');
    });
</script>
@endpush
@endsection
