@extends('layouts.staff')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Booking</h1>
            <p class="text-gray-600">Add a new reservation for your assigned properties.</p>
        </div>
        <a href="{{ route('staff.bookings') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Bookings
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.bookings.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Property & Accommodation</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Property Selection -->
                <div>
                    <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                    <select name="property_id" id="property_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Property</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Accommodation Selection -->
                <div>
                    <label for="accommodation_id" class="block text-sm font-medium text-gray-700 mb-2">Accommodation</label>
                    <select name="accommodation_id" id="accommodation_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Accommodation</option>
                        @foreach($accommodations as $accommodation)
                            <option value="{{ $accommodation->id }}" data-property="{{ $accommodation->property_id }}" {{ old('accommodation_id') == $accommodation->id ? 'selected' : '' }}>
                                {{ $accommodation->display_name }} - {{ $accommodation->property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Booking Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Check-in Date -->
                <div>
                    <label for="check_in_date" class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                    <input type="date" name="check_in_date" id="check_in_date" value="{{ old('check_in_date') }}" min="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Check-out Date -->
                <div>
                    <label for="check_out_date" class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                    <input type="date" name="check_out_date" id="check_out_date" value="{{ old('check_out_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Adults -->
                <div>
                    <label for="adults" class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                    <input type="number" name="adults" id="adults" value="{{ old('adults', 1) }}" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Children -->
                <div>
                    <label for="children" class="block text-sm font-medium text-gray-700 mb-2">Children</label>
                    <input type="number" name="children" id="children" value="{{ old('children', 0) }}" min="0" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Booking Type -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Booking Type</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="booking_type" value="per_day" {{ old('booking_type', 'per_day') == 'per_day' ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm text-gray-700">Per Day</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="booking_type" value="per_person" {{ old('booking_type') == 'per_person' ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm text-gray-700">Per Person</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Guest Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Guest Name -->
                <div>
                    <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-2">Guest Name</label>
                    <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Guest Mobile -->
                <div>
                    <label for="guest_mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                    <input type="text" name="guest_mobile" id="guest_mobile" value="{{ old('guest_mobile') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Guest Email -->
                <div>
                    <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                    <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">B2B Partner (Optional)</h2>
            
            <div class="space-y-4">
                <!-- B2B Partner Selection -->
                <div>
                    <label for="b2b_partner_id" class="block text-sm font-medium text-gray-700 mb-2">B2B Partner</label>
                    <select name="b2b_partner_id" id="b2b_partner_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select B2B Partner (Optional)</option>
                        @foreach($b2bPartners as $partner)
                            <option value="{{ $partner->uuid }}" {{ old('b2b_partner_id') == $partner->uuid ? 'selected' : '' }}>
                                {{ $partner->partner_name }} ({{ $partner->mobile_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Use B2B Reserved Customer -->
                <div class="flex items-center">
                    <input type="checkbox" name="use_b2b_reserved_customer" id="use_b2b_reserved_customer" value="1" {{ old('use_b2b_reserved_customer') ? 'checked' : '' }} class="mr-2">
                    <label for="use_b2b_reserved_customer" class="text-sm text-gray-700">Use B2B Partner's Reserved Customer</label>
                </div>

                <!-- Commission Details -->
                <div id="commission_details" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="commission_percentage" class="block text-sm font-medium text-gray-700 mb-2">Commission Percentage</label>
                        <input type="number" name="commission_percentage" id="commission_percentage" value="{{ old('commission_percentage', 10) }}" min="0" max="100" step="0.1" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="commission_amount" class="block text-sm font-medium text-gray-700 mb-2">Commission Amount (₹)</label>
                        <input type="number" name="commission_amount" id="commission_amount" value="{{ old('commission_amount') }}" min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Total Amount -->
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">Total Amount (₹)</label>
                    <input type="number" name="total_amount" id="total_amount" value="{{ old('total_amount') }}" min="0" step="0.01" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Advance Paid -->
                <div>
                    <label for="advance_paid" class="block text-sm font-medium text-gray-700 mb-2">Advance Paid (₹)</label>
                    <input type="number" name="advance_paid" id="advance_paid" value="{{ old('advance_paid', 0) }}" min="0" step="0.01" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Additional Information</h2>
            
            <!-- Special Requests -->
            <div class="mb-4">
                <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                <textarea name="special_requests" id="special_requests" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any special requests or notes...">{{ old('special_requests') }}</textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('staff.bookings') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Create Booking
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter accommodations based on selected property
    const propertySelect = document.getElementById('property_id');
    const accommodationSelect = document.getElementById('accommodation_id');
    const accommodationOptions = accommodationSelect.querySelectorAll('option[data-property]');

    propertySelect.addEventListener('change', function() {
        const selectedPropertyId = this.value;
        
        // Reset accommodation selection
        accommodationSelect.value = '';
        
        // Show/hide accommodation options based on property
        accommodationOptions.forEach(option => {
            if (option.dataset.property === selectedPropertyId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });

    // Show/hide commission details based on B2B partner selection
    const b2bPartnerSelect = document.getElementById('b2b_partner_id');
    const commissionDetails = document.getElementById('commission_details');

    b2bPartnerSelect.addEventListener('change', function() {
        if (this.value) {
            commissionDetails.classList.remove('hidden');
        } else {
            commissionDetails.classList.add('hidden');
        }
    });

    // Calculate balance automatically
    const totalAmountInput = document.getElementById('total_amount');
    const advancePaidInput = document.getElementById('advance_paid');

    function calculateBalance() {
        const total = parseFloat(totalAmountInput.value) || 0;
        const advance = parseFloat(advancePaidInput.value) || 0;
        const balance = total - advance;
        
        // You can display the balance somewhere if needed
        console.log('Balance:', balance);
    }

    totalAmountInput.addEventListener('input', calculateBalance);
    advancePaidInput.addEventListener('input', calculateBalance);
});
</script>
@endpush
@endsection
