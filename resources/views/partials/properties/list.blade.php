@push('styles')
<style>
    .property-card { 
        background: white; 
        transition: all 0.3s ease;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        position: relative;
    }
    .property-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 40px rgba(0,0,0,0.15); 
    }
    .property-card .clickable-area {
        transition: all 0.2s ease;
    }
    .property-card .clickable-area:hover {
        background-color: rgba(249, 250, 251, 0.8);
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
    
    /* Professional toggle styling */
    .toggle-container {
        background: linear-gradient(145deg, #f8fafc, #e2e8f0);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
    }
    
    .toggle-button-active {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .toggle-button-inactive {
        background: transparent;
    }
    
    .toggle-button-inactive:hover {
        background: rgba(255, 255, 255, 0.5);
    }
</style>
@endpush

<div class="space-y-4 overflow-y-auto">
    <template x-for="property in filteredProperties" :key="property.id">
        <div class="property-card p-4 sm:p-6">
            <!-- Action buttons moved to top for better mobile experience -->
            <div class="flex justify-between items-start mb-4">
                <!-- Clickable property info section -->
                <a :href="!showArchive ? '/properties/' + property.uuid : '#'" 
                   :class="!showArchive ? 'cursor-pointer clickable-area -m-2 p-2 rounded-lg' : 'cursor-default'"
                   class="flex items-center space-x-3 flex-1 min-w-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl property-icon flex items-center justify-center text-white font-bold flex-shrink-0">
                        <i class="fas fa-building text-lg sm:text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center">
                            <h3 class="font-semibold text-gray-800 text-lg truncate" x-text="property.name"></h3>
                            <template x-if="!showArchive">
                                <i class="fas fa-external-link-alt text-xs text-gray-400 ml-2 opacity-60"></i>
                            </template>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="text-xs px-3 py-1 rounded-full font-medium"
                                  :class="'status-' + property.status"
                                  x-text="property.status.charAt(0).toUpperCase() + property.status.slice(1)"></span>
                            <span class="text-xs text-gray-500" x-text="property.category?.name"></span>
                            <!-- Archive status badge -->
                            <template x-if="showArchive">
                                <span class="text-xs px-3 py-1 rounded-full font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-trash mr-1"></i>
                                    Deleted
                                </span>
                            </template>
                        </div>
                    </div>
                </a>
                <!-- Edit and Delete buttons moved to top right -->
                <div class="flex gap-2 flex-shrink-0 ml-3" @click.stop>
                    <!-- Edit button - disabled for archived properties -->
                    <a :href="!showArchive ? '/properties/' + property.uuid + '/edit' : '#'" 
                       :class="!showArchive ? 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-700 hover:from-blue-200 hover:to-blue-300' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                       class="w-10 h-10 rounded-xl font-medium text-sm transition action-btn flex items-center justify-center"
                       :title="!showArchive ? 'Edit Property' : 'Cannot edit archived property'">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <!-- Delete/Status button -->
                    <template x-if="!showArchive">
                        <button @click.stop="requestPropertyDeletion(property)" 
                                class="w-10 h-10 bg-gradient-to-r from-red-100 to-red-200 text-red-700 rounded-xl font-medium text-sm hover:from-red-200 hover:to-red-300 transition action-btn flex items-center justify-center"
                                title="Request Deletion">
                            <i class="fas fa-trash"></i>
                        </button>
                    </template>
                    
                    <template x-if="showArchive">
                        <div class="w-10 h-10 bg-gradient-to-r from-red-100 to-red-200 text-red-700 rounded-xl font-medium text-sm flex items-center justify-center"
                             title="Property deleted">
                            <i class="fas fa-trash"></i>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Content based on view type -->
            <template x-if="!showArchive">
                <div>
                    <!-- Property stats for active properties -->
                    <div class="flex justify-between items-center mb-4 text-center sm:text-left">
                        <div>
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

                    <!-- Action buttons for bookings -->
                    <div class="flex flex-col sm:flex-row gap-2" @click.stop>
                        <button @click.stop="openBookingModal(property)" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-green-600 hover:to-green-700 transition text-center action-btn">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Add Booking
                        </button>
                        @if($hasB2bPartners)
                        <button @click.stop="openB2BBookingModal(property)" class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-purple-600 hover:to-purple-700 transition text-center action-btn">
                            <i class="fas fa-handshake mr-2"></i>
                            B2B Booking
                        </button>
                        @else
                        <a href="{{ route('b2b.create') }}" class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-gray-500 hover:to-gray-600 transition text-center action-btn">
                            <i class="fas fa-plus mr-2"></i>
                            Add B2B Partner
                        </a>
                        @endif
                    </div>
                </div>
            </template>
            
            <!-- Simplified view for archived properties -->
            <template x-if="showArchive">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-map-marker-alt text-gray-500"></i>
                        <span class="text-sm text-gray-600 font-medium" x-text="property.location?.city?.name + ', ' + property.location?.city?.district?.state?.name"></span>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <template x-if="filteredProperties.length === 0">
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-blue-400 to-purple-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                <i class="fas fa-building"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                <span x-show="!showArchive">No Properties Found</span>
                <span x-show="showArchive">No Archived Properties</span>
            </h3>
            <p class="text-gray-500 text-sm mb-4">
                <span x-show="!showArchive">Start by adding your first property.</span>
                <span x-show="showArchive">No deleted properties found.</span>
            </p>
            <a x-show="!showArchive" href="{{ route('properties.create') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium">
                <i class="fas fa-plus mr-2"></i>
                Add Property
            </a>
        </div>
    </template>
    
    <!-- Professional Toggle at Bottom -->
    <div class="mt-8 border-t border-gray-200 pt-6">
        <div class="flex justify-center">
            <div class="toggle-container p-1 rounded-xl inline-flex">
                <button @click="showArchive = false" 
                        :class="!showArchive ? 'toggle-button-active text-gray-900' : 'toggle-button-inactive text-gray-500'"
                        class="px-6 py-2.5 rounded-lg font-medium text-sm transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-building text-sm"></i>
                    <span>Active</span>
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full ml-1 font-semibold" x-text="properties.length"></span>
                </button>
                <button @click="showArchive = true" 
                        :class="showArchive ? 'toggle-button-active text-gray-900' : 'toggle-button-inactive text-gray-500'"
                        class="px-6 py-2.5 rounded-lg font-medium text-sm transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-archive text-sm"></i>
                    <span>Archive</span>
                    <span class="bg-orange-100 text-orange-800 text-xs px-2 py-0.5 rounded-full ml-1 font-semibold" x-text="archivedProperties.length"></span>
                </button>
            </div>
        </div>
        
        <!-- Subtle description -->
        <div class="text-center mt-3">
            <p class="text-xs text-gray-500">
                <span x-show="!showArchive">Manage your active properties</span>
                <span x-show="showArchive">Deleted properties (archived)</span>
            </p>
        </div>
    </div>
</div>

<!-- Property Booking Modal -->
<div x-show="showBookingModal" x-transition class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40" style="z-index: 99999 !important;">
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

<!-- B2B Booking Modal -->
<div x-show="showB2BBookingModal" x-transition class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40" style="z-index: 99999 !important;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">B2B Booking</h3>
                    <p class="text-sm text-gray-600" x-text="'Create B2B booking for ' + selectedB2BProperty?.name"></p>
                </div>
                <button @click="closeB2BBookingModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div x-show="!selectedB2BPartner" class="space-y-4">
                    <h4 class="text-md font-semibold text-gray-900">Select B2B Partner</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <template x-for="partner in b2bPartners" :key="partner.uuid">
                            <button @click="selectB2BPartner(partner)" 
                                    class="p-4 border border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors text-left">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-medium text-gray-900" x-text="partner.partner_name"></h5>
                                        <p class="text-sm text-gray-600" x-text="partner.commission_rate + '% commission'"></p>
                                    </div>
                                    <div class="text-right">
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
                
                <div x-show="selectedB2BPartner && !selectedB2BAccommodation" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-md font-semibold text-gray-900">Select Accommodation</h4>
                        <button @click="selectedB2BPartner = null" class="text-sm text-purple-600 hover:text-purple-800">
                            Back to Partners
                        </button>
                    </div>
                    
                    <div class="bg-purple-50 rounded-lg p-4 mb-4">
                        <h5 class="font-medium text-purple-900" x-text="selectedB2BPartner?.partner_name"></h5>
                        <p class="text-sm text-purple-600" x-text="selectedB2BPartner?.commission_rate + '% commission'"></p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <template x-for="accommodation in b2bPropertyAccommodations" :key="accommodation.id">
                            <button @click="selectB2BAccommodation(accommodation)" 
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
                
                <div x-show="selectedB2BAccommodation" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-md font-semibold text-gray-900">B2B Booking Details</h4>
                        <button @click="selectedB2BAccommodation = null; b2bCustomPrice = null; b2bCommissionType = 'percentage'; b2bCommissionValue = selectedB2BPartner?.commission_rate || 10" class="text-sm text-purple-600 hover:text-purple-800">
                            Change Accommodation
                        </button>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium text-gray-900" x-text="selectedB2BAccommodation?.display_name"></h5>
                        <p class="text-sm text-gray-600" x-text="selectedB2BAccommodation?.predefined_type?.name || 'Custom'"></p>
                        <p class="text-sm text-gray-500" x-text="'Max occupancy: ' + selectedB2BAccommodation?.max_occupancy"></p>
                        <p class="font-semibold text-green-600" x-text="'₹' + (b2bCustomPrice || selectedB2BAccommodation?.base_price) + ' per day'"></p>
                    </div>
                    
                    <!-- Price Override -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Price Override (Optional)</label>
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-500">₹</span>
                            <input type="number" x-model="b2bCustomPrice" 
                                   :placeholder="selectedB2BAccommodation?.base_price"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <button @click="b2bCustomPrice = null" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">
                                Reset
                            </button>
                        </div>
                        <p class="text-xs text-gray-500">Leave empty to use default price</p>
                    </div>
                    
                    <!-- Commission Settings -->
                    <div class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-900">Commission Settings</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Commission Type</label>
                                <select x-model="b2bCommissionType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="percentage">Percentage</option>
                                    <option value="amount">Amount</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span x-show="b2bCommissionType === 'percentage'">Commission (%)</span>
                                    <span x-show="b2bCommissionType === 'amount'">Commission Amount (₹)</span>
                                </label>
                                <input type="number" x-model="b2bCommissionValue" 
                                       :placeholder="b2bCommissionType === 'percentage' ? '10' : '100'"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a :href="'/bookings/create?property_uuid=' + selectedB2BProperty?.uuid + '&accommodation_uuid=' + selectedB2BAccommodation?.uuid + '&b2b_partner_uuid=' + selectedB2BPartner?.uuid + (b2bCustomPrice ? '&custom_price=' + b2bCustomPrice : '') + '&commission_type=' + b2bCommissionType + '&commission_value=' + b2bCommissionValue" 
                           class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-handshake mr-2"></i>
                            Continue to B2B Booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>