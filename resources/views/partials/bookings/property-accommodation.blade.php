<!-- Property & Accommodation Section -->
<div class="space-y-4 sm:space-y-6">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Property & Accommodation</h3>
                <p class="text-sm text-gray-600">Select your property and accommodation</p>
            </div>
        </div>
        
        <!-- Dynamic Plus/Submit Button -->
        <button x-show="!selectedProperty && !selectedAccommodation" 
                @click="openPropertySelectionModal()" 
                class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95 group">
            <svg class="w-6 h-6 text-white transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
        </button>
        
        <!-- Submit Button (when property is selected) -->
        <button x-show="selectedProperty && selectedAccommodation" 
                type="submit"
                class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95 group">
            <svg class="w-6 h-6 text-white transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </button>
    </div>
    
    <!-- Two Card Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Property Card -->
        <div x-show="selectedPropertyInfo || selectedAccommodationInfo" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="bg-gradient-to-br from-white to-blue-50 rounded-xl shadow-md hover:shadow-lg border-2 border-blue-200 p-4 sm:p-6 transition-all duration-300">
            
            <!-- Property Header -->
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-lg font-bold text-blue-900 mb-1">Property</h4>
                    <p class="text-sm font-semibold text-blue-700" x-text="selectedPropertyInfo?.name || selectedAccommodationInfo?.property_name || 'Select a property'"></p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Property Details -->
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm border border-blue-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Location</span>
                    </div>
                    <span class="text-sm font-semibold text-blue-800">Selected Property</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm border border-blue-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Status</span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Available
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Accommodation Card -->
        <div x-show="selectedAccommodationInfo" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="bg-gradient-to-br from-white to-emerald-50 rounded-xl shadow-md hover:shadow-lg border-2 border-emerald-200 p-4 sm:p-6 transition-all duration-300">
            
            <!-- Accommodation Header -->
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-lg font-bold text-emerald-900 mb-1">Accommodation</h4>
                    <p class="text-sm font-semibold text-emerald-700" x-text="selectedAccommodationInfo?.display_name || 'Select accommodation'"></p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Accommodation Details -->
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm border border-emerald-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Price per day</span>
                    </div>
                    <span class="text-lg font-bold text-emerald-800" x-text="'₹' + (selectedAccommodationPrice || 0).toLocaleString()"></span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm border border-emerald-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 01 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 01 9.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Max occupancy</span>
                    </div>
                    <span class="text-sm font-bold text-emerald-800" x-text="(selectedAccommodationInfo?.max_occupancy || 0) + ' guests'"></span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm border border-emerald-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Type</span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800" x-text="selectedAccommodationInfo?.predefined_type?.name || 'Custom'"></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Property Selection Forms (when multiple properties/accommodations) -->
    <div x-show="showPropertyAccommodationSelection" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
        
        <!-- Property Selection (when multiple properties) -->
        <template x-if="showPropertySelection">
            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Property
                        </label>
                        <select name="property_id" x-model="selectedProperty" @change="loadAccommodations()" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800 select2-dropdown" 
                                required>
                            <option value="">Select Property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('property_id')
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Accommodation
                        </label>
                        <select name="accommodation_id" x-model="selectedAccommodation" @change="updateAccommodationPrice()" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800 select2-dropdown" 
                                required>
                            <option value="">Select Accommodation</option>
                        </select>
                        @error('accommodation_id')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </template>
        
        <!-- Accommodation Selection Only (when single property, multiple accommodations) -->
        <template x-if="!showPropertySelection">
            <div class="space-y-5">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 713 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Accommodation
                        </label>
                        <select name="accommodation_id" x-model="selectedAccommodation" @change="updateAccommodationPrice()" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800 select2-dropdown" 
                                required>
                            <option value="">Select Accommodation</option>
                            <template x-for="accommodation in singlePropertyAccommodations" :key="accommodation.id">
                                <option :value="accommodation.id" x-text="accommodation.display_name"></option>
                            </template>
                        </select>
                        @error('accommodation_id')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </template>
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
            <!-- No hidden accommodation_id here since the select dropdown handles it -->
        </div>
    </template>
</div>