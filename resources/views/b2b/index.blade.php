@extends('layouts.app')

@section('title', 'B2B Management')

@section('header')
    <div x-data="b2bData()" x-init="init()">
        @include('partials.b2b.header')
    </div>
@endsection

@section('content')
<div x-data="b2bData()" x-init="init()" class="space-y-6">
    @include('partials.b2b.search')
    
    <!-- Partners List with Property-style Cards -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">B2B Partners (<span x-text="filteredPartners.length"></span>)</h3>
        </div>
        
        <div class="p-4 sm:p-6">
            @include('partials.b2b.partners')
        </div>
    </div>

    <!-- B2B Booking Modal -->
    <div x-show="showB2BBookingModal" x-transition class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40" style="z-index: 99999 !important;">
        <div class="flex min-h-full items-center justify-center p-4 pb-20 sm:pb-4">
            <div class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[90vh] sm:max-h-[95vh] flex flex-col">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">B2B Booking</h3>
                        <p class="text-sm text-gray-600" x-text="'Create booking for ' + selectedB2BPartner?.partner_name"></p>
                    </div>
                    <button @click="closeB2BBookingModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Step 1: Select Property -->
                    <div x-show="!selectedB2BProperty" class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-900">Select Property</h4>
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="property in b2bProperties" :key="property.id">
                                <button @click="selectB2BProperty(property)" 
                                        class="p-4 border border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors text-left">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-medium text-gray-900" x-text="property.name"></h5>
                                            <p class="text-sm text-gray-600" x-text="property.property_accommodations_count + ' accommodations'"></p>
                                            <p class="text-sm text-gray-500" x-text="property.location?.city?.name + ', ' + property.location?.city?.district?.state?.name"></p>
                                        </div>
                                        <div class="text-right">
                                            <i class="fas fa-chevron-right text-gray-400"></i>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Step 2: Select Accommodation -->
                    <div x-show="selectedB2BProperty && !selectedB2BAccommodation" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-md font-semibold text-gray-900">Select Accommodation</h4>
                            <button @click="selectedB2BProperty = null; b2bPropertyAccommodations = []" class="text-sm text-purple-600 hover:text-purple-800">
                                Back to Properties
                            </button>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-4 mb-4">
                            <h5 class="font-medium text-purple-900" x-text="selectedB2BProperty?.name"></h5>
                            <p class="text-sm text-purple-600" x-text="selectedB2BProperty?.property_accommodations_count + ' accommodations available'"></p>
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
                    
                    <!-- Step 3: Booking Details -->
                    <div x-show="selectedB2BAccommodation" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-md font-semibold text-gray-900">B2B Booking Details</h4>
                            <button @click="selectedB2BAccommodation = null; b2bCustomPrice = null; b2bCommissionType = 'percentage'; b2bCommissionValue = selectedB2BPartner?.commission_rate || 10" class="text-sm text-purple-600 hover:text-purple-800">
                                Change Accommodation
                            </button>
                        </div>
                        
                        <!-- Partner Info -->
                        <div class="bg-purple-50 rounded-lg p-4">
                            <h5 class="font-medium text-purple-900" x-text="selectedB2BPartner?.partner_name"></h5>
                            <p class="text-sm text-purple-600" x-text="selectedB2BPartner?.commission_rate + '% commission'"></p>
                        </div>
                        
                        <!-- Property & Accommodation Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="font-medium text-gray-900" x-text="selectedB2BProperty?.name + ' - ' + selectedB2BAccommodation?.display_name"></h5>
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
                            <a :href="generateBookingUrl()" 
                               @click="console.log('🚀 Navigating to:', generateBookingUrl())"
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
</div>
@endsection

@push('scripts')
<script>
function b2bData() {
    return {
        search: '',
        partners: @json($partners ?? []),

        get filteredPartners() {
            return this.partners.filter(partner => 
                partner.partner_name.toLowerCase().includes(this.search.toLowerCase()) ||
                (partner.contact_user && partner.contact_user.name.toLowerCase().includes(this.search.toLowerCase()))
            );
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        get activePartners() {
            return this.partners.filter(partner => partner.status === 'active').length;
        },

        get totalBookings() {
            return this.partners.reduce((total, partner) => total + (partner.reservations_count || 0), 0);
        },

        get totalPartners() {
            return this.partners.length;
        },

        async init() {
            // Partners are already loaded from server
        },

        // B2B booking modal state
        showB2BBookingModal: false,
        selectedB2BPartner: null,
        selectedB2BProperty: null,
        selectedB2BAccommodation: null,
        b2bProperties: [],
        b2bPropertyAccommodations: [],
        b2bCustomPrice: null,
        b2bCommissionType: 'percentage',
        b2bCommissionValue: 10,

        async openB2BBookingModal(partner) {
            this.selectedB2BPartner = partner;
            this.selectedB2BProperty = null;
            this.selectedB2BAccommodation = null;
            this.b2bCustomPrice = null;
            this.b2bCommissionType = 'percentage';
            this.b2bCommissionValue = partner.commission_rate || 10;
            this.showB2BBookingModal = true;
            
            // Load properties
            try {
                const response = await fetch('/api/properties');
                const properties = await response.json();
                console.log('🔍 Properties loaded:', properties);
                this.b2bProperties = properties;
            } catch (error) {
                console.error('❌ Error loading properties:', error);
                this.b2bProperties = [];
            }
        },

        closeB2BBookingModal() {
            this.showB2BBookingModal = false;
            this.selectedB2BPartner = null;
            this.selectedB2BProperty = null;
            this.selectedB2BAccommodation = null;
            this.b2bProperties = [];
            this.b2bPropertyAccommodations = [];
            this.b2bCustomPrice = null;
            this.b2bCommissionType = 'percentage';
            this.b2bCommissionValue = 10;
        },

        async selectB2BProperty(property) {
            console.log('🏠 Property selected:', property);
            this.selectedB2BProperty = property;
            this.selectedB2BAccommodation = null;
            this.b2bCustomPrice = null;
            
            try {
                const response = await fetch(`/api/properties/${property.id}/accommodations`);
                const accommodations = await response.json();
                console.log('🏨 Accommodations loaded:', accommodations);
                this.b2bPropertyAccommodations = accommodations;
            } catch (error) {
                console.error('❌ Error loading accommodations:', error);
                this.b2bPropertyAccommodations = [];
            }
        },

        selectB2BAccommodation(accommodation) {
            console.log('🛏️ Accommodation selected:', accommodation);
            this.selectedB2BAccommodation = accommodation;
        },

        generateBookingUrl() {
            const url = '/bookings/create?accommodation_uuid=' + this.selectedB2BAccommodation?.uuid + 
                       '&b2b_partner_uuid=' + this.selectedB2BPartner?.uuid + 
                       (this.b2bCustomPrice ? '&custom_price=' + this.b2bCustomPrice : '') + 
                       '&commission_type=' + this.b2bCommissionType + 
                       '&commission_value=' + this.b2bCommissionValue;
            
            console.log('🔗 Generated URL (Simplified):', url);
            console.log('📊 URL Components:', {
                accommodation_uuid: this.selectedB2BAccommodation?.uuid,
                b2b_partner_uuid: this.selectedB2BPartner?.uuid,
                custom_price: this.b2bCustomPrice,
                commission_type: this.b2bCommissionType,
                commission_value: this.b2bCommissionValue
            });
            
            return url;
        }
    }
}
</script>
@endpush