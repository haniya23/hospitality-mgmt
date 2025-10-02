@extends('layouts.app')

@section('title', 'Create Booking')

@section('header')
    @include('partials.bookings.create-header')
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('bookings.store') }}" x-data="bookingCreateForm()" x-init="init()" @submit="handleSubmit" class="space-y-4 sm:space-y-6">
        @csrf
        
        <!-- Property & Accommodation Section -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-6">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Property & Accommodation</h3>
                        <p class="text-sm text-gray-600">Booking details for your stay</p>
                    </div>
                </div>
                
                <button @click="openPropertySelectionModal()" 
                        class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95 group">
                    <svg class="w-6 h-6 text-white transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>
            </div>
            
            <!-- Property Details Card -->
            <div x-show="!showPropertyAccommodationSelection && selectedAccommodationInfo && !selectedPropertyInfo" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-4 sm:p-6">
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
                        <h4 class="text-lg font-semibold text-blue-900 mb-1" x-text="selectedPropertyInfo?.name || selectedAccommodationInfo?.property_name || 'Property'"></h4>
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
            <template x-if="showPropertyAccommodationSelection && showPropertySelection">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                            <select name="property_id" x-model="selectedProperty" @change="loadAccommodations()" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-dropdown" 
                                    required>
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
                                    required>
                                <option value="">Select Accommodation</option>
                            </select>
                            @error('accommodation_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Accommodation Selection Only (when single property, multiple accommodations) -->
            <template x-if="showPropertyAccommodationSelection && !showPropertySelection">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Accommodation</label>
                            <select name="accommodation_id" x-model="selectedAccommodation" @change="updateAccommodationPrice()" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-dropdown" 
                                    required>
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
            </template>
            
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
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                    <input type="text" name="check_in_date" x-model="checkInDate" @change="updateCheckOutDate(); checkPastBooking()" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent datepicker-input" 
                           placeholder="Check-in" readonly required>
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
                           placeholder="Check-out" readonly required>
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
            <div x-show="days > 0" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     class="mt-4 p-3 sm:p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-100 shadow-sm hover:shadow-md transition-shadow duration-300">
    
    <!-- Desktop Layout -->
    <div class="hidden sm:flex items-center justify-between gap-6">
        <!-- Days & Nights Section -->
        <div class="flex items-center gap-6">
            <!-- Days Counter -->
            <div class="relative">
                <div class="flex items-center gap-3 bg-white rounded-lg px-4 py-2 shadow-sm">
                    <button type="button" 
                            @click="decreaseDays()" 
                            x-show="days > 1"
                            class="group w-9 h-9 bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm">
                        <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                        </svg>
                    </button>
                    
                    <div class="text-center min-w-[3rem]">
                        <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent" x-text="days"></div>
                        <div class="text-xs font-medium text-blue-600 uppercase tracking-wider mt-0.5">Days</div>
                    </div>
                    
                    <button type="button" 
                            @click="increaseDays()" 
                            x-show="canIncreaseDays()"
                            class="group w-9 h-9 bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm">
                        <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Nights Display -->
            <div class="flex items-center gap-3 bg-white rounded-lg px-5 py-3 shadow-sm">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-800" x-text="nights"></div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nights</div>
                </div>
            </div>
        </div>
        
        <!-- Total Guests Section -->
        <div class="flex items-center gap-3 bg-white rounded-lg px-5 py-3 shadow-sm">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-0.5">Total Guests</div>
                <div class="text-2xl font-bold text-gray-800" x-text="totalGuests"></div>
            </div>
        </div>
    </div>

    <!-- Mobile Layout -->
    <div class="sm:hidden space-y-3">
        <!-- Days Counter - Mobile -->
        <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2.5 shadow-sm">
            <button type="button" 
                    @click="decreaseDays()" 
                    x-show="days > 1"
                    class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center shadow-sm transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                </svg>
            </button>
            
            <div class="flex-1 text-center">
                <div class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent" x-text="days"></div>
                <div class="text-xs font-medium text-blue-600 uppercase tracking-wider">Days</div>
            </div>
            
            <button type="button" 
                    @click="increaseDays()" 
                    x-show="canIncreaseDays()"
                    class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center shadow-sm transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </button>
        </div>

        <!-- Nights & Guests - Mobile -->
        <div class="grid grid-cols-2 gap-3">
            <!-- Nights -->
            <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2.5 shadow-sm">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider truncate">Nights</div>
                    <div class="text-xl font-bold text-gray-800" x-text="nights"></div>
                </div>
            </div>

            <!-- Total Guests -->
            <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2.5 shadow-sm">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider truncate">Guests</div>
                    <div class="text-xl font-bold text-gray-800" x-text="totalGuests"></div>
                </div>
            </div>
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
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Set Amount</label>
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
<div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-6">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">Commission</h3>
        </div>
        
        @if($hasB2bPartners)
        <div class="flex bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl p-1 shadow-inner w-full sm:w-auto">
            <button type="button" 
                    @click="isB2B = false" 
                    :class="!isB2B ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                    class="flex-1 sm:flex-initial px-4 py-2.5 sm:py-2 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="!isB2B ? 'text-purple-600' : 'text-gray-600'">Direct</span>
            </button>
            <button type="button" 
                    @click="isB2B = true; calculateCommission()" 
                    :class="isB2B ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                    class="flex-1 sm:flex-initial px-4 py-2.5 sm:py-2 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="isB2B ? 'text-purple-600' : 'text-gray-600'">B2B</span>
            </button>
        </div>
        @else
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 p-3 sm:p-0 bg-blue-50 sm:bg-transparent rounded-lg sm:rounded-none border border-blue-100 sm:border-0">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm text-gray-700 font-medium">Direct booking only</span>
            </div>
            <a href="{{ route('b2b.create') }}" 
               class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 font-semibold group">
                <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add B2B Partner
            </a>
        </div>
        @endif
    </div>
    
    <div x-show="isB2B && {{ $hasB2bPartners ? 'true' : 'false' }}" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="space-y-5">
        
        <!-- Partner Search -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                B2B Partner
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       x-model="partnerSearch" 
                       @input="searchPartners()" 
                       placeholder="Search partners..." 
                       class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300">
                
                <div x-show="filteredPartners.length > 0" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute z-10 w-full bg-white border-2 border-purple-200 rounded-xl mt-2 max-h-60 overflow-y-auto shadow-xl">
                    <template x-for="partner in filteredPartners" :key="partner.id">
                        <div @click="selectPartner(partner)" 
                             class="px-4 py-3 hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-all duration-150 group">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 group-hover:text-purple-600 transition-colors" x-text="partner.partner_name"></div>
                                    <div class="flex items-center gap-1 mt-1">
                                        <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600" x-text="partner.commission_rate + '% commission'"></span>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <input type="hidden" name="b2b_partner_id" x-model="selectedPartner">
        </div>
        
        <!-- Commission Type & Value -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Commission Type
                </label>
                <div class="flex bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl p-1 shadow-inner">
                    <button type="button" 
                            @click="commissionType = 'percentage'; calculateCommission()" 
                            :class="commissionType === 'percentage' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                            class="flex-1 px-3 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                        <span :class="commissionType === 'percentage' ? 'text-purple-600' : 'text-gray-600'">Percentage</span>
                    </button>
                    <button type="button" 
                            @click="commissionType = 'amount'; calculateCommission()" 
                            :class="commissionType === 'amount' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                            class="flex-1 px-3 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                        <span :class="commissionType === 'amount' ? 'text-purple-600' : 'text-gray-600'">Amount</span>
                    </button>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    Commission <span class="text-purple-600 font-bold" x-text="commissionType === 'percentage' ? '(%)' : '(₹)'"></span>
                </label>
                <div class="relative">
                    <input type="number" 
                           x-model="commissionValue" 
                           @input="calculateCommission()" 
                           :step="commissionType === 'percentage' ? '0.01' : '1'"
                           :min="commissionType === 'percentage' ? '0' : '0'"
                           :max="commissionType === 'percentage' ? '100' : ''"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                           placeholder="Enter value">
                </div>
            </div>
        </div>
        
        <!-- Commission Calculation Display -->
        <div x-show="commissionAmount > 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="p-4 sm:p-5 bg-gradient-to-br from-orange-50 to-red-50 rounded-xl border-2 border-orange-200 shadow-sm">
            
            <!-- Mobile Layout -->
            <div class="sm:hidden space-y-3">
                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Commission</span>
                    </div>
                    <span class="text-lg font-bold text-orange-600" x-text="'₹' + commissionAmount.toLocaleString()"></span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Net Amount</span>
                    </div>
                    <span class="text-lg font-bold text-green-600" x-text="'₹' + netAmount.toLocaleString()"></span>
                </div>
            </div>
            
            <!-- Desktop Layout -->
            <div class="hidden sm:block space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-orange-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Commission Amount</span>
                    </div>
                    <span class="text-2xl font-bold text-orange-600" x-text="'₹' + commissionAmount.toLocaleString()"></span>
                </div>
                
                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Net Amount (After Commission)</span>
                    </div>
                    <span class="text-2xl font-bold text-green-600" x-text="'₹' + netAmount.toLocaleString()"></span>
                </div>
            </div>
        </div>
        
        <!-- Hidden inputs for form submission -->
        <input type="hidden" name="commission_percentage" x-model="commissionPercentage">
        <input type="hidden" name="commission_amount" x-model="commissionAmount">
    </div>
</div>

        <!-- 4. Customer Information -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-6">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Customer Information</h3>
                </div>
                
                <div class="flex bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl p-1 shadow-inner w-full sm:w-auto">
                    <button type="button" 
                            @click="customerType = 'new'" 
                            :class="customerType === 'new' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                            class="flex-1 sm:flex-initial px-4 py-2.5 sm:py-2 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                        <span :class="customerType === 'new' ? 'text-blue-600' : 'text-gray-600'">New</span>
                    </button>
                    <button type="button" 
                            @click="customerType = 'existing'" 
                            :class="customerType === 'existing' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                            class="flex-1 sm:flex-initial px-4 py-2.5 sm:py-2 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                        <span :class="customerType === 'existing' ? 'text-blue-600' : 'text-gray-600'">Existing</span>
                    </button>
                    <button type="button" 
                            @click="customerType = 'b2b'" 
                            :class="customerType === 'b2b' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                            class="flex-1 sm:flex-initial px-4 py-2.5 sm:py-2 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                        <span :class="customerType === 'b2b' ? 'text-blue-600' : 'text-gray-600'">B2B</span>
                    </button>
                </div>
            </div>
            
            <!-- B2B Reserved Customer Toggle -->
            <div x-show="isB2B && customerType === 'b2b' && {{ $hasB2bPartners ? 'true' : 'false' }}" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mb-5 p-4 sm:p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-blue-800">B2B Reserved Customer</h4>
                        </div>
                        <p class="text-xs text-blue-600 leading-relaxed" x-text="useB2BReservedCustomer ? 'Automatically selected reserved customer for this partner' : 'Using reserved customer for B2B partner to block dates'"></p>
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" 
                               x-model="useB2BReservedCustomer" 
                               class="h-5 w-5 text-blue-600 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        <span class="text-sm font-semibold text-blue-700 group-hover:text-blue-800 transition-colors">Use B2B Reserved Customer</span>
                    </label>
                </div>
            </div>
            
            <div x-show="customerType === 'existing'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search Customer
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="guestSearch" 
                           @input="searchGuests()" 
                           placeholder="Search by name or mobile..." 
                           class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300">
                    
                    <div x-show="filteredGuests.length > 0" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute z-10 w-full bg-white border-2 border-blue-200 rounded-xl mt-2 max-h-60 overflow-y-auto shadow-xl">
                        <template x-for="guest in filteredGuests" :key="guest.id">
                            <div @click="selectGuest(guest)" 
                                 class="px-4 py-3 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-all duration-150 group">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors" x-text="guest.name"></div>
                                        <div class="flex items-center gap-1 mt-1">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            <span class="text-sm text-gray-600" x-text="guest.mobile_number"></span>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Customer Input Fields - Hidden when B2B Reserved Customer is selected -->
            <div x-show="!(customerType === 'b2b' && useB2BReservedCustomer)" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="space-y-5">
                
                <!-- Customer Details Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Guest Name
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="guest_name" 
                                   x-model="guestName" 
                                   value="{{ old('guest_name') }}" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                                   placeholder="Enter guest name"
                                   :required="!(customerType === 'b2b' && useB2BReservedCustomer)">
                        </div>
                        @error('guest_name')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Mobile Number
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="guest_mobile" 
                                   x-model="guestMobile" 
                                   value="{{ old('guest_mobile') }}" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                                   placeholder="Enter mobile number"
                                   :required="!(customerType === 'b2b' && useB2BReservedCustomer)">
                        </div>
                        @error('guest_mobile')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
                
                <!-- Email Field -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email <span class="text-gray-500 font-normal">(Optional)</span>
                    </label>
                    <div class="relative">
                        <input type="email" 
                               name="guest_email" 
                               x-model="guestEmail" 
                               value="{{ old('guest_email') }}" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                               placeholder="Enter email address (optional)">
                    </div>
                    @error('guest_email')
                        <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- B2B Reserved Customer Info Display -->
            <div x-show="customerType === 'b2b' && useB2BReservedCustomer && {{ $hasB2bPartners ? 'true' : 'false' }}" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="p-4 sm:p-5 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-green-800 mb-1">Using B2B Reserved Customer</h4>
                        <p class="text-sm font-semibold text-green-700 mb-2" x-text="selectedPartnerReservedCustomer || 'Loading reserved customer details...'"></p>
                        <div x-show="useB2BReservedCustomer && selectedPartnerReservedCustomer && !selectedPartnerReservedCustomer.includes('No reserved') && !selectedPartnerReservedCustomer.includes('Error')" 
                             class="flex items-center gap-2 p-2 bg-white rounded-lg border border-green-200">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs text-green-600 font-medium">This customer is automatically selected for the B2B partner</span>
                        </div>
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
        <div x-show="showPropertySelectionModal" x-transition class="fixed inset-0 z-50 overflow-y-auto modal-backdrop">
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

@push('styles')
<style>
/* Date picker overflow fixes */
.ui-datepicker {
    z-index: 9999 !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.75rem !important;
    overflow: hidden !important;
}

.ui-datepicker-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    color: white !important;
    border-radius: 0 !important;
    padding: 0.75rem !important;
}

