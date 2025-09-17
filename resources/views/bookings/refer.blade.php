.<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bookings - Hospitality Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    @include('partials.sidebar')
    
    <!-- Main Content -->
    <div class="lg:ml-72 pb-20 lg:pb-6">
        <!-- Mobile Header -->
        <div class="lg:hidden bg-gradient-to-r from-purple-600 to-blue-600 text-white p-4 mb-6 rounded-2xl mx-4 mt-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button @click="$dispatch('toggle-sidebar')" class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition-all">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold">Bookings</h1>
                        <p class="text-sm opacity-90">Manage your reservations</p>
                    </div>
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-white"></i>
                </div>
            </div>
        </div>
    {{-- We'll wrap the entire component in a container with a light background for better contrast --}}
    <div class="bg-gray-50/50 min-h-screen p-4 sm:p-6">
        <div id="booking-management" x-data="bookingManager()" x-init="init()">
            <div x-show="message" x-transition class="mb-4 p-4 rounded-xl" :class="messageType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'">
                <span x-text="message"></span>
            </div>

            <div class="space-y-4 mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Booking Management</h1>
                    <p class="text-sm text-gray-500">Manage all your property bookings from one place.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1 relative">
                        <select x-model="selectedProperty" @change="loadBookings()" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-gray-700 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                            <option value="">All Properties</option>
                            <template x-for="property in properties" :key="property.id">
                                <option :value="property.id" x-text="property.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <button @click="openBookingModal()" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold rounded-xl px-5 py-2.5 hover:bg-blue-700 transition shadow-sm hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        New Booking
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-amber-600">Pending Bookings (<span x-text="pendingBookings.length"></span>)</h3>
                    </div>
                    
                    <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                        <template x-for="booking in pendingBookings" :key="booking.id">
                            <div class="p-4 hover:bg-gray-50/80 transition-colors">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="font-semibold text-gray-800" x-text="booking.guest.name"></h4>
                                            <span x-show="booking.b2b_partner" class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">B2B</span>
                                        </div>
                                        <div class="text-sm text-gray-500 space-y-1.5">
                                            <div class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-1.032 0 1 1 0 00-.463.882V5.5a1 1 0 001 1h4.5a1 1 0 00.882-.463 1 1 0 000-1.032l-4.5-4.5zM8.5 6a1 1 0 00-1 1v4.5a1 1 0 00.463.882l4.5 4.5a1 1 0 001.032 0 1 1 0 00.463-.882V14.5a1 1 0 00-1-1h-4.5a1 1 0 00-.882.463 1 1 0 000 1.032l4.5 4.5a1 1 0 001.032 0 1 1 0 00.463-.882V19.5a1 1 0 00-1-1h-4.5a1 1 0 00-.882.463 1 1 0 000 1.032l4.5 4.5a1 1 0 001.032 0 1 1 0 00.463-.882V24.5a1 1 0 00-1-1h-4.5a1 1 0 00-1 1v4.5a1 1 0 00.882.463 1 1 0 001.032 0l4.5-4.5a1 1 0 000-1.032V25.5a1 1 0 00-1-1h-4.5a1 1 0 00-1 1v4.5a1 1 0 00.882.463 1 1 0 001.032 0l4.5-4.5a1 1 0 000-1.032V11.5a1 1 0 00-1-1h-4.5a1 1 0 00-1 1v4.5a1 1 0 00.882.463 1 1 0 001.032 0l4.5-4.5a1 1 0 000-1.032V6.5a1 1 0 00-1-1h-4.5a1 1 0 00-1 1z" clip-rule="evenodd" /><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l3 3a1 1 0 001.414-1.414L11 8.586V5z" clip-rule="evenodd" /></svg>
                                                <span x-text="booking.accommodation.property.name + ' - ' + booking.accommodation.display_name"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>
                                                <span x-text="formatDateRange(booking.check_in_date, booking.check_out_date)"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right space-y-2">
                                        <div class="text-lg font-bold text-gray-800" x-text="'₹' + formatNumber(booking.total_amount)"></div>
                                        <div class="flex flex-col items-end gap-2 sm:flex-row sm:gap-1">
                                            <button @click="toggleBookingStatus(booking.id)" class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
                                                Activate
                                            </button>
                                            <button @click="openCancelModal(booking.id)" class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="pendingBookings.length === 0" class="p-8 text-center text-gray-400">
                            <div class="text-sm">No pending bookings found</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-green-600">Active Bookings (<span x-text="activeBookings.length"></span>)</h3>
                    </div>
                    
                    <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                        <template x-for="booking in activeBookings" :key="booking.id">
                            <div class="p-4 hover:bg-gray-50/80 transition-colors">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="font-semibold text-gray-800" x-text="booking.guest.name"></h4>
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full" :class="booking.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-sky-100 text-sky-800'" x-text="booking.status.charAt(0).toUpperCase() + booking.status.slice(1)"></span>
                                            <span x-show="booking.b2b_partner" class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">B2B</span>
                                        </div>
                                        <div class="text-sm text-gray-500 space-y-1.5">
                                            <div class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
                                                <span x-text="booking.accommodation.property.name + ' - ' + booking.accommodation.display_name"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>
                                                <span x-text="formatDateRange(booking.check_in_date, booking.check_out_date)"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right space-y-2">
                                        <div class="text-lg font-bold text-gray-800" x-text="'₹' + formatNumber(booking.total_amount)"></div>
                                        <div x-show="booking.balance_pending > 0" class="text-xs font-semibold text-orange-600" x-text="'₹' + formatNumber(booking.balance_pending) + ' pending'"></div>
                                        <button @click="openCancelModal(booking.id)" class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="activeBookings.length === 0" class="p-8 text-center text-gray-400">
                            <div class="text-sm">No active bookings found</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
                    <div class="text-3xl font-extrabold text-amber-500" x-text="pendingBookings.length"></div>
                    <div class="text-sm text-gray-500 mt-1">Pending</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
                    <div class="text-3xl font-extrabold text-green-500" x-text="activeBookings.filter(b => b.status === 'confirmed').length"></div>
                    <div class="text-sm text-gray-500 mt-1">Confirmed</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
                    <div class="text-3xl font-extrabold text-sky-500" x-text="activeBookings.filter(b => b.status === 'checked_in').length"></div>
                    <div class="text-sm text-gray-500 mt-1">Checked In</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-gray-800" x-text="'₹' + formatNumber(totalValue)"></div>
                    <div class="text-sm text-gray-500 mt-1">Total Value</div>
                </div>
            </div>

            <div x-show="showCancelModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/40" style="display: none;">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div @click.away="closeCancelModal()" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" x-show="showCancelModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Cancel Booking</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Reason for Cancellation</label>
                                <select x-model="cancelReason" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-300">
                                    <option value="">Select a reason</option>
                                    <option value="Guest Request">Guest Request</option>
                                    <option value="No Show">No Show</option>
                                    <option value="Payment Issue">Payment Issue</option>
                                    <option value="Property Issue">Property Issue</option>
                                    <option value="Overbooking">Overbooking</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Notes (Optional)</label>
                                <textarea x-model="cancelDescription" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-300" placeholder="Add any extra details..."></textarea>
                            </div>
                        </div>
                        
                        <div class="flex gap-3 mt-6">
                            <button @click="closeCancelModal()" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                                Keep Booking
                            </button>
                            <button @click="cancelBooking()" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Confirm Cancellation
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="showBookingModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/40" style="display: none;">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div @click.away="closeBookingModal()" class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl max-h-[95vh] flex flex-col" x-show="showBookingModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                        <div class="flex items-center justify-between p-5 border-b border-gray-100">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">New Booking</h3>
                                <p class="text-sm text-gray-500">Create a new reservation</p>
                            </div>
                            <button @click="closeBookingModal()" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto p-6">
                            <form @submit.prevent="saveBooking()" class="space-y-6">
                                <div class="p-1.5 bg-gray-100 rounded-xl flex">
                                    <button type="button" @click="bookingMode = 'quick'" class="w-full py-2 text-sm font-semibold rounded-lg transition-all" :class="bookingMode === 'quick' ? 'bg-white shadow text-blue-600' : 'text-gray-600'">
                                        Quick Entry
                                    </button>
                                    <button type="button" @click="bookingMode = 'full'" class="w-full py-2 text-sm font-semibold rounded-lg transition-all" :class="bookingMode === 'full' ? 'bg-white shadow text-blue-600' : 'text-gray-600'">
                                        Full Details
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Property</label>
                                        <select x-model="booking.property_id" @change="loadAccommodations()" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                            <option value="">Select Property</option>
                                            <template x-for="property in properties" :key="property.id">
                                                <option :value="property.id" x-text="property.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Accommodation</label>
                                        <select x-model="booking.accommodation_id" @change="calculateRate()" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                            <option value="">Select Accommodation</option>
                                            <template x-for="acc in accommodations" :key="acc.id">
                                                <option :value="acc.id" x-text="acc.display_name + ' (₹' + acc.base_price + '/night)'"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Check-in</label>
                                        <input type="date" x-model="booking.check_in_date" @change="calculateRate()" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Check-out</label>
                                        <input type="date" x-model="booking.check_out_date" @change="calculateRate()" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                    </div>
                                </div>

                                <div x-show="booking.check_in_date && booking.check_out_date && calculateNights() > 0" class="p-3 rounded-lg" :class="isPastDate() ? 'bg-red-50' : 'bg-blue-50'">
                                    <p class="text-sm font-medium" :class="isPastDate() ? 'text-red-700' : 'text-blue-700'">
                                        Total duration: <span class="font-bold" x-text="calculateNights() + ' night' + (calculateNights() > 1 ? 's' : '')"></span>
                                        <span x-show="isPastDate()" class="ml-2 font-semibold"> (Past Date)</span>
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Adults</label>
                                        <input type="number" x-model="booking.adults" min="1" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Children</label>
                                        <input type="number" x-model="booking.children" min="0" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 pt-6 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-semibold text-gray-800">Customer Details</label>
                                        <div class="flex bg-gray-100 rounded-lg p-1">
                                            <button type="button" @click="createNewGuest = false" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="!createNewGuest ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">Select</button>
                                            <button type="button" @click="createNewGuest = true" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="createNewGuest ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">Create New</button>
                                        </div>
                                    </div>

                                    <div x-show="createNewGuest">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <input type="text" x-model="booking.guest_name" placeholder="Full Name" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                            <input type="tel" x-model="booking.guest_mobile" @input="checkExistingGuest()" placeholder="Mobile Number" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                            <input type="email" x-model="booking.guest_email" placeholder="Email (Optional)" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                        </div>
                                    </div>

                                    <div x-show="!createNewGuest">
                                        <select x-model="booking.guest_id" @change="selectGuest()" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-300">
                                            <option value="">Select an existing customer</option>
                                            <template x-for="guest in guests" :key="guest.id">
                                                <option :value="guest.id" x-text="guest.name + ' (' + guest.mobile_number + ')'"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <div x-show="bookingMode === 'full'" class="border-t border-gray-100 pt-6 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-semibold text-gray-800">B2B Partner (Optional)</label>
                                        <div class="flex bg-gray-100 rounded-lg p-1">
                                            <button type="button" @click="createNewPartner = false" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="!createNewPartner ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">Select</button>
                                            <button type="button" @click="createNewPartner = true" class="px-3 py-1 text-xs font-medium rounded-md transition-colors" :class="createNewPartner ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">Create New</button>
                                        </div>
                                    </div>

                                    <div x-show="createNewPartner">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input type="text" x-model="booking.partner_name" placeholder="Partner Company Name" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4">
                                            <input type="tel" x-model="booking.partner_mobile" placeholder="Partner Mobile" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4">
                                        </div>
                                    </div>

                                    <div x-show="!createNewPartner">
                                        <select x-model="booking.b2b_partner_id" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4">
                                            <option value="">No B2B Partner</option>
                                            <template x-for="partner in partners" :key="partner.id">
                                                <option :value="partner.id" x-text="partner.partner_name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 pt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Total Amount</label>
                                        <input type="number" x-model="booking.total_amount" step="0.01" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Advance Paid</label>
                                        <input type="number" x-model="booking.advance_paid" step="0.01" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Balance Due</label>
                                        <input type="number" :value="booking.total_amount - booking.advance_paid" readonly class="w-full border-gray-200 rounded-lg py-2.5 px-4 bg-gray-100 text-gray-500 font-semibold">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="flex gap-3 p-4 bg-gray-50/70 border-t border-gray-100 rounded-b-2xl">
                            <button type="button" @click="closeBookingModal()" class="flex-1 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-100 transition">
                                Cancel
                            </button>
                            <button type="button" @click="saveBooking()" class="flex-1 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                                Create Booking
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Your existing Javascript function remains unchanged.
        // I've included it here so you have the complete file.
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
                        const diff = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                        return diff > 0 ? diff : 0;
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
                    if (!this.booking.property_id || !this.booking.accommodation_id || !this.booking.guest_name || !this.booking.guest_mobile || !this.booking.check_in_date || !this.booking.check_out_date) {
                        this.showMessage('Please fill all required fields', 'error');
                        return;
                    }

                    if(this.calculateNights() <= 0) {
                        this.showMessage('Check-out date must be after check-in date.', 'error');
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
    </div>
    
    @include('partials.bottom-bar')
</body>
</html>