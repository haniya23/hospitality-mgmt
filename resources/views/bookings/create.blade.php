@extends('layouts.app')

@section('title', 'Create Booking')

@section('header')
    @include('partials.bookings.create-header')
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('bookings.store') }}" x-data="bookingCreateForm()" x-init="init()" class="space-y-4 sm:space-y-6">
        @csrf
        
        <!-- 1. Check-in Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Check-in Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                    <input type="date" name="check_in_date" x-model="checkInDate" @change="updateCheckOutDate()" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('check_in_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                    <input type="date" name="check_out_date" x-model="checkOutDate" @change="calculateDaysNights()" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('check_out_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                    <input type="number" name="adults" x-model="adults" @change="calculateTotalGuests(); calculateAmount()" value="{{ old('adults', 1) }}" min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('adults')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Children</label>
                    <input type="number" name="children" x-model="children" @change="calculateTotalGuests(); calculateAmount()" value="{{ old('children', 0) }}" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('children')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Booking Type -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Booking Type</label>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button type="button" @click="bookingType = 'per_day'; calculateAmount()" 
                            :class="bookingType === 'per_day' ? 'bg-white shadow-sm' : ''" 
                            class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Per Day</button>
                    <button type="button" @click="bookingType = 'per_person'; calculateAmount()" 
                            :class="bookingType === 'per_person' ? 'bg-white shadow-sm' : ''" 
                            class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Per Person</button>
                </div>
                <input type="hidden" name="booking_type" x-model="bookingType">
            </div>
            
            
            <!-- Per Person Price (shown when per_person is selected) -->
            <div x-show="bookingType === 'per_person'" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Per Person (₹)</label>
                <input type="number" x-model="perPersonPrice" @input="calculateAmount()" 
                       step="0.01" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Default: ₹1,000 per person per day</p>
            </div>
            <!-- Days and Nights Display -->
            <div x-show="days > 0" class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600" x-text="days"></div>
                            <div class="text-sm text-blue-500">Days</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600" x-text="nights"></div>
                            <div class="text-sm text-blue-500">Nights</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Total Guests</div>
                        <div class="text-lg font-semibold text-gray-800" x-text="totalGuests"></div>
                    </div>
                </div>
            </div>
            
            <!-- Special Requests -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests (Optional)</label>
                <textarea name="special_requests" rows="3" placeholder="Any special requests or notes for this booking..." 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('special_requests') }}</textarea>
                @error('special_requests')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 2. Amount Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Amount Calculation</h3>
            
            <!-- Default Amount Display -->
            <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700" x-text="bookingType === 'per_day' ? 'Default Amount (Base Price × Days)' : 'Default Amount (Per Person Price × Guests × Days)'"></span>
                    <span class="text-lg font-bold text-gray-900" x-text="'₹' + defaultAmount.toLocaleString()"></span>
                </div>
                <div class="text-xs text-gray-500">
                    <span x-show="bookingType === 'per_day'" x-text="'₹' + (selectedAccommodationPrice || 0).toLocaleString() + ' × ' + days + ' days'"></span>
                    <span x-show="bookingType === 'per_person'" x-text="'₹' + perPersonPrice.toLocaleString() + ' × ' + totalGuests + ' guests × ' + days + ' days'"></span>
                </div>
            </div>
            
            <!-- Override Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount (Override)</label>
                    <input type="number" name="total_amount" x-model="totalAmount" step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    <p class="text-xs text-gray-500 mt-1">Leave empty to use default amount</p>
                    @error('total_amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Advance Paid</label>
                    <input type="number" name="advance_paid" x-model="advancePaid" step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('advance_paid')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Balance Calculation -->
            <div x-show="totalAmount > 0" class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Balance Pending</span>
                    <span class="text-lg font-bold text-green-600" x-text="'₹' + balancePending.toLocaleString()"></span>
                </div>
            </div>
        </div>

        <!-- 3. B2B Commission Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">B2B Commission</h3>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button type="button" @click="isB2B = false" :class="!isB2B ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Direct</button>
                    <button type="button" @click="isB2B = true; calculateCommission()" :class="isB2B ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">B2B</button>
                </div>
            </div>
            
            <div x-show="isB2B" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">B2B Partner</label>
                    <select name="b2b_partner_id" x-model="selectedPartner" @change="calculateCommission()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent select2-dropdown">
                        <option value="">Select Partner</option>
                        <template x-for="partner in partners" :key="partner.id">
                            <option :value="partner.id" x-text="partner.partner_name"></option>
                        </template>
                    </select>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Commission Type</label>
                        <div class="flex bg-gray-100 rounded-lg p-1">
                            <button type="button" @click="commissionType = 'percentage'; calculateCommission()" 
                                    :class="commissionType === 'percentage' ? 'bg-white shadow-sm' : ''" 
                                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Percentage</button>
                            <button type="button" @click="commissionType = 'amount'; calculateCommission()" 
                                    :class="commissionType === 'amount' ? 'bg-white shadow-sm' : ''" 
                                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Amount</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Commission <span x-text="commissionType === 'percentage' ? '(%)' : '(₹)'"></span>
                        </label>
                        <input type="number" x-model="commissionValue" @input="calculateCommission()" 
                               :step="commissionType === 'percentage' ? '0.01' : '1'"
                               :min="commissionType === 'percentage' ? '0' : '0'"
                               :max="commissionType === 'percentage' ? '100' : ''"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
                
                <!-- Commission Calculation Display -->
                <div x-show="commissionAmount > 0" class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Commission Amount</span>
                        <span class="text-lg font-bold text-orange-600" x-text="'₹' + commissionAmount.toLocaleString()"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Net Amount (After Commission)</span>
                        <span class="text-lg font-bold text-green-600" x-text="'₹' + netAmount.toLocaleString()"></span>
                    </div>
                </div>
                
                <!-- Hidden inputs for form submission -->
                <input type="hidden" name="commission_percentage" x-model="commissionPercentage">
                <input type="hidden" name="commission_amount" x-model="commissionAmount">
            </div>
        </div>

        <!-- 4. Customer Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button type="button" @click="customerType = 'new'" :class="customerType === 'new' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">New</button>
                    <button type="button" @click="customerType = 'existing'" :class="customerType === 'existing' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Existing</button>
                    <button type="button" @click="customerType = 'b2b'" :class="customerType === 'b2b' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">B2B</button>
                </div>
            </div>
            
            <!-- B2B Customer Toggle -->
            <div x-show="isB2B && customerType === 'b2b'" class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">B2B Customer</h4>
                        <p class="text-xs text-blue-600">Using dummy customer for B2B partner to block dates</p>
                    </div>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="useB2BDummyCustomer" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-blue-700">Use B2B Dummy Customer</span>
                    </label>
                </div>
            </div>
            
            <div x-show="customerType === 'existing'" class="mb-4">
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
        
        <!-- 5. Property & Accommodation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Property & Accommodation</h3>
            
            <!-- Show only if multiple properties or accommodations -->
            <div x-show="showPropertyAccommodationSelection" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                    <select name="property_id" x-model="selectedProperty" @change="loadAccommodations()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent select2-dropdown" 
                            :required="showPropertyAccommodationSelection">
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
                    <select name="accommodation_id" x-model="selectedAccommodation" @change="updateAccommodationPrice()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent select2-dropdown" 
                            :required="showPropertyAccommodationSelection">
                        <option value="">Select Accommodation</option>
                    </select>
                    @error('accommodation_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
            </div>
        </div>

            <!-- Show selected property/accommodation info -->
            <div x-show="selectedAccommodationInfo" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                <div>
                        <h4 class="font-medium text-gray-900" x-text="selectedAccommodationInfo?.display_name"></h4>
                        <p class="text-sm text-gray-600" x-text="selectedAccommodationInfo?.property_name"></p>
                </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900" x-text="'₹' + (selectedAccommodationInfo?.base_price || 0).toLocaleString()"></div>
                        <div class="text-sm text-gray-500">per night</div>
                </div>
            </div>
        </div>

            <!-- Hidden inputs for single property/accommodation -->
            <template x-if="!showPropertyAccommodationSelection">
                <div>
                    <input type="hidden" name="property_id" x-model="defaultPropertyId">
                    <input type="hidden" name="accommodation_id" x-model="defaultAccommodationId">
                </div>
            </template>
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
        // Form data
        selectedProperty: '{{ old('property_id') }}',
        selectedAccommodation: '{{ old('accommodation_id') }}',
        customerType: 'new',
        isB2B: false,
        useB2BDummyCustomer: false,
        guestSearch: '',
        guestName: '{{ old('guest_name') }}',
        guestMobile: '{{ old('guest_mobile') }}',
        guestEmail: '{{ old('guest_email') }}',
        selectedPartner: '{{ old('b2b_partner_id') }}',
        
        // Date and guest data
        checkInDate: '{{ old('check_in_date') }}',
        checkOutDate: '{{ old('check_out_date') }}',
        adults: {{ old('adults', 1) }},
        children: {{ old('children', 0) }},
        days: 1,
        nights: 0,
        totalGuests: 1,
        bookingType: '{{ old('booking_type', 'per_day') }}',
        perPersonPrice: 1000,
        
        // Amount data
        totalAmount: {{ old('total_amount', 0) }},
        advancePaid: {{ old('advance_paid', 0) }},
        defaultAmount: 0,
        balancePending: 0,
        selectedAccommodationPrice: 0,
        selectedAccommodationInfo: null,
        
        // B2B Commission data
        commissionType: 'percentage',
        commissionValue: 0,
        commissionAmount: 0,
        commissionPercentage: 0,
        netAmount: 0,
        
        // Property logic
        showPropertyAccommodationSelection: true,
        defaultPropertyId: null,
        defaultAccommodationId: null,
        
        // Data arrays
        guests: [],
        filteredGuests: [],
        partners: [],
        accommodations: [],
        
        async init() {
            await this.loadGuests();
            await this.loadPartners();
            await this.checkPropertyAccommodationLogic();
            this.calculateDaysNights();
            this.calculateAmount();
            this.calculateCommission();
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
                this.accommodations = await response.json();
                
                const select = document.querySelector('select[name="accommodation_id"]');
                select.innerHTML = '<option value="">Select Accommodation</option>';
                
                this.accommodations.forEach(acc => {
                    const option = document.createElement('option');
                    option.value = acc.id;
                    option.textContent = `${acc.display_name} - ₹${acc.base_price}`;
                    select.appendChild(option);
                });
                
                this.selectedAccommodation = '';
            } catch (error) {
                console.error('Error loading accommodations:', error);
            }
        },
        
        // Date and calculation methods
        updateCheckOutDate() {
            if (this.checkInDate) {
                const checkIn = new Date(this.checkInDate);
                const checkOut = new Date(checkIn);
                checkOut.setDate(checkOut.getDate() + 1);
                this.checkOutDate = checkOut.toISOString().split('T')[0];
                this.calculateDaysNights();
            }
        },
        
        calculateDaysNights() {
            if (this.checkInDate && this.checkOutDate) {
                const checkIn = new Date(this.checkInDate);
                const checkOut = new Date(this.checkOutDate);
                const diffTime = Math.abs(checkOut - checkIn);
                this.days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                this.nights = this.days > 0 ? this.days - 1 : 0;
                
                // Ensure minimum 1 day
                if (this.days === 0) {
                    this.days = 1;
                }
                
                this.calculateTotalGuests();
                this.calculateAmount();
            } else {
                // Default to 1 day when no dates selected
                this.days = 1;
                this.nights = 0;
            }
        },
        
        calculateTotalGuests() {
            this.totalGuests = parseInt(this.adults) + parseInt(this.children);
        },
        
        calculateAmount() {
            if (this.bookingType === 'per_person') {
                this.defaultAmount = this.perPersonPrice * this.totalGuests * this.days;
            } else {
                this.defaultAmount = this.selectedAccommodationPrice * this.days;
            }
            
            // Reset total amount when booking type changes to show new default
            this.totalAmount = this.defaultAmount;
            this.balancePending = this.totalAmount - this.advancePaid;
            this.calculateCommission();
        },
        
        calculateCommission() {
            if (this.isB2B && this.commissionValue > 0) {
                if (this.commissionType === 'percentage') {
                    this.commissionAmount = (this.totalAmount * this.commissionValue) / 100;
                    this.commissionPercentage = this.commissionValue;
                } else {
                    this.commissionAmount = this.commissionValue;
                    this.commissionPercentage = (this.commissionValue / this.totalAmount) * 100;
                }
                this.netAmount = this.totalAmount - this.commissionAmount;
            } else {
                this.commissionAmount = 0;
                this.commissionPercentage = 0;
                this.netAmount = this.totalAmount;
            }
        },
        
        updateAccommodationPrice() {
            const accommodation = this.accommodations.find(acc => acc.id == this.selectedAccommodation);
            if (accommodation) {
                this.selectedAccommodationPrice = accommodation.base_price;
                this.selectedAccommodationInfo = accommodation;
                this.calculateAmount();
            }
        },
        
        async checkPropertyAccommodationLogic() {
            try {
                const response = await fetch('/api/properties/accommodation-count');
                const data = await response.json();
                
                // If only one property with one accommodation, hide selection
                if (data.totalProperties === 1 && data.totalAccommodations === 1) {
                    this.showPropertyAccommodationSelection = false;
                    this.defaultPropertyId = data.defaultPropertyId;
                    this.defaultAccommodationId = data.defaultAccommodationId;
                    this.selectedAccommodationPrice = data.defaultPrice;
                    this.selectedAccommodationInfo = data.defaultAccommodation;
                } else {
                    this.showPropertyAccommodationSelection = true;
                }
            } catch (error) {
                console.error('Error checking property logic:', error);
                this.showPropertyAccommodationSelection = true;
            }
        }
    }
}
</script>
@endpush