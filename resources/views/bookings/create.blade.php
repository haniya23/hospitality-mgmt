@extends('layouts.app')

@section('title', 'Create Booking')

@section('header')
    @include('partials.bookings.create-header')
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('bookings.store') }}" x-data="bookingCreateForm()" x-init="init()" class="space-y-4 sm:space-y-6">
        @csrf
        
        <!-- Property & Accommodation Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
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
                <button @click="openPropertySelectionModal()" class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>
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
                        <h4 class="text-lg font-semibold text-blue-900 mb-1" x-text="selectedAccommodationInfo?.property_name || 'Property'"></h4>
                        <p class="text-sm text-blue-700 mb-3" x-text="selectedAccommodationInfo?.display_name || 'Accommodation'"></p>
                        
                        <!-- Property Details Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="text-blue-800">
                                    <span class="font-medium">Price:</span>
                                    <span x-text="'₹' + (selectedAccommodationPrice || 0).toLocaleString() + '/day'"></span>
                                </span>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-blue-800">
                                    <span class="font-medium">Max Guests:</span>
                                    <span x-text="selectedAccommodationInfo?.max_occupancy || 0"></span>
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
            
            <!-- Property & Accommodation Info (when selected via URL) -->
            <div x-show="selectedPropertyInfo && selectedAccommodationInfo" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200 p-4 sm:p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-green-800" x-text="selectedPropertyInfo?.name || 'Property'"></h4>
                        <p class="text-sm text-green-600" x-text="selectedAccommodationInfo?.display_name || 'Accommodation'"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="text-green-800">
                            <span class="font-medium">Price:</span>
                            <span x-text="'₹' + selectedAccommodationPrice"></span>
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-green-800">
                            <span class="font-medium">Max Occupancy:</span>
                            <span x-text="selectedAccommodationInfo?.max_occupancy || 'N/A'"></span>
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span class="text-green-800">
                            <span class="font-medium">Type:</span>
                            <span x-text="selectedAccommodationInfo?.predefined_type?.name || 'Custom'"></span>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Property Selection (when multiple properties) -->
            <div x-show="showPropertyAccommodationSelection && showPropertySelection" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                        <select name="property_id" x-model="selectedProperty" @change="loadAccommodations()" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-dropdown" 
                                :required="showPropertyAccommodationSelection && showPropertySelection">
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-dropdown" 
                                :required="showPropertyAccommodationSelection">
                            <option value="">Select Accommodation</option>
                        </select>
                        @error('accommodation_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Accommodation Selection Only (when single property, multiple accommodations) -->
            <div x-show="showPropertyAccommodationSelection && !showPropertySelection" class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Accommodation</label>
                        <select name="accommodation_id" x-model="selectedAccommodation" @change="updateAccommodationPrice()" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-dropdown" 
                                :required="!showPropertySelectionModal">
                            <option value="">Select Accommodation</option>
                            <template x-for="accommodation in singlePropertyAccommodations" :key="accommodation.id">
                                <option :value="accommodation.id" x-text="accommodation.display_name"></option>
                            </template>
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
            
            <!-- Hidden input for single property with multiple accommodations -->
            <template x-if="showPropertyAccommodationSelection && !showPropertySelection">
                <div>
                    <input type="hidden" name="property_id" x-model="defaultPropertyId">
                </div>
            </template>
        </div>
        
        <!-- 1. Check-in Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Check-in Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                    <input type="date" name="check_in_date" x-model="checkInDate" @change="updateCheckOutDate(); checkPastBooking()" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
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

        <!-- 3. Commission Section -->
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
                    <div class="relative">
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
                    <input type="text" name="guest_name" x-model="guestName" value="{{ old('guest_name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           :required="!(customerType === 'b2b' && useB2BReservedCustomer)">
                    @error('guest_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                    <input type="text" name="guest_mobile" x-model="guestMobile" value="{{ old('guest_mobile') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           :required="!(customerType === 'b2b' && useB2BReservedCustomer)">
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
        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('bookings.index') }}" class="w-full sm:w-auto px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-center">
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Create Booking
            </button>
        </div>
        <!-- Property Selection Modal -->
        <div x-show="showPropertySelectionModal" x-transition class="fixed inset-0 z-50 overflow-y-auto backdrop-blur-sm bg-black/40">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Select Property & Accommodation</h3>
                    <p class="text-sm text-gray-600">Choose a property and accommodation for your booking</p>
                </div>
                <button @click="closePropertySelectionModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div x-show="!selectedModalProperty" class="space-y-4">
                    <h4 class="text-md font-semibold text-gray-900">Select Property</h4>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($properties as $property)
                            <button @click="selectModalProperty({{ $property->id }}, '{{ $property->uuid }}', '{{ $property->name }}')" 
                                    class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-left">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-medium text-gray-900">{{ $property->name }}</h5>
                                        <p class="text-sm text-gray-600">{{ $property->propertyAccommodations->count() }} accommodations</p>
                                    </div>
                                    <div class="text-right">
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
                
                <div x-show="selectedModalProperty" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-md font-semibold text-gray-900">Select Accommodation</h4>
                        <button @click="selectedModalProperty = null; modalAccommodations = []" class="text-sm text-blue-600 hover:text-blue-800">
                            Back to Properties
                        </button>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h5 class="font-medium text-gray-900" x-text="selectedModalProperty?.name"></h5>
                        <p class="text-sm text-gray-600" x-text="modalAccommodations.length + ' accommodations available'"></p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <template x-for="accommodation in modalAccommodations" :key="accommodation.id">
                            <button @click="selectModalAccommodation(accommodation)" 
                                    class="p-4 border border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors text-left">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-medium text-gray-900" x-text="accommodation.display_name"></h5>
                                        <p class="text-sm text-gray-600" x-text="accommodation.predefined_type?.name || 'Custom'"></p>
                                        <p class="text-sm text-gray-500" x-text="'Max occupancy: ' + accommodation.max_occupancy"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-green-600" x-text="'₹' + accommodation.base_price"></p>
                                        <p class="text-xs text-gray-500">per day</p>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function bookingCreateForm() {
    return {
        // Form data
        selectedProperty: '{{ old('property_id', request('property_uuid') ? \App\Models\Property::where('uuid', request('property_uuid'))->first()?->id : '') }}',
        selectedAccommodation: '',
        selectedPartner: '{{ old('b2b_partner_id', request('b2b_partner_uuid') ? \App\Models\B2bPartner::where('uuid', request('b2b_partner_uuid'))->first()?->uuid : '') }}',
        
        customerType: '{{ request('b2b_partner_uuid') ? 'b2b' : 'new' }}',
        isB2B: {{ request('b2b_partner_uuid') ? 'true' : 'false' }},
        useB2BReservedCustomer: {{ request('b2b_partner_uuid') ? 'true' : 'false' }},
        guestSearch: '',
        guestName: '{{ old('guest_name') }}',
        guestMobile: '{{ old('guest_mobile') }}',
        guestEmail: '{{ old('guest_email') }}',
        
        // Date and guest data
        checkInDate: '{{ old('check_in_date') }}',
        checkOutDate: '{{ old('check_out_date') }}',
        adults: {{ old('adults', 1) }},
        children: {{ old('children', 0) }},
        days: 1,
        nights: 0,
        isPastBooking: false,
        totalGuests: 1,
        bookingType: '{{ old('booking_type', 'per_day') }}',
        perPersonPrice: 1000,
        
        // Amount data
        totalAmount: 0,
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
        showPropertySelection: true,
        singlePropertyAccommodations: [],
        defaultPropertyId: null,
        defaultAccommodationId: null,
        
        // Property selection modal
        showPropertySelectionModal: false,
        selectedModalProperty: null,
        modalAccommodations: [],
        
        // Property info for URL parameters
        selectedPropertyInfo: null,
        customPrice: {{ request('custom_price') ?: 'null' }},
        
        // Commission parameters
        commissionType: '{{ request('commission_type', 'percentage') }}',
        commissionValue: {{ request('commission_value', 10) }},
        
        // Data arrays
        guests: [],
        filteredGuests: [],
        partners: [],
        filteredPartners: [],
        accommodations: [],
        selectedPartnerReservedCustomer: null,
        partnerSearch: '',
        
        async init() {
            await this.loadGuests();
            await this.loadPartners();
            await this.checkPropertyAccommodationLogic();
            this.calculateDaysNights();
            // Calculate amount after accommodation price is set
            this.calculateAmount();
            
            // If property and accommodation are provided via URL, load accommodations
            if (this.selectedProperty && '{{ request('accommodation_uuid') }}') {
                await this.loadAccommodations();
                await this.findAccommodationByUuid();
                await this.loadPropertyInfo();
                
                // Apply custom price if provided
                if (this.customPrice) {
                    this.selectedAccommodationPrice = this.customPrice;
                    this.calculateAmount();
                }
            }
            
            // If B2B partner is provided via URL, auto-select it
            if (this.selectedPartner && this.partners.length > 0) {
                const partner = this.partners.find(p => p.uuid === this.selectedPartner);
                if (partner) {
                    this.selectPartner(partner);
                }
            }
            this.calculateCommission();
            this.checkPastBooking();
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

        async loadPartnerReservedCustomer(partnerId) {
            if (!partnerId) {
                this.selectedPartnerReservedCustomer = null;
                return;
            }
            
            try {
                const response = await fetch(`/api/partners/${partnerId}/reserved-customer`);
                const data = await response.json();
                this.selectedPartnerReservedCustomer = data.name;
            } catch (error) {
                console.error('Error loading partner reserved customer:', error);
                this.selectedPartnerReservedCustomer = null;
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

        searchPartners() {
            if (this.partnerSearch.length < 2) {
                this.filteredPartners = [];
                return;
            }
            
            this.filteredPartners = this.partners.filter(partner => {
                const name = partner.partner_name || '';
                const search = this.partnerSearch.toLowerCase();
                return name.toLowerCase().includes(search);
            }).slice(0, 5);
        },

        selectPartner(partner) {
            this.selectedPartner = partner.uuid;
            this.partnerSearch = partner.partner_name;
            this.filteredPartners = [];
            
            // Set default commission rate from partner
            this.commissionValue = partner.commission_rate || 10;
            this.commissionType = 'percentage';
            this.calculateCommission();
        },

        updateBalance() {
            this.balancePending = this.totalAmount - this.advancePaid;
            this.calculateCommission();
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
        
        increaseDays() {
            // Check if adding one more day would exceed 30 days from check-in
            if (this.checkInDate) {
                const checkIn = new Date(this.checkInDate);
                const maxCheckOut = new Date(checkIn);
                maxCheckOut.setDate(maxCheckOut.getDate() + 30);
                
                const proposedCheckOut = new Date(checkIn);
                proposedCheckOut.setDate(proposedCheckOut.getDate() + this.days + 1);
                
                if (proposedCheckOut <= maxCheckOut) {
                    this.days = this.days + 1;
                    this.nights = this.days - 1;
                    this.updateCheckOutDate();
                    this.calculateTotalGuests();
                    this.calculateAmount();
                }
            } else {
                // If no check-in date, allow up to 30 days
                if (this.days < 30) {
                    this.days = this.days + 1;
                    this.nights = this.days - 1;
                    this.calculateTotalGuests();
                    this.calculateAmount();
                }
            }
        },
        
        decreaseDays() {
            if (this.days > 1) {
                this.days = this.days - 1;
                this.nights = this.days - 1;
                this.updateCheckOutDate();
                this.calculateTotalGuests();
                this.calculateAmount();
            }
        },
        
        updateCheckOutDate() {
            if (this.checkInDate) {
                const checkIn = new Date(this.checkInDate);
                const checkOut = new Date(checkIn);
                checkOut.setDate(checkOut.getDate() + this.days);
                this.checkOutDate = checkOut.toISOString().split('T')[0];
            }
        },
        
        canIncreaseDays() {
            if (this.checkInDate) {
                const checkIn = new Date(this.checkInDate);
                const maxCheckOut = new Date(checkIn);
                maxCheckOut.setDate(maxCheckOut.getDate() + 30);
                
                const proposedCheckOut = new Date(checkIn);
                proposedCheckOut.setDate(proposedCheckOut.getDate() + this.days + 1);
                
                return proposedCheckOut <= maxCheckOut;
            } else {
                // If no check-in date, allow up to 30 days
                return this.days < 30;
            }
        },
        
        checkPastBooking() {
            if (this.checkInDate) {
                const checkIn = new Date(this.checkInDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Reset time to start of day
                this.isPastBooking = checkIn < today;
            } else {
                this.isPastBooking = false;
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
            
            // Always update total amount to match calculated amount
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
                
                // If only one property with one accommodation, hide selection completely
                if (data.totalProperties === 1 && data.totalAccommodations === 1) {
                    this.showPropertyAccommodationSelection = false;
                    this.showPropertySelection = false;
                    this.defaultPropertyId = data.defaultPropertyId;
                    this.defaultAccommodationId = data.defaultAccommodationId;
                    this.selectedAccommodationPrice = data.defaultPrice;
                    this.selectedAccommodationInfo = data.defaultAccommodation;
                    // Recalculate amount after setting accommodation price
                    this.calculateAmount();
                } 
                // If only one property with multiple accommodations, show only accommodation selection
                else if (data.totalProperties === 1 && data.totalAccommodations > 1) {
                    this.showPropertyAccommodationSelection = true;
                    this.showPropertySelection = false;
                    this.defaultPropertyId = data.defaultPropertyId;
                    this.singlePropertyAccommodations = data.accommodations;
                } 
                // If multiple properties, show both property and accommodation selection
                else {
                    this.showPropertyAccommodationSelection = true;
                    this.showPropertySelection = true;
                }
            } catch (error) {
                console.error('Error checking property logic:', error);
                this.showPropertyAccommodationSelection = true;
                this.showPropertySelection = true;
            }
        },

        // Watch for partner selection changes
        watch: {
            selectedPartner(newValue) {
                if (this.customerType === 'b2b' && this.useB2BReservedCustomer) {
                    this.loadPartnerReservedCustomer(newValue);
                }
            },
            useB2BReservedCustomer(newValue) {
                if (newValue && this.selectedPartner) {
                    this.loadPartnerReservedCustomer(this.selectedPartner);
                } else {
                    this.selectedPartnerReservedCustomer = null;
                }
            }
        },
        
        // Property selection modal methods
        openPropertySelectionModal() {
            this.showPropertySelectionModal = true;
            this.selectedModalProperty = null;
            this.modalAccommodations = [];
        },
        
        closePropertySelectionModal() {
            this.showPropertySelectionModal = false;
            this.selectedModalProperty = null;
            this.modalAccommodations = [];
        },
        
        async selectModalProperty(propertyId, propertyUuid, propertyName) {
            this.selectedModalProperty = {
                id: propertyId,
                uuid: propertyUuid,
                name: propertyName
            };
            
            try {
                const response = await fetch(`/api/properties/${propertyId}/accommodations`);
                const accommodations = await response.json();
                this.modalAccommodations = accommodations;
            } catch (error) {
                console.error('Error loading accommodations:', error);
                this.modalAccommodations = [];
            }
        },
        
        async selectModalAccommodation(accommodation) {
            // Set the selected property and accommodation
            this.selectedProperty = this.selectedModalProperty.id;
            this.selectedAccommodation = accommodation.id;
            
            // Set accommodation info
            this.selectedAccommodationInfo = accommodation;
            this.selectedAccommodationPrice = accommodation.base_price;
            
            // Load accommodations for the form
            await this.loadAccommodations();
            this.updateAccommodationPrice();
            
            // Update property info
            await this.loadPropertyInfo();
            
            // Update URL with selected property and accommodation
            const propertyUuid = this.selectedModalProperty.uuid;
            const accommodationUuid = accommodation.uuid;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('property_uuid', propertyUuid);
            currentUrl.searchParams.set('accommodation_uuid', accommodationUuid);
            window.history.replaceState({}, '', currentUrl);
            
            // Close the modal
            this.closePropertySelectionModal();
        },
        
        async loadPropertyInfo() {
            if (this.selectedProperty) {
                try {
                    const response = await fetch(`/api/properties/${this.selectedProperty}`);
                    const property = await response.json();
                    this.selectedPropertyInfo = property;
                } catch (error) {
                    console.error('Error loading property info:', error);
                }
            }
        },
        
        async findAccommodationByUuid() {
            const accommodationUuid = '{{ request('accommodation_uuid') }}';
            if (accommodationUuid && this.accommodations.length > 0) {
                const accommodation = this.accommodations.find(acc => acc.uuid === accommodationUuid);
                if (accommodation) {
                    this.selectedAccommodation = accommodation.id;
                    this.selectedAccommodationPrice = accommodation.base_price;
                    this.selectedAccommodationInfo = accommodation;
                    this.calculateAmount();
                }
            }
        }
    }
}
</script>
@endpush