@extends('layouts.app')

@section('title', 'Create Booking - Stay loops')

@section('header')
    <x-page-header 
        title="Create New Booking" 
        subtitle="Choose your booking type and complete the process" 
        icon="calendar-plus">
    </x-page-header>
@endsection

@section('content')
<div x-data="enhancedBookingForm()" x-init="init()" class="max-w-6xl mx-auto space-y-6">
    <!-- Booking Type Selector -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Choose Booking Type</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <button @click="setBookingType('normal')" 
                    :class="bookingType === 'normal' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
                    class="p-4 border-2 rounded-xl transition-all text-left">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-plus text-blue-600"></i>
                    </div>
                    <span class="font-semibold">Normal Booking</span>
                </div>
                <p class="text-sm text-gray-600">Property → Accommodation → Dates → Guest</p>
            </button>
            
            <button @click="setBookingType('ease')" 
                    :class="bookingType === 'ease' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
                    class="p-4 border-2 rounded-xl transition-all text-left">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-bolt text-green-600"></i>
                    </div>
                    <span class="font-semibold">Ease Booking</span>
                </div>
                <p class="text-sm text-gray-600">Dates → Available Rooms → Quick Confirm</p>
            </button>
            
            <button @click="setBookingType('b2b')" 
                    :class="bookingType === 'b2b' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
                    class="p-4 border-2 rounded-xl transition-all text-left">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-handshake text-purple-600"></i>
                    </div>
                    <span class="font-semibold">B2B Booking</span>
                </div>
                <p class="text-sm text-gray-600">Partner → Property → Amount Override</p>
            </button>
        </div>
    </div>

    <!-- Booking Form -->
    <form method="POST" action="{{ route('bookings.store') }}" @submit.prevent="submitBooking()" class="space-y-6">
        @csrf
        
        <!-- Normal Booking Flow -->
        <div x-show="bookingType === 'normal'" x-transition class="space-y-6">
            <!-- Step 1: Property Selection -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-blue-600 font-semibold">1</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Select Property</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <template x-for="property in properties" :key="property.id">
                        <button type="button" @click="selectProperty(property)"
                                :class="selectedProperty?.id === property.id ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
                                class="p-4 border-2 rounded-xl transition-all text-left">
                            <h4 class="font-semibold text-gray-900" x-text="property.name"></h4>
                            <p class="text-sm text-gray-600" x-text="property.category?.name"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="property.property_accommodations_count + ' accommodations'"></p>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Step 2: Accommodation Selection -->
            <div x-show="selectedProperty" x-transition class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-green-600 font-semibold">2</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Select Accommodation</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <template x-for="accommodation in selectedPropertyAccommodations" :key="accommodation.id">
                        <button type="button" @click="selectAccommodation(accommodation)"
                                :class="selectedAccommodation?.id === accommodation.id ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
                                class="p-4 border-2 rounded-xl transition-all text-left">
                            <h4 class="font-semibold text-gray-900" x-text="accommodation.name"></h4>
                            <p class="text-sm text-gray-600" x-text="accommodation.type + ' • Capacity: ' + accommodation.capacity"></p>
                            <p class="text-sm font-semibold text-green-600" x-text="'₹' + accommodation.base_price + ' / day'"></p>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Step 3: Dates & Guest Details -->
            <div x-show="selectedAccommodation" x-transition class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-green-600 font-semibold">3</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Dates & Guest Details</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                        <input type="text" x-model="bookingData.check_in_date" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 datepicker-input"
                               placeholder="Select check-in date" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                        <input type="text" x-model="bookingData.check_out_date" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 datepicker-input"
                               placeholder="Select check-out date" readonly>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                        <input type="number" x-model="bookingData.adults" required min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Children</label>
                        <input type="number" x-model="bookingData.children" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Ease Booking Flow -->
        <div x-show="bookingType === 'ease'" x-transition class="space-y-6">
            <!-- Step 1: Date Selection -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-green-600 font-semibold">1</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Select Your Dates</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                        <input type="text" x-model="bookingData.check_in_date" @change="findAvailableRooms()" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                        <input type="text" x-model="bookingData.check_out_date" @change="findAvailableRooms()" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
            </div>

            <!-- Step 2: Available Rooms -->
            <div x-show="bookingData.check_in_date && bookingData.check_out_date" x-transition class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-green-600 font-semibold">2</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Available Accommodations</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <template x-for="room in availableRooms" :key="room.id">
                        <button type="button" @click="selectAccommodation(room)"
                                :class="selectedAccommodation?.id === room.id ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
                                class="p-4 border-2 rounded-xl transition-all text-left">
                            <h4 class="font-semibold text-gray-900" x-text="room.name"></h4>
                            <p class="text-sm text-gray-600" x-text="room.property_name + ' • ' + room.type"></p>
                            <p class="text-sm font-semibold text-green-600" x-text="'₹' + room.base_price + ' / day'"></p>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Step 3: Guest Details -->
            <div x-show="selectedAccommodation" x-transition class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-green-600 font-semibold">3</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Guest Details</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                        <input type="number" x-model="bookingData.adults" required min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Children</label>
                        <input type="number" x-model="bookingData.children" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- B2B Booking Flow -->
        <div x-show="bookingType === 'b2b'" x-transition class="space-y-6">
            <!-- Step 1: Partner Selection -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-purple-600 font-semibold">1</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Select B2B Partner</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <template x-for="partner in b2bPartners" :key="partner.id">
                        <button type="button" @click="selectPartner(partner)"
                                :class="selectedPartner?.id === partner.id ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300'"
                                class="p-4 border-2 rounded-xl transition-all text-left">
                            <h4 class="font-semibold text-gray-900" x-text="partner.name"></h4>
                            <p class="text-sm text-gray-600" x-text="partner.mobile_number"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="partner.status"></p>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Step 2: Property & Accommodation -->
            <div x-show="selectedPartner" x-transition class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-purple-600 font-semibold">2</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Select Property & Accommodation</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <template x-for="property in properties" :key="property.id">
                        <button type="button" @click="selectProperty(property)"
                                :class="selectedProperty?.id === property.id ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300'"
                                class="p-4 border-2 rounded-xl transition-all text-left">
                            <h4 class="font-semibold text-gray-900" x-text="property.name"></h4>
                            <p class="text-sm text-gray-600" x-text="property.category?.name"></p>
                        </button>
                    </template>
                </div>
                
                <div x-show="selectedProperty" class="mt-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Select Accommodation</h4>
                    <div class="grid md:grid-cols-2 gap-4">
                        <template x-for="accommodation in selectedPropertyAccommodations" :key="accommodation.id">
                            <button type="button" @click="selectAccommodation(accommodation)"
                                    :class="selectedAccommodation?.id === accommodation.id ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300'"
                                    class="p-4 border-2 rounded-xl transition-all text-left">
                                <h4 class="font-semibold text-gray-900" x-text="accommodation.name"></h4>
                                <p class="text-sm text-gray-600" x-text="accommodation.type + ' • Capacity: ' + accommodation.capacity"></p>
                                <p class="text-sm font-semibold text-purple-600" x-text="'₹' + accommodation.base_price + ' / day'"></p>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Step 3: Dates & Amount -->
            <div x-show="selectedAccommodation" x-transition class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-purple-600 font-semibold">3</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Dates & Amount</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                        <input type="text" x-model="bookingData.check_in_date" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 datepicker-input"
                               placeholder="Select check-in date" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                        <input type="text" x-model="bookingData.check_out_date" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 datepicker-input"
                               placeholder="Select check-out date" readonly>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Negotiated Amount (₹)</label>
                        <input type="number" x-model="bookingData.total_amount" required min="0" step="0.01"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <p class="text-xs text-gray-500 mt-1">Override the standard rate for this partner</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Advance Paid (₹)</label>
                        <input type="number" x-model="bookingData.advance_paid" required min="0" step="0.01"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Guest Information (Common for all types) -->
        <div x-show="selectedAccommodation" x-transition class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Guest Information</h3>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Name</label>
                    <input type="text" x-model="bookingData.guest_name" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                    <input type="tel" x-model="bookingData.guest_mobile" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                <input type="email" x-model="bookingData.guest_email"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
        </div>

        <!-- Booking Summary -->
        <div x-show="selectedAccommodation" x-transition class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>
            
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Accommodation:</span>
                    <span class="font-semibold" x-text="selectedAccommodation?.name"></span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Dates:</span>
                    <span class="font-semibold" x-text="bookingData.check_in_date + ' to ' + bookingData.check_out_date"></span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Guests:</span>
                    <span class="font-semibold" x-text="bookingData.adults + ' adults' + (bookingData.children > 0 ? ', ' + bookingData.children + ' children' : '')"></span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="font-semibold text-green-600" x-text="'₹' + bookingData.total_amount"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Advance Paid:</span>
                    <span class="font-semibold" x-text="'₹' + bookingData.advance_paid"></span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div x-show="selectedAccommodation" class="flex justify-end">
            <button type="submit" 
                    class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl">
                Create Booking
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function enhancedBookingForm() {
    return {
        bookingType: '{{ request('type', 'normal') }}',
        properties: @json($properties ?? []),
        b2bPartners: @json($b2bPartners ?? []),
        availableRooms: [],
        selectedProperty: null,
        selectedAccommodation: null,
        selectedPartner: null,
        bookingData: {
            check_in_date: '',
            check_out_date: '',
            adults: 1,
            children: 0,
            guest_name: '',
            guest_mobile: '',
            guest_email: '',
            total_amount: 0,
            advance_paid: 0
        },

        init() {
            // Enhanced booking form initialized
        },

        setBookingType(type) {
            this.bookingType = type;
            this.resetSelection();
        },

        resetSelection() {
            this.selectedProperty = null;
            this.selectedAccommodation = null;
            this.selectedPartner = null;
            this.availableRooms = [];
        },

        selectProperty(property) {
            this.selectedProperty = property;
            this.selectedAccommodation = null;
            this.loadPropertyAccommodations();
        },

        selectAccommodation(accommodation) {
            this.selectedAccommodation = accommodation;
            this.bookingData.total_amount = accommodation.base_price;
        },

        selectPartner(partner) {
            this.selectedPartner = partner;
        },

        async loadPropertyAccommodations() {
            if (!this.selectedProperty) return;
            
            try {
                const response = await fetch(`/api/properties/${this.selectedProperty.uuid}/accommodations`);
                if (response.ok) {
                    const accommodations = await response.json();
                    this.selectedPropertyAccommodations = accommodations;
                }
            } catch (error) {
                // Error loading accommodations
            }
        },

        async findAvailableRooms() {
            if (!this.bookingData.check_in_date || !this.bookingData.check_out_date) return;
            
            try {
                const response = await fetch('/api/accommodations/available', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        check_in_date: this.bookingData.check_in_date,
                        check_out_date: this.bookingData.check_out_date
                    })
                });
                
                if (response.ok) {
                    this.availableRooms = await response.json();
                }
            } catch (error) {
                // Error finding available rooms
            }
        },

        async submitBooking() {
            const formData = {
                property_id: this.selectedProperty.id,
                accommodation_id: this.selectedAccommodation.id,
                check_in_date: this.bookingData.check_in_date,
                check_out_date: this.bookingData.check_out_date,
                adults: this.bookingData.adults,
                children: this.bookingData.children,
                guest_name: this.bookingData.guest_name,
                guest_mobile: this.bookingData.guest_mobile,
                guest_email: this.bookingData.guest_email,
                total_amount: this.bookingData.total_amount,
                advance_paid: this.bookingData.advance_paid,
                booking_type: 'per_day'
            };

            if (this.bookingType === 'b2b' && this.selectedPartner) {
                formData.b2b_partner_id = this.selectedPartner.uuid;
            }

            try {
                const response = await fetch('/bookings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    window.location.href = '/booking-dashboard';
                } else {
                    const error = await response.json();
                    alert('Error creating booking: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                // Error occurred
                alert('Error creating booking. Please try again.');
            }
        },

        get selectedPropertyAccommodations() {
            return this.selectedProperty?.accommodations || [];
        }
    }
}
</script>
@endpush
