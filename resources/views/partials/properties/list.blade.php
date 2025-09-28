@push('styles')
<style>
    .property-card { 
        background: white; 
        transition: all 0.3s ease;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    .property-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 40px rgba(0,0,0,0.15); 
    }
    .status-active { 
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #059669; 
    }
    .status-pending { 
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706; 
    }
    .status-inactive { 
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626; 
    }
    .property-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .action-btn {
        transition: all 0.2s ease;
        border-radius: 12px;
    }
    .action-btn:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

<div class="space-y-4">
    <template x-for="property in filteredProperties" :key="property.id">
        <div class="property-card p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <div class="flex items-center space-x-3 mb-3 sm:mb-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl property-icon flex items-center justify-center text-white font-bold">
                        <i class="fas fa-building text-lg sm:text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800 text-lg" x-text="property.name"></h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="text-xs px-3 py-1 rounded-full font-medium"
                                  :class="'status-' + property.status"
                                  x-text="property.status.charAt(0).toUpperCase() + property.status.slice(1)"></span>
                            <span class="text-xs text-gray-500" x-text="property.category?.name"></span>
                        </div>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-xl font-bold text-gray-800" x-text="property.property_accommodations_count + ' accommodations'"></div>
                    <div class="text-sm text-gray-500" x-text="property.bookings_count + ' bookings'"></div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-4">
                <div class="flex items-center space-x-2 mb-3">
                    <i class="fas fa-map-marker-alt text-gray-500"></i>
                    <span class="text-sm text-gray-600 font-medium" x-text="property.location?.city?.name + ', ' + property.location?.city?.district?.state?.name"></span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-800" x-text="property.property_accommodations_count"></div>
                        <div class="text-xs text-gray-500">Accommodations</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-800" x-text="property.bookings_count || 0"></div>
                        <div class="text-xs text-gray-500">Bookings</div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <button @click="openBookingModal(property)" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-green-600 hover:to-green-700 transition text-center action-btn">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Add Booking
                </button>
                <div class="flex gap-2">
                    <a :href="'/properties/' + property.uuid + '/edit'" class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-700 py-3 px-4 rounded-xl font-medium text-sm hover:from-blue-200 hover:to-blue-300 transition action-btn">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="bg-gradient-to-r from-red-100 to-red-200 text-red-700 py-3 px-4 rounded-xl font-medium text-sm hover:from-red-200 hover:to-red-300 transition action-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-if="filteredProperties.length === 0">
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-blue-400 to-purple-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                <i class="fas fa-building"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Properties Found</h3>
            <p class="text-gray-500 text-sm mb-4">Start by adding your first property.</p>
            <a href="{{ route('properties.create') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium">
                <i class="fas fa-plus mr-2"></i>
                Add Property
            </a>
        </div>
    </template>
</div>

<!-- Property Booking Modal -->
<div x-show="showBookingModal" x-transition class="fixed inset-0 z-50 overflow-y-auto backdrop-blur-sm bg-black/40">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Add Booking</h3>
                    <p class="text-sm text-gray-600" x-text="'Create booking for ' + selectedProperty?.name"></p>
                </div>
                <button @click="closeBookingModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div x-show="!selectedAccommodation" class="space-y-4">
                    <h4 class="text-md font-semibold text-gray-900">Select Accommodation</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <template x-for="accommodation in propertyAccommodations" :key="accommodation.id">
                            <button @click="selectAccommodation(accommodation)" 
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
                
                <div x-show="selectedAccommodation" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-md font-semibold text-gray-900">Selected Accommodation</h4>
                        <button @click="selectedAccommodation = null; customPrice = null" class="text-sm text-blue-600 hover:text-blue-800">
                            Change
                        </button>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium text-gray-900" x-text="selectedAccommodation?.display_name"></h5>
                        <p class="text-sm text-gray-600" x-text="selectedAccommodation?.predefined_type?.name || 'Custom'"></p>
                        <p class="text-sm text-gray-500" x-text="'Max occupancy: ' + selectedAccommodation?.max_occupancy"></p>
                        <p class="font-semibold text-green-600" x-text="'₹' + (customPrice || selectedAccommodation?.base_price) + ' per day'"></p>
                    </div>
                    
                    <!-- Price Override -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Price Override (Optional)</label>
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-500">₹</span>
                            <input type="number" x-model="customPrice" 
                                   :placeholder="selectedAccommodation?.base_price"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <button @click="customPrice = null" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">
                                Reset
                            </button>
                        </div>
                        <p class="text-xs text-gray-500">Leave empty to use default price</p>
                    </div>
                    
                    <div class="text-center">
                        <a :href="'/bookings/create?property_uuid=' + selectedProperty?.uuid + '&accommodation_uuid=' + selectedAccommodation?.uuid + (customPrice ? '&custom_price=' + customPrice : '')" 
                           class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Continue to Booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>