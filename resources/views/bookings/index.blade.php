<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bookings - Hospitality Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .booking-card {
            background: white;
            transition: all 0.3s ease;
        }
        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-confirmed { background: #d1fae5; color: #059669; }
        .status-checkedin { background: #dbeafe; color: #2563eb; }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.sidebar')
    
    <div x-data="bookingManager()" x-init="init()" class="lg:ml-72">
        <!-- Header -->
        <header class="gradient-bg text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
            <div class="relative px-4 py-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <button @click="$dispatch('toggle-sidebar')" class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition-all lg:hidden">
                            <i class="fas fa-bars text-white"></i>
                        </button>
                        <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">Bookings</h1>
                            <p class="text-sm opacity-90">Manage your reservations</p>
                        </div>
                    </div>
                    <button @click="openBookingModal()" class="glassmorphism rounded-xl px-4 py-2">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="font-medium">New</span>
                    </button>
                </div>

                <!-- Filter Tabs -->
                <div class="flex space-x-2 mb-4">
                    <button @click="activeFilter = 'all'" 
                            :class="activeFilter === 'all' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        All
                    </button>
                    <button @click="activeFilter = 'pending'" 
                            :class="activeFilter === 'pending' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Pending
                    </button>
                    <button @click="activeFilter = 'active'" 
                            :class="activeFilter === 'active' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Active
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="glassmorphism rounded-2xl p-4">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold" x-text="pendingBookings.length"></div>
                            <div class="text-xs opacity-75">Pending</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="activeBookings.filter(b => b.status === 'confirmed').length"></div>
                            <div class="text-xs opacity-75">Confirmed</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="activeBookings.filter(b => b.status === 'checked_in').length"></div>
                            <div class="text-xs opacity-75">Checked In</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div x-show="message" x-transition class="mx-4 mt-4 p-4 rounded-xl" :class="messageType === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'">
            <span x-text="message"></span>
        </div>

        <!-- Content -->
        <div class="px-4 py-6 pb-32 space-y-4">
            <!-- Property Filter -->
            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <select x-model="selectedProperty" @change="loadBookings()" 
                        class="w-full bg-transparent border-none text-gray-800 font-medium focus:ring-0">
                    <option value="">All Properties</option>
                    <template x-for="property in properties" :key="property.id">
                        <option :value="property.id" x-text="property.name"></option>
                    </template>
                </select>
            </div>

            <!-- Bookings List -->
            <template x-for="booking in filteredBookings" :key="booking.id">
                <div class="booking-card rounded-2xl p-4 shadow-sm">
                    <!-- Booking Header -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                <span x-text="booking.guest.name.charAt(0).toUpperCase()"></span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800" x-text="booking.guest.name"></h3>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs px-2 py-1 rounded-full font-medium"
                                          :class="booking.status === 'pending' ? 'status-pending' : 
                                                 booking.status === 'confirmed' ? 'status-confirmed' : 
                                                 'status-checkedin'"
                                          x-text="booking.status.charAt(0).toUpperCase() + booking.status.slice(1)"></span>
                                    <span x-show="booking.b2b_partner" 
                                          class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded-full font-medium">
                                        B2B
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-800" x-text="'₹' + formatNumber(booking.total_amount)"></div>
                            <div x-show="booking.balance_pending > 0" 
                                 class="text-xs text-orange-600 font-medium" 
                                 x-text="'₹' + formatNumber(booking.balance_pending) + ' pending'"></div>
                        </div>
                    </div>

                    <!-- Property Details -->
                    <div class="bg-gray-50 rounded-xl p-3 mb-3">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-map-marker-alt text-gray-500 text-sm"></i>
                            <span class="text-sm font-medium text-gray-700" 
                                  x-text="booking.accommodation.property.name + ' - ' + booking.accommodation.display_name"></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-calendar-alt text-gray-500 text-sm"></i>
                            <span class="text-sm text-gray-600" 
                                  x-text="formatDateRange(booking.check_in_date, booking.check_out_date)"></span>
                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full" 
                                  x-text="calculateNights(booking.check_in_date, booking.check_out_date) + ' nights'"></span>
                        </div>
                    </div>

                    <!-- Guest Info -->
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <span x-text="booking.adults + ' adults' + (booking.children > 0 ? ', ' + booking.children + ' children' : '')"></span>
                        <span x-text="booking.guest.mobile_number"></span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <template x-if="booking.status === 'pending'">
                            <div class="flex space-x-2 w-full">
                                <button @click="toggleBookingStatus(booking.id)" 
                                        class="flex-1 bg-green-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-green-600 transition">
                                    <i class="fas fa-check mr-1"></i>
                                    Confirm
                                </button>
                                <button @click="openCancelModal(booking.id)" 
                                        class="flex-1 bg-red-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-red-600 transition">
                                    <i class="fas fa-times mr-1"></i>
                                    Cancel
                                </button>
                            </div>
                        </template>
                        <template x-if="booking.status !== 'pending'">
                            <div class="flex space-x-2 w-full">
                                <button class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-blue-600 transition">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </button>
                                <button @click="openCancelModal(booking.id)" 
                                        class="bg-red-100 text-red-600 py-2 px-4 rounded-xl font-medium text-sm hover:bg-red-200 transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="filteredBookings.length === 0">
                <div class="text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Bookings Found</h3>
                    <p class="text-gray-500 text-sm mb-4">Start by creating your first booking.</p>
                    <button @click="openBookingModal()" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Create Booking
                    </button>
                </div>
            </template>
        </div>

        <!-- Booking Modal -->
        <div x-show="showBookingModal" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-black/40">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl max-h-[95vh] flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 pb-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-t-2xl border-b border-emerald-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-plus text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">New Booking</h3>
                                <p class="text-sm text-emerald-600 font-medium">Create a new reservation</p>
                            </div>
                        </div>
                        <button @click="closeBookingModal()" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl">
                            <i class="fas fa-times text-xl"></i>
                        </button>
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

    </div>
    
    @include('partials.bottom-bar')

    <script>
        function bookingManager() {
            return {
                properties: [],
                accommodations: [],
                guests: [],
                pendingBookings: [],
                activeBookings: [],
                selectedProperty: '',
                activeFilter: 'all',
                showBookingModal: false,
                showCancelModal: false,
                cancelBookingId: null,
                cancelReason: '',
                cancelDescription: '',
                message: '',
                messageType: 'success',
                createNewGuest: true,
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
                    total_amount: 0,
                    advance_paid: 0
                },

                get filteredBookings() {
                    const allBookings = [...this.pendingBookings, ...this.activeBookings];
                    if (this.activeFilter === 'pending') return this.pendingBookings;
                    if (this.activeFilter === 'active') return this.activeBookings;
                    return allBookings;
                },

                async init() {
                    await this.loadProperties();
                    await this.loadGuests();
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
                        total_amount: 0,
                        advance_paid: 0
                    };
                    this.createNewGuest = true;
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

                calculateNights(checkIn, checkOut) {
                    if (checkIn && checkOut) {
                        const start = new Date(checkIn);
                        const end = new Date(checkOut);
                        const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                        return diff > 0 ? diff : 0;
                    }
                    return 0;
                },

                calculateRate() {
                    if (this.booking.accommodation_id && this.booking.check_in_date && this.booking.check_out_date) {
                        const acc = this.accommodations.find(a => a.id == this.booking.accommodation_id);
                        if (acc) {
                            const nights = this.calculateNights(this.booking.check_in_date, this.booking.check_out_date);
                            this.booking.total_amount = acc.base_price * nights;
                        }
                    }
                },

                async saveBooking() {
                    if (!this.booking.property_id || !this.booking.accommodation_id || !this.booking.guest_name || !this.booking.guest_mobile || !this.booking.check_in_date || !this.booking.check_out_date) {
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
                        
                        if (response.ok && result.success) {
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
                        this.showMessage('Please select a reason for cancellation.', 'error');
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
                    if (num === null || num === undefined) return '0';
                    return new Intl.NumberFormat('en-IN').format(num);
                },

                formatDateRange(checkIn, checkOut) {
                    const options = { month: 'short', day: 'numeric' };
                    const start = new Date(checkIn).toLocaleDateString('en-GB', options);
                    const end = new Date(checkOut).toLocaleDateString('en-GB', options);
                    return `${start} - ${end}`;
                }
            }
        }
    </script>
</body>
</html>