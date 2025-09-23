@extends('layouts.app')

@section('title', 'Create Booking')

@section('header')
    @include('partials.bookings.create-header')
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('bookings.store') }}" x-data="bookingCreateForm()" x-init="init()" class="space-y-4 sm:space-y-6">
        @csrf
        
        <!-- Guest Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Guest Information</h3>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button type="button" @click="isNewGuest = true" :class="isNewGuest ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">New</button>
                    <button type="button" @click="isNewGuest = false" :class="!isNewGuest ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Existing</button>
                </div>
            </div>
            
            <div x-show="!isNewGuest" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Customer</label>
                <div class="relative">
                    <input type="text" x-model="guestSearch" @input="searchGuests()" placeholder="Search by name or mobile..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <div x-show="filteredGuests.length > 0" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-40 overflow-y-auto">
                        <template x-for="guest in filteredGuests" :key="guest.id">
                            <div @click="selectGuest(guest)" class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                <div class="font-medium" x-text="guest.name"></div>
                                <div class="text-sm text-gray-500" x-text="guest.mobile_number"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Name</label>
                    <input type="text" name="guest_name" x-model="guestName" value="{{ old('guest_name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('guest_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                    <input type="text" name="guest_mobile" x-model="guestMobile" value="{{ old('guest_mobile') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('guest_mobile')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                    <input type="email" name="guest_email" x-model="guestEmail" value="{{ old('guest_email') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('guest_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- B2B Partner -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Booking Type</h3>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button type="button" @click="isB2B = false" :class="!isB2B ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Direct</button>
                    <button type="button" @click="isB2B = true" :class="isB2B ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">B2B</button>
                </div>
            </div>
            
            <div x-show="isB2B">
                <label class="block text-sm font-medium text-gray-700 mb-2">B2B Partner</label>
                <select name="b2b_partner_id" x-model="selectedPartner" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Select Partner</option>
                    <template x-for="partner in partners" :key="partner.id">
                        <option :value="partner.id" x-text="partner.partner_name"></option>
                    </template>
                </select>
            </div>
        </div>

        <!-- Property & Accommodation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Property & Accommodation</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                    <select name="property_id" x-model="selectedProperty" @change="loadAccommodations()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                        <option value="">Select Property</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Accommodation</label>
                    <select name="accommodation_id" x-model="selectedAccommodation" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                        <option value="">Select Accommodation</option>
                    </select>
                    @error('accommodation_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                    <input type="date" name="check_in_date" value="{{ old('check_in_date') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('check_in_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                    <input type="date" name="check_out_date" value="{{ old('check_out_date') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('check_out_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                    <input type="number" name="adults" value="{{ old('adults', 1) }}" min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('adults')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Children</label>
                    <input type="number" name="children" value="{{ old('children', 0) }}" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('children')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount') }}" step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('total_amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Advance Paid</label>
                    <input type="number" name="advance_paid" value="{{ old('advance_paid') }}" step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('advance_paid')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('bookings.index') }}" class="w-full sm:w-auto px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-center">
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Create Booking
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function bookingCreateForm() {
    return {
        selectedProperty: '{{ old('property_id') }}',
        selectedAccommodation: '{{ old('accommodation_id') }}',
        isNewGuest: true,
        isB2B: false,
        guestSearch: '',
        guestName: '{{ old('guest_name') }}',
        guestMobile: '{{ old('guest_mobile') }}',
        guestEmail: '{{ old('guest_email') }}',
        selectedPartner: '{{ old('b2b_partner_id') }}',
        guests: [],
        filteredGuests: [],
        partners: [],
        
        async init() {
            await this.loadGuests();
            await this.loadPartners();
        },
        
        async loadGuests() {
            try {
                const response = await fetch('/api/guests');
                this.guests = await response.json();
            } catch (error) {
                console.error('Error loading guests:', error);
            }
        },
        
        async loadPartners() {
            try {
                const response = await fetch('/api/partners');
                this.partners = await response.json();
            } catch (error) {
                console.error('Error loading partners:', error);
            }
        },
        
        searchGuests() {
            if (this.guestSearch.length < 2) {
                this.filteredGuests = [];
                return;
            }
            
            this.filteredGuests = this.guests.filter(guest => 
                guest.name.toLowerCase().includes(this.guestSearch.toLowerCase()) ||
                guest.mobile_number.includes(this.guestSearch)
            ).slice(0, 5);
        },
        
        selectGuest(guest) {
            this.guestName = guest.name;
            this.guestMobile = guest.mobile_number;
            this.guestEmail = guest.email || '';
            this.guestSearch = '';
            this.filteredGuests = [];
        },
        
        async loadAccommodations() {
            if (!this.selectedProperty) return;
            
            try {
                const response = await fetch(`/api/properties/${this.selectedProperty}/accommodations`);
                const accommodations = await response.json();
                
                const select = document.querySelector('select[name="accommodation_id"]');
                select.innerHTML = '<option value="">Select Accommodation</option>';
                
                accommodations.forEach(acc => {
                    const option = document.createElement('option');
                    option.value = acc.id;
                    option.textContent = `${acc.display_name} - ₹${acc.base_price}`;
                    select.appendChild(option);
                });
                
                this.selectedAccommodation = '';
            } catch (error) {
                console.error('Error loading accommodations:', error);
            }
        }
    }
}
</script>
@endpush