.ui-datepicker-title {
    color: white !important;
    font-weight: 600 !important;
}

.ui-datepicker-prev, .ui-datepicker-next {
    background: rgba(255, 255, 255, 0.2) !important;
    border: none !important;
    border-radius: 0.375rem !important;
    color: white !important;
    cursor: pointer !important;
}

.ui-datepicker-prev:hover, .ui-datepicker-next:hover {
    background: rgba(255, 255, 255, 0.3) !important;
}

.ui-datepicker table {
    width: 100% !important;
    margin: 0 !important;
}

.ui-datepicker td {
    border: none !important;
    padding: 0 !important;
}

.ui-datepicker td a {
    display: block !important;
    padding: 0.5rem !important;
    text-align: center !important;
    text-decoration: none !important;
    color: #374151 !important;
    border-radius: 0.375rem !important;
    margin: 0.125rem !important;
    transition: all 0.2s ease !important;
}

.ui-datepicker td a:hover {
    background-color: #f3f4f6 !important;
    color: #1f2937 !important;
}

.ui-datepicker td .ui-state-active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    font-weight: 600 !important;
}

.ui-datepicker .ui-datepicker-today a {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    font-weight: 600 !important;
}

/* Dropdown overflow fixes */
.relative {
    position: relative !important;
}

