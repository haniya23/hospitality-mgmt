@extends('layouts.app')

@section('title', 'Update Booking')

@section('header')
    @include('partials.bookings.edit-header')
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('bookings.update', $booking->uuid) }}" x-data="bookingEditForm()" x-init="init()" class="space-y-4 sm:space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Property & Accommodation Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Property & Accommodation</h3>
                    <p class="text-sm text-gray-600">Booking details for your stay</p>
                </div>
            </div>
            
            <!-- Property Details Card -->
            <div x-show="!showPropertyAccommodationSelection" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:space-x-4 space-y-3 sm:space-y-0">
                    <!-- Property Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Property Info -->
                    <div class="flex-1 min-w-0">
                        <h4 class="text-lg font-semibold text-blue-900 mb-1" x-text="selectedAccommodationInfo?.property_name || '{{ $booking->accommodation->property->name }}'"></h4>
                        <p class="text-sm text-blue-700 mb-3" x-text="selectedAccommodationInfo?.custom_name || '{{ $booking->accommodation->custom_name }}'"></p>
                        
                        <!-- Property Details Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="text-blue-800">
                                    <span class="font-medium">Price:</span>
                                    <span x-text="'₹' + (selectedAccommodationPrice || {{ $booking->accommodation->base_price }}).toLocaleString() + '/day'"></span>
                                </span>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-blue-800">
                                    <span class="font-medium">Max Guests:</span>
                                    <span x-text="selectedAccommodationInfo?.max_occupancy || {{ $booking->accommodation->max_occupancy }}"></span>
                                </span>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="text-blue-800">
                                    <span class="font-medium">Type:</span>
                                    <span x-text="selectedAccommodationInfo?.predefined_type?.name || 'Custom'"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Property Selection (when multiple properties) -->
            <div x-show="showPropertyAccommodationSelection" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                        <select name="property_id" x-model="selectedProperty" @change="loadAccommodations()" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-dropdown" 
                                :required="showPropertyAccommodationSelection">
                            <option value="">Select Property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" {{ old('property_id', $booking->accommodation->property_id) == $property->id ? 'selected' : '' }}>
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-dropdown" 
                                :required="showPropertyAccommodationSelection">
                            <option value="">Select Accommodation</option>
                            @foreach($accommodations as $accommodation)
                                <option value="{{ $accommodation->id }}" {{ old('accommodation_id', $booking->property_accommodation_id) == $accommodation->id ? 'selected' : '' }}>
                                    {{ $accommodation->custom_name }} - ₹{{ $accommodation->base_price }}
                                </option>
                            @endforeach
                        </select>
                        @error('accommodation_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
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

        <!-- 1. Check-in Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Check-in Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                    <input type="text" name="check_in_date" x-model="checkInDate" @change="updateCheckOutDate(); checkPastBooking()" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent datepicker-input" 
                           placeholder="Select check-in date" readonly required>
                    @error('check_in_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <!-- Past booking warning -->
                    <div x-show="isPastBooking" class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="text-sm text-yellow-800">You're recording a past booking</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                    <input type="text" name="check_out_date" x-model="checkOutDate" @change="calculateDaysNights()" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent datepicker-input" 
                           placeholder="Select check-out date" readonly required>
                    @error('check_out_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                    <input type="number" name="adults" x-model="adults" @change="calculateTotalGuests(); calculateAmount()" min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    @error('adults')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Children</label>
                    <input type="number" name="children" x-model="children" @change="calculateTotalGuests(); calculateAmount()" min="0" 
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
                            <div class="flex items-center justify-center space-x-2">
                                <button type="button" @click="decreaseDays()" 
                                        x-show="days > 1"
                                        class="w-8 h-8 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <div class="text-2xl font-bold text-blue-600 min-w-[2rem] text-center" x-text="days"></div>
                                <button type="button" @click="increaseDays()" 
                                        x-show="canIncreaseDays()"
                                        class="w-8 h-8 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>
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
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('special_requests', $booking->special_requests) }}</textarea>
                @error('special_requests')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 2. Booking Status Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Status</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                    <select name="status" x-model="bookingStatus" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent select2-dropdown" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="checked_in">Checked In</option>
                        <option value="checked_out">Checked Out</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmation Number</label>
                    <input type="text" name="confirmation_number" x-model="confirmationNumber" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           placeholder="Auto-generated or custom">
                    @error('confirmation_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 3. Amount Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Amount Calculation</h3>
            
            <!-- Default Amount Display -->
            <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700" x-text="bookingType === 'per_day' ? 'Default Amount (Base Price × Days)' : 'Default Amount (Per Person Price × Guests × Days)'"></span>
                    <span class="text-lg font-bold text-gray-900" x-text="'₹' + defaultAmount.toLocaleString()"></span>
                </div>
                <div class="text-xs text-gray-500">
                    <span x-show="bookingType === 'per_day'" x-text="'₹' + (selectedAccommodationPrice || {{ $booking->accommodation->base_price }}).toLocaleString() + ' × ' + days + ' days'"></span>
                    <span x-show="bookingType === 'per_person'" x-text="'₹' + perPersonPrice.toLocaleString() + ' × ' + totalGuests + ' guests × ' + days + ' days'"></span>
                </div>
            </div>
            
            <!-- Override Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount (Override)</label>
                    <input type="number" name="total_amount" x-model="totalAmount" @input="updateBalance()" step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                    <p class="text-xs text-gray-500 mt-1">Shows calculated amount, can be overridden</p>
                    @error('total_amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Advance Paid</label>
                    <input type="number" name="advance_paid" x-model="advancePaid" @input="updateBalance()" step="0.01" min="0" 
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

        <!-- 4. Commission Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Commission</h3>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button type="button" @click="isB2B = false" :class="!isB2B ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Direct</button>
                    <button type="button" @click="isB2B = true; calculateCommission()" :class="isB2B ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">B2B</button>
                </div>
            </div>
            
            <div x-show="isB2B" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">B2B Partner</label>
                    
                    <!-- Show selected partner if exists -->
                    <div x-show="selectedPartner && selectedPartnerReservedCustomer" class="mb-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-blue-800" x-text="selectedPartnerReservedCustomer"></div>
                                <div class="text-sm text-blue-600" x-text="'Commission: ' + commissionValue + '%'"></div>
                            </div>
                            <button type="button" @click="selectedPartner = ''; selectedPartnerReservedCustomer = ''; commissionValue = 0; calculateCommission()" 
                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Search for new partner -->
                    <div x-show="!selectedPartner || !selectedPartnerReservedCustomer" class="relative">
                        <input type="text" x-model="partnerSearch" @input="searchPartners()" placeholder="Search partners..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <div x-show="filteredPartners.length > 0" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-40 overflow-y-auto">
                            <template x-for="partner in filteredPartners" :key="partner.id">
                                <div @click="selectPartner(partner)" class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                    <div class="font-medium" x-text="partner.partner_name"></div>
                                    <div class="text-sm text-gray-500" x-text="partner.commission_rate + '% commission'"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <input type="hidden" name="b2b_partner_id" x-model="selectedPartner">
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

        <!-- 5. Customer Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button type="button" @click="customerType = 'new'" :class="customerType === 'new' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">New</button>
                    <button type="button" @click="customerType = 'existing'" :class="customerType === 'existing' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">Existing</button>
                    <button type="button" @click="customerType = 'b2b'" :class="customerType === 'b2b' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">B2B</button>
                </div>
            </div>
            
            <!-- B2B Reserved Customer Toggle -->
            <div x-show="isB2B && customerType === 'b2b'" class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">B2B Reserved Customer</h4>
                        <p class="text-xs text-blue-600">Using reserved customer for B2B partner to block dates</p>
                    </div>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="useB2BReservedCustomer" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-blue-700">Use B2B Reserved Customer</span>
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
            
            <!-- Customer Input Fields - Hidden when B2B Reserved Customer is selected -->
            <div x-show="!(customerType === 'b2b' && useB2BReservedCustomer)" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Name</label>
                    <input type="text" name="guest_name" x-model="guestName" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           :required="!(customerType === 'b2b' && useB2BReservedCustomer)">
                    @error('guest_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                    <input type="text" name="guest_mobile" x-model="guestMobile" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           :required="!(customerType === 'b2b' && useB2BReservedCustomer)">
                    @error('guest_mobile')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                    <input type="email" name="guest_email" x-model="guestEmail" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('guest_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- B2B Reserved Customer Info Display -->
            <div x-show="customerType === 'b2b' && useB2BReservedCustomer" class="p-4 bg-green-50 rounded-lg border border-green-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-green-800">Using B2B Reserved Customer</h4>
                        <p class="text-sm text-green-700" x-text="selectedPartnerReservedCustomer || 'Select a B2B partner to see reserved customer details'"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden input for B2B reserved customer flag -->
        <input type="hidden" name="use_b2b_reserved_customer" :value="useB2BReservedCustomer ? '1' : '0'">

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pb-8 lg:pb-0">
            <a href="{{ route('bookings.index') }}" class="w-full sm:w-auto px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-center">
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Update Booking
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function bookingEditForm() {
    return {
        // Form data
        selectedProperty: '{{ old('property_id', $booking->accommodation->property_id) }}',
        selectedAccommodation: '{{ old('accommodation_id', $booking->property_accommodation_id) }}',
        customerType: 'new',
        isB2B: {{ $booking->b2b_partner_id ? 'true' : 'false' }},
        useB2BReservedCustomer: false,
        guestSearch: '',
        guestName: '{{ old('guest_name', $booking->guest->name) }}',
        guestMobile: '{{ old('guest_mobile', $booking->guest->mobile_number) }}',
        guestEmail: '{{ old('guest_email', $booking->guest->email) }}',
        
        // Status fields
        bookingStatus: '{{ old('status', $booking->status) }}',
        confirmationNumber: '{{ old('confirmation_number', $booking->confirmation_number) }}',
        selectedPartner: '{{ old('b2b_partner_id', $booking->b2b_partner_id) }}',
        
        // Date and guest data
        checkInDate: '{{ old('check_in_date', $booking->check_in_date->format('Y-m-d')) }}',
        checkOutDate: '{{ old('check_out_date', $booking->check_out_date->format('Y-m-d')) }}',
        adults: {{ old('adults', $booking->adults) }},
        children: {{ old('children', $booking->children) }},
        days: 1,
        nights: 0,
        isPastBooking: false,
        totalGuests: {{ old('adults', $booking->adults) + old('children', $booking->children) }},
        bookingType: '{{ old('booking_type', $booking->booking_type) }}',
        perPersonPrice: 1000,
        
        // Amount data
        totalAmount: {{ old('total_amount', $booking->total_amount) }},
        advancePaid: {{ old('advance_paid', $booking->advance_paid) }},
        defaultAmount: 0,
        balancePending: {{ old('balance_pending', $booking->balance_pending) }},
        selectedAccommodationPrice: {{ $booking->accommodation->base_price }},
        selectedAccommodationInfo: {
            property_name: '{{ $booking->accommodation->property->name }}',
            custom_name: '{{ $booking->accommodation->custom_name }}',
            max_occupancy: {{ $booking->accommodation->max_occupancy }},
            predefined_type: { name: 'Custom' }
        },
        
        // B2B Commission data
        commissionType: 'percentage',
        commissionValue: {{ $booking->b2bPartner ? $booking->b2bPartner->commission_rate : 0 }},
        commissionAmount: 0,
        commissionPercentage: {{ $booking->b2bPartner ? $booking->b2bPartner->commission_rate : 0 }},
        netAmount: 0,
        partnerSearch: '',
        partners: [],
        filteredPartners: [],
        selectedPartnerReservedCustomer: '{{ $booking->b2bPartner ? $booking->b2bPartner->partner_name : "" }}',
        
        // Guest data
        guests: [],
        filteredGuests: [],
        
        // Property selection
        showPropertyAccommodationSelection: false,
        defaultPropertyId: '{{ $booking->accommodation->property_id }}',
        defaultAccommodationId: '{{ $booking->property_accommodation_id }}',
        
        async init() {
            await this.loadGuests();
            await this.loadPartners();
            this.calculateDaysNights();
            this.calculateTotalGuests();
            this.calculateAmount();
            this.checkPastBooking();
            
            // Initialize B2B data if partner exists
            if (this.isB2B && this.selectedPartner) {
                this.calculateCommission();
            }
        },
        
        async loadGuests() {
            try {
                const response = await fetch('/api/guests');
                this.guests = await response.json();
            } catch (error) {
                // Error loading guests
            }
        },
        
        async loadPartners() {
            try {
                const response = await fetch('/api/partners');
                this.partners = await response.json();
            } catch (error) {
                // Error loading partners
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
        
        searchPartners() {
            if (this.partnerSearch.length < 2) {
                this.filteredPartners = [];
                return;
            }
            
            this.filteredPartners = this.partners.filter(partner => 
                partner.partner_name.toLowerCase().includes(this.partnerSearch.toLowerCase())
            ).slice(0, 5);
        },
        
        async selectPartner(partner) {
            this.selectedPartner = partner.id;
            this.partnerSearch = '';
            this.filteredPartners = [];
            
            // Load reserved customer if available
            try {
                const response = await fetch(`/api/partners/${partner.id}/reserved-customer`);
                const data = await response.json();
                this.selectedPartnerReservedCustomer = data.name;
            } catch (error) {
                // Error loading reserved customer
            }
        },
        
        calculateDaysNights() {
            if (this.checkInDate && this.checkOutDate) {
                const start = new Date(this.checkInDate);
                const end = new Date(this.checkOutDate);
                const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                this.days = diff > 0 ? diff : 1;
                this.nights = this.days - 1;
            }
            this.calculateAmount();
        },
        
        calculateTotalGuests() {
            this.totalGuests = this.adults + this.children;
            this.calculateAmount();
        },
        
        calculateAmount() {
            if (this.bookingType === 'per_day') {
                this.defaultAmount = (this.selectedAccommodationPrice || 0) * this.days;
            } else {
                this.defaultAmount = this.perPersonPrice * this.totalGuests * this.days;
            }
            
            if (this.totalAmount === 0) {
                this.totalAmount = this.defaultAmount;
            }
            
            this.updateBalance();
            this.calculateCommission();
        },
        
        updateBalance() {
            this.balancePending = this.totalAmount - this.advancePaid;
        },
        
        calculateCommission() {
            if (this.commissionType === 'percentage') {
                this.commissionAmount = (this.totalAmount * this.commissionValue) / 100;
                this.commissionPercentage = this.commissionValue;
            } else {
                this.commissionAmount = this.commissionValue;
                this.commissionPercentage = (this.commissionValue / this.totalAmount) * 100;
            }
            
            this.netAmount = this.totalAmount - this.commissionAmount;
        },
        
        updateCheckOutDate() {
            if (this.checkInDate) {
                const checkIn = new Date(this.checkInDate);
                checkIn.setDate(checkIn.getDate() + 1);
                this.checkOutDate = checkIn.toISOString().split('T')[0];
            }
            this.calculateDaysNights();
        },
        
        checkPastBooking() {
            if (this.checkInDate) {
                const checkIn = new Date(this.checkInDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                this.isPastBooking = checkIn < today;
            }
        },
        
        decreaseDays() {
            if (this.days > 1) {
                this.days--;
                this.nights = this.days - 1;
                this.updateCheckOutDate();
            }
        },
        
        increaseDays() {
            this.days++;
            this.nights = this.days - 1;
            this.updateCheckOutDate();
        },
        
        canIncreaseDays() {
            return true; // Allow unlimited days for editing
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
                    option.textContent = `${acc.custom_name} - ₹${acc.base_price}`;
                    select.appendChild(option);
                });
                
                this.selectedAccommodation = '';
            } catch (error) {
                // Error loading accommodations
            }
        },
        
        updateAccommodationPrice() {
            const accommodation = this.accommodations?.find(acc => acc.id == this.selectedAccommodation);
            if (accommodation) {
                this.selectedAccommodationPrice = accommodation.base_price;
                this.selectedAccommodationInfo = accommodation;
                this.calculateAmount();
            }
        }
    }
}

// Initialize datepickers when document is ready
$(document).ready(function() {
    // Initialize check-in date picker
    $('input[name="check_in_date"]').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showAnim: 'slideDown',
        onSelect: function(dateText) {
            // Update Alpine.js model
            const alpineComponent = Alpine.$data(document.querySelector('[x-data*="bookingEditForm"]'));
            if (alpineComponent) {
                alpineComponent.checkInDate = dateText;
                alpineComponent.updateCheckOutDate();
                alpineComponent.checkPastBooking();
            }
        }
    });
    
    // Initialize check-out date picker
    $('input[name="check_out_date"]').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showAnim: 'slideDown',
        onSelect: function(dateText) {
            // Update Alpine.js model
            const alpineComponent = Alpine.$data(document.querySelector('[x-data*="bookingEditForm"]'));
            if (alpineComponent) {
                alpineComponent.checkOutDate = dateText;
                alpineComponent.calculateDaysNights();
            }
        }
    });
    
    // Update check-out date minimum when check-in date changes
    $('input[name="check_in_date"]').on('change', function() {
        const checkInDate = $(this).datepicker('getDate');
        if (checkInDate) {
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            $('input[name="check_out_date"]').datepicker('option', 'minDate', nextDay);
        }
    });
    
    // Set initial values from the booking data
    const checkInValue = '{{ old("check_in_date", $booking->check_in_date) }}';
    const checkOutValue = '{{ old("check_out_date", $booking->check_out_date) }}';
    
    if (checkInValue) {
        $('input[name="check_in_date"]').datepicker('setDate', checkInValue);
    }
    if (checkOutValue) {
        $('input[name="check_out_date"]').datepicker('setDate', checkOutValue);
    }
});
</script>
@endpush