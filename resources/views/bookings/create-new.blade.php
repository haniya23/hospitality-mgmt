@extends('layouts.app')

@section('title', 'Create Booking')

@section('header')
    @include('partials.bookings.create-header')
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('bookings.store') }}" x-data="bookingCreateForm()" x-init="init()" @submit="handleSubmit" class="space-y-4 sm:space-y-6">
        @csrf
        
        @include('partials.bookings.property-accommodation')
        
        @include('partials.bookings.check-in-details')

        @include('partials.bookings.amount-calculation')

        @include('partials.bookings.commission')

        @include('partials.bookings.customer-information')

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