.absolute {
    position: absolute !important;
}

/* Ensure dropdowns appear above other content */
.absolute.z-10 {
    z-index: 1000 !important;
}

/* Partner search dropdown */
.absolute.z-10.w-full.bg-white.border.border-gray-300.rounded-lg.mt-1.max-h-40.overflow-y-auto {
    z-index: 1001 !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

/* Guest search dropdown */
.absolute.z-10.w-full.bg-white.border.border-gray-300.rounded-lg.mt-1.max-h-40.overflow-y-auto {
    z-index: 1002 !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

/* Property selection modal */
.fixed.inset-0.z-50 {
    z-index: 9998 !important;
}

/* Select2 dropdown fixes */
.select2-container {
    z-index: 1003 !important;
}

.select2-dropdown {
    z-index: 1004 !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

/* Ensure form sections don't clip dropdowns */
.bg-white.rounded-xl.shadow-sm.border.border-gray-200 {
    overflow: visible !important;
}

/* Mobile responsive fixes */
@media (max-width: 640px) {
    .ui-datepicker {
        width: 90% !important;
        left: 5% !important;
        right: 5% !important;
        margin: 0 !important;
    }
    
    .absolute.z-10.w-full {
        left: 0 !important;
        right: 0 !important;
        max-width: 100% !important;
    }
}

/* SCROLL FIX: Use backdrop blur instead of disabling body scroll */
.modal-backdrop {
    backdrop-filter: blur(4px);
    background: rgba(0, 0, 0, 0.4);
}

/* Remove the problematic body scroll disable */
body.modal-open {
    /* overflow: hidden !important; - REMOVED TO FIX SCROLL */
}
</style>
@endpush

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
            
            // SCROLL FIX: Ensure scroll is enabled on component initialization
            this.ensureScrollEnabled();
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
        
        handleSubmit(event) {
            // Check if form is valid
            const form = event.target;
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Find the first invalid field and focus it
                const invalidField = form.querySelector(':invalid');
                if (invalidField) {
                    invalidField.focus();
                    invalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            
            return true;
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
                
                if (response.ok && data.name) {
                    this.selectedPartnerReservedCustomer = data.name;
                } else {
                    // Partner doesn't have a reserved customer
                    this.selectedPartnerReservedCustomer = 'No reserved customer found for this partner';
                    this.useB2BReservedCustomer = false;
                }
            } catch (error) {
                console.error('Error loading partner reserved customer:', error);
                this.selectedPartnerReservedCustomer = 'Error loading reserved customer';
                this.useB2BReservedCustomer = false;
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
            
            // Automatically set B2B mode and reserved customer
            this.isB2B = true;
            this.customerType = 'b2b';
            this.useB2BReservedCustomer = true;
            
            // Load the reserved customer for this partner
            this.loadPartnerReservedCustomer(partner.uuid);
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
                
                // Update the select element for multiple properties case
                const select = document.querySelector('select[name="accommodation_id"]');
                if (select) {
                    select.innerHTML = '<option value="">Select Accommodation</option>';
                    
                    this.accommodations.forEach(acc => {
                        const option = document.createElement('option');
                        option.value = acc.id;
                        option.textContent = `${acc.display_name} - ₹${acc.base_price}`;
                        select.appendChild(option);
                    });
                }
                
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
                    this.selectedProperty = data.defaultPropertyId; // Set selectedProperty for form validation
                    this.selectedAccommodation = data.defaultAccommodationId; // Set selectedAccommodation for form validation
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
            // SCROLL FIX: Don't disable body scroll - use CSS backdrop instead
            // document.body.classList.add('modal-open'); - REMOVED
        },
        
        closePropertySelectionModal() {
            this.showPropertySelectionModal = false;
            this.selectedModalProperty = null;
            this.modalAccommodations = [];
            // SCROLL FIX: No need to restore scroll since we never disabled it
            // document.body.classList.remove('modal-open'); - REMOVED
        },
        
        // SCROLL FIX: Cleanup function to ensure scroll is never permanently disabled
        ensureScrollEnabled() {
            if (!this.showPropertySelectionModal) {
                document.body.classList.remove('modal-open');
            }
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
            
            // Hide the property/accommodation selection since we've made a selection
            this.showPropertyAccommodationSelection = false;
            this.showPropertySelection = false;
            
            // Set the default values for hidden inputs
            this.defaultPropertyId = this.selectedModalProperty.id;
            this.defaultAccommodationId = accommodation.id;
            
            // Update property info
            await this.loadPropertyInfo();
            
            // Calculate amount with the new accommodation price
            this.calculateAmount();
            
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

// Initialize datepickers when document is ready
$(document).ready(function() {
    // Common datepicker options with overflow fixes
    const datepickerOptions = {
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showAnim: 'fadeIn',
        showOptions: { duration: 200 },
        beforeShow: function(input, inst) {
            // Ensure datepicker appears above other elements
            setTimeout(function() {
                inst.dpDiv.css({
                    'z-index': 9999,
                    'position': 'absolute'
                });
                
                // Handle mobile positioning
                if (window.innerWidth <= 640) {
                    const inputOffset = $(input).offset();
                    const inputHeight = $(input).outerHeight();
                    const windowHeight = $(window).height();
                    const pickerHeight = inst.dpDiv.outerHeight();
                    
                    // Position above input if not enough space below
                    if (inputOffset.top + inputHeight + pickerHeight > windowHeight) {
                        inst.dpDiv.css({
                            'top': inputOffset.top - pickerHeight - 10,
                            'left': '5%',
                            'width': '90%'
                        });
                    }
                }
            }, 1);
        },
        onClose: function() {
            // Clean up any positioning classes
            $(this).removeClass('datepicker-active');
        }
    };
    
    // Initialize check-in date picker
    $('input[name="check_in_date"]').datepicker($.extend({}, datepickerOptions, {
        minDate: 0, // Disable past dates
        onSelect: function(dateText) {
            $(this).addClass('datepicker-active');
            // Update Alpine.js model
            const alpineComponent = Alpine.$data(document.querySelector('[x-data*="bookingCreateForm"]'));
            if (alpineComponent) {
                alpineComponent.checkInDate = dateText;
                alpineComponent.updateCheckOutDate();
                alpineComponent.checkPastBooking();
            }
        }
    }));
    
    // Initialize check-out date picker
    $('input[name="check_out_date"]').datepicker($.extend({}, datepickerOptions, {
        minDate: 1, // At least tomorrow
        onSelect: function(dateText) {
            $(this).addClass('datepicker-active');
            // Update Alpine.js model
            const alpineComponent = Alpine.$data(document.querySelector('[x-data*="bookingCreateForm"]'));
            if (alpineComponent) {
                alpineComponent.checkOutDate = dateText;
                alpineComponent.calculateDaysNights();
            }
        }
    }));
    
    // Update check-out date minimum when check-in date changes
    $('input[name="check_in_date"]').on('change', function() {
        const checkInDate = $(this).datepicker('getDate');
        if (checkInDate) {
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            $('input[name="check_out_date"]').datepicker('option', 'minDate', nextDay);
        }
    });
});
</script>
@endpush