@extends('layouts.mobile')

@section('title', 'Bookings - Hospitality Manager')
@section('page-title', 'Bookings')

@section('content')
    <div id="booking-management" x-data="bookingManager()" x-init="init()">
        <!-- Flash Messages -->
        <div x-show="message" x-transition class="mb-4 p-4 rounded-xl" :class="messageType === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'">
            <span x-text="message"></span>
        </div>

        <!-- Header -->
        <div class="space-y-4 mb-6">
            <div>
                <h2 class="heading-1">Booking Management</h2>
                <p class="body-text">Manage your property bookings</p>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <!-- Property Selector -->
                <div class="flex-1 relative">
                    <select x-model="selectedProperty" @change="loadBookings()" class="form-select">
                        <option value="">All Properties</option>
                        <template x-for="property in properties" :key="property.id">
                            <option :value="property.id" x-text="property.name"></option>
                        </template>
                    </select>
                </div>
                
                <!-- New Booking Button -->
                <button @click="openBookingModal()" class="btn-primary">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Booking
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Bookings -->
            <div class="glass-card overflow-hidden">
                <div class="px-6 py-4 border-b border-glass-border">
                    <h3 class="heading-3 text-yellow-300">Pending Bookings (<span x-text="pendingBookings.length"></span>)</h3>
                </div>
                
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    <template x-for="booking in pendingBookings" :key="booking.id">
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h4 class="font-medium text-gray-900" x-text="booking.guest.name"></h4>
                                        <span x-show="booking.b2b_partner" class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">B2B</span>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div x-text="booking.accommodation.property.name + ' - ' + booking.accommodation.display_name"></div>
                                        <div x-text="formatDateRange(booking.check_in_date, booking.check_out_date)"></div>
                                    </div>
                                </div>
                                <div class="text-right space-y-1">
                                    <div class="font-semibold text-gray-900" x-text="'₹' + formatNumber(booking.total_amount)"></div>
                                    <div class="space-y-1">
                                        <button @click="toggleBookingStatus(booking.id)" class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                            Activate
                                        </button>
                                        <button @click="openCancelModal(booking.id)" class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="pendingBookings.length === 0" class="p-8 text-center text-gray-500">
                        <div class="text-sm">No pending bookings</div>
                    </div>
                </div>
            </div>

            <!-- Active Bookings -->
            <div class="glass-card overflow-hidden">
                <div class="px-6 py-4 border-b border-glass-border">
                    <h3 class="heading-3 text-green-300">Active Bookings (<span x-text="activeBookings.length"></span>)</h3>
                </div>
                
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    <template x-for="booking in activeBookings" :key="booking.id">
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h4 class="font-medium text-gray-900" x-text="booking.guest.name"></h4>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full" :class="booking.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'" x-text="booking.status.charAt(0).toUpperCase() + booking.status.slice(1)"></span>
                                        <span x-show="booking.b2b_partner" class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">B2B</span>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div x-text="booking.accommodation.property.name + ' - ' + booking.accommodation.display_name"></div>
                                        <div x-text="formatDateRange(booking.check_in_date, booking.check_out_date)"></div>
                                    </div>
                                </div>
                                <div class="text-right space-y-1">
                                    <div class="font-semibold text-gray-900" x-text="'₹' + formatNumber(booking.total_amount)"></div>
                                    <div x-show="booking.balance_pending > 0" class="text-xs text-orange-600" x-text="'₹' + formatNumber(booking.balance_pending) + ' pending'"></div>
                                    <button @click="openCancelModal(booking.id)" class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="activeBookings.length === 0" class="p-8 text-center text-gray-500">
                        <div class="text-sm">No active bookings</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-yellow-300" x-text="pendingBookings.length"></div>
                <div class="small-text text-secondary">Pending</div>
            </div>
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-green-300" x-text="activeBookings.filter(b => b.status === 'confirmed').length"></div>
                <div class="small-text text-secondary">Confirmed</div>
            </div>
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-blue-300" x-text="activeBookings.filter(b => b.status === 'checked_in').length"></div>
                <div class="small-text text-secondary">Checked In</div>
            </div>
            <div class="glass-card p-4 text-center">
                <div class="text-xl font-bold text-primary" x-text="'₹' + formatNumber(totalValue)"></div>
                <div class="small-text text-secondary">Total Value</div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <div x-show="showCancelModal" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-black/50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancel Booking</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                            <select x-model="cancelReason" class="w-full border border-gray-300 rounded-xl px-3 py-2">
                                <option value="">Select reason</option>
                                <option value="Guest Request">Guest Request</option>
                                <option value="No Show">No Show</option>
                                <option value="Payment Issue">Payment Issue</option>
                                <option value="Property Issue">Property Issue</option>
                                <option value="Overbooking">Overbooking</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea x-model="cancelDescription" rows="3" class="w-full border border-gray-300 rounded-xl px-3 py-2" placeholder="Optional description..."></textarea>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mt-6">
                        <button @click="closeCancelModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button @click="cancelBooking()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">
                            Confirm Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Modal -->
        <div x-show="showBookingModal" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-black/40">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl max-h-[95vh] flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 pb-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-t-2xl border-b border-emerald-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">New Booking</h3>
                                <p class="text-sm text-emerald-600 font-medium">Create a new reservation</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <!-- Mode Toggle -->
                            <div class="flex bg-white/80 rounded-lg p-1 shadow-sm">
                                <button @click="bookingMode = 'quick'" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="bookingMode === 'quick' ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-gray-600'">
                                    Quick
                                </button>
                                <button @click="bookingMode = 'full'" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="bookingMode === 'full' ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-gray-600'">
                                    Full
                                </button>
                            </div>
                            <button @click="closeBookingModal()" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <form @submit.prevent="saveBooking()" class="space-y-6">
                            <!-- Property & Accommodation -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Property</label>
                                    <select x-model="booking.property_id" @change="loadAccommodations()" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                        <option value="">Select Property</option>
                                        <template x-for="property in properties" :key="property.id">
                                            <option :value="property.id" x-text="property.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Accommodation</label>
                                    <select x-model="booking.accommodation_id" @change="calculateRate()" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                        <option value="">Select Accommodation</option>
                                        <template x-for="acc in accommodations" :key="acc.id">
                                            <option :value="acc.id" x-text="acc.display_name + ' (₹' + acc.base_price + '/night)'"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in</label>
                                    <input type="date" x-model="booking.check_in_date" @change="calculateRate()" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out</label>
                                    <input type="date" x-model="booking.check_out_date" @change="calculateRate()" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                </div>
                            </div>

                            <!-- Nights Display -->
                            <div x-show="booking.check_in_date && booking.check_out_date" class="p-4 rounded-xl" :class="isPastDate() ? 'bg-red-50' : 'bg-emerald-50'">
                                <p class="text-sm" :class="isPastDate() ? 'text-red-700' : 'text-emerald-700'">
                                    <span class="font-medium" x-text="calculateNights() + ' night' + (calculateNights() > 1 ? 's' : '')"></span>
                                    <span x-show="isPastDate()" class="ml-2 text-red-600">⚠️ Past date selected</span>
                                </p>
                            </div>

                            <!-- Guests -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Adults</label>
                                    <input type="number" x-model="booking.adults" min="1" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Children</label>
                                    <input type="number" x-model="booking.children" min="0" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                </div>
                            </div>

                            <!-- Customer -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-semibold text-gray-700">Customer</label>
                                    <div class="flex bg-gray-100 rounded-lg p-1">
                                        <button type="button" @click="createNewGuest = false" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="!createNewGuest ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                            Select
                                        </button>
                                        <button type="button" @click="createNewGuest = true" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="createNewGuest ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                            Create
                                        </button>
                                    </div>
                                </div>

                                <div x-show="createNewGuest">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-xl">
                                        <input type="text" x-model="booking.guest_name" placeholder="Full Name" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                        <input type="tel" x-model="booking.guest_mobile" @input="checkExistingGuest()" placeholder="Mobile Number" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                        <input type="email" x-model="booking.guest_email" placeholder="Email (Optional)" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                    </div>
                                </div>

                                <div x-show="!createNewGuest">
                                    <select x-model="booking.guest_id" @change="selectGuest()" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                        <option value="">Select Customer</option>
                                        <template x-for="guest in guests" :key="guest.id">
                                            <option :value="guest.id" x-text="guest.name + ' (' + guest.mobile_number + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- B2B Partner (Full Mode) -->
                            <div x-show="bookingMode === 'full'" class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">B2B Partner (Optional)</label>
                                    <div class="flex bg-gray-100 rounded-lg p-1">
                                        <button type="button" @click="createNewPartner = false" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="!createNewPartner ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                            Select
                                        </button>
                                        <button type="button" @click="createNewPartner = true" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="createNewPartner ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                            Create
                                        </button>
                                    </div>
                                </div>

                                <div x-show="createNewPartner">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-blue-50 rounded-xl">
                                        <input type="text" x-model="booking.partner_name" placeholder="Partner Name" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                        <input type="tel" x-model="booking.partner_mobile" placeholder="Partner Mobile" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                    </div>
                                </div>

                                <div x-show="!createNewPartner">
                                    <select x-model="booking.b2b_partner_id" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                        <option value="">No B2B Partner</option>
                                        <template x-for="partner in partners" :key="partner.id">
                                            <option :value="partner.id" x-text="partner.partner_name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Pricing -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                                    <input type="number" x-model="booking.total_amount" step="0.01" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Advance Paid</label>
                                    <input type="number" x-model="booking.advance_paid" step="0.01" class="w-full border border-gray-200 rounded-xl py-3 px-4">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Balance</label>
                                    <input type="number" :value="booking.total_amount - booking.advance_paid" readonly class="w-full border border-gray-200 rounded-xl py-3 px-4 bg-gray-50">
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="flex gap-3 pt-4 border-t">
                                <button type="button" @click="closeBookingModal()" class="flex-1 py-3 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit" class="flex-1 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700">
                                    Create Booking
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bookingManager() {
            return {
                properties: [],
                accommodations: [],
                guests: [],
                partners: [],
                pendingBookings: [],
                activeBookings: [],
                selectedProperty: '',
                showBookingModal: false,
                showCancelModal: false,
                cancelBookingId: null,
                cancelReason: '',
                cancelDescription: '',
                message: '',
                messageType: 'success',
                bookingMode: 'quick',
                createNewGuest: true,
                createNewPartner: false,
                booking: {
                    property_id: '',
                    accommodation_id: '',
                    check_in_date: '',
                    check_out_date: '',
                    adults: 1,
                    children: 0,
                    guest_id: '',
                    guest_name: '',
                    guest_mobile: '',
                    guest_email: '',
                    b2b_partner_id: '',
                    partner_name: '',
                    partner_mobile: '',
                    total_amount: 0,
                    advance_paid: 0
                },

                get totalValue() {
                    return [...this.pendingBookings, ...this.activeBookings].reduce((sum, b) => sum + parseFloat(b.total_amount), 0);
                },

                async init() {
                    await this.loadProperties();
                    await this.loadGuests();
                    await this.loadPartners();
                    await this.loadBookings();
                },

                async loadProperties() {
                    try {
                        const response = await fetch('/api/properties');
                        this.properties = await response.json();
                    } catch (error) {
                        console.error('Error loading properties:', error);
                    }
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

                async loadAccommodations() {
                    if (!this.booking.property_id) {
                        this.accommodations = [];
                        return;
                    }
                    try {
                        const response = await fetch(`/api/properties/${this.booking.property_id}/accommodations`);
                        this.accommodations = await response.json();
                    } catch (error) {
                        console.error('Error loading accommodations:', error);
                    }
                },

                async loadBookings() {
                    try {
                        const url = this.selectedProperty ? `/api/bookings?property_id=${this.selectedProperty}` : '/api/bookings';
                        const response = await fetch(url);
                        const data = await response.json();
                        this.pendingBookings = data.pending || [];
                        this.activeBookings = data.active || [];
                    } catch (error) {
                        console.error('Error loading bookings:', error);
                    }
                },

                openBookingModal() {
                    this.showBookingModal = true;
                    this.resetBookingForm();
                },

                closeBookingModal() {
                    this.showBookingModal = false;
                },

                resetBookingForm() {
                    this.booking = {
                        property_id: this.selectedProperty || '',
                        accommodation_id: '',
                        check_in_date: '',
                        check_out_date: '',
                        adults: 1,
                        children: 0,
                        guest_id: '',
                        guest_name: '',
                        guest_mobile: '',
                        guest_email: '',
                        b2b_partner_id: '',
                        partner_name: '',
                        partner_mobile: '',
                        total_amount: 0,
                        advance_paid: 0
                    };
                    this.createNewGuest = true;
                    this.createNewPartner = false;
                    if (this.booking.property_id) {
                        this.loadAccommodations();
                    }
                },

                checkExistingGuest() {
                    if (this.booking.guest_mobile) {
                        const existingGuest = this.guests.find(g => g.mobile_number === this.booking.guest_mobile);
                        if (existingGuest) {
                            this.booking.guest_name = existingGuest.name;
                            this.booking.guest_email = existingGuest.email;
                        }
                    }
                },

                selectGuest() {
                    const guest = this.guests.find(g => g.id == this.booking.guest_id);
                    if (guest) {
                        this.booking.guest_name = guest.name;
                        this.booking.guest_mobile = guest.mobile_number;
                        this.booking.guest_email = guest.email;
                    }
                },

                calculateNights() {
                    if (this.booking.check_in_date && this.booking.check_out_date) {
                        const checkIn = new Date(this.booking.check_in_date);
                        const checkOut = new Date(this.booking.check_out_date);
                        return Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                    }
                    return 0;
                },

                isPastDate() {
                    if (this.booking.check_in_date) {
                        const checkIn = new Date(this.booking.check_in_date);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        return checkIn < today;
                    }
                    return false;
                },

                calculateRate() {
                    if (this.booking.accommodation_id && this.booking.check_in_date && this.booking.check_out_date) {
                        const acc = this.accommodations.find(a => a.id == this.booking.accommodation_id);
                        if (acc) {
                            const nights = this.calculateNights();
                            this.booking.total_amount = acc.base_price * nights;
                        }
                    }
                },

                async saveBooking() {
                    if (!this.booking.property_id || !this.booking.accommodation_id || !this.booking.guest_name || !this.booking.guest_mobile) {
                        this.showMessage('Please fill all required fields', 'error');
                        return;
                    }

                    try {
                        const response = await fetch('/api/bookings', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.booking)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            this.showMessage('Booking created successfully!', 'success');
                            this.closeBookingModal();
                            await this.loadBookings();
                        } else {
                            this.showMessage(result.message || 'Error creating booking', 'error');
                        }
                    } catch (error) {
                        console.error('Booking error:', error);
                        this.showMessage('Error creating booking', 'error');
                    }
                },

                async toggleBookingStatus(bookingId) {
                    try {
                        const response = await fetch(`/api/bookings/${bookingId}/toggle-status`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            this.showMessage('Booking status updated', 'success');
                            await this.loadBookings();
                        } else {
                            this.showMessage(result.message || 'Error updating booking', 'error');
                        }
                    } catch (error) {
                        this.showMessage('Error updating booking', 'error');
                    }
                },

                openCancelModal(bookingId) {
                    this.cancelBookingId = bookingId;
                    this.showCancelModal = true;
                    this.cancelReason = '';
                    this.cancelDescription = '';
                },

                closeCancelModal() {
                    this.showCancelModal = false;
                    this.cancelBookingId = null;
                },

                async cancelBooking() {
                    if (!this.cancelReason) {
                        this.showMessage('Please select a reason', 'error');
                        return;
                    }

                    try {
                        const response = await fetch(`/api/bookings/${this.cancelBookingId}/cancel`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                reason: this.cancelReason,
                                description: this.cancelDescription
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            this.showMessage('Booking cancelled successfully', 'success');
                            this.closeCancelModal();
                            await this.loadBookings();
                        } else {
                            this.showMessage(result.message || 'Error cancelling booking', 'error');
                        }
                    } catch (error) {
                        this.showMessage('Error cancelling booking', 'error');
                    }
                },

                showMessage(msg, type = 'success') {
                    this.message = msg;
                    this.messageType = type;
                    setTimeout(() => {
                        this.message = '';
                    }, 5000);
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('en-IN').format(num);
                },

                formatDateRange(checkIn, checkOut) {
                    const options = { month: 'short', day: 'numeric' };
                    const start = new Date(checkIn).toLocaleDateString('en-US', options);
                    const end = new Date(checkOut).toLocaleDateString('en-US', { ...options, year: 'numeric' });
                    return `${start} - ${end}`;
                }
            }
        }
    </script>
@endsection