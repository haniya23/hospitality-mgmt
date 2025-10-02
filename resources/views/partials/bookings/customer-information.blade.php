<!-- Customer Information Section -->
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
                    class="flex-1 sm:flex-initial px-3 py-2.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="customerType === 'new' ? 'text-blue-600' : 'text-gray-600'">New</span>
            </button>
            <button type="button" 
                    @click="customerType = 'existing'" 
                    :class="customerType === 'existing' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                    class="flex-1 sm:flex-initial px-3 py-2.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="customerType === 'existing' ? 'text-blue-600' : 'text-gray-600'">Existing</span>
            </button>
            <button type="button" 
                    @click="customerType = 'b2b'" 
                    :class="customerType === 'b2b' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                    class="flex-1 sm:flex-initial px-3 py-2.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="customerType === 'b2b' ? 'text-blue-600' : 'text-gray-600'">B2B</span>
            </button>
            <button type="button" 
                    @click="customerType = 'accommodation'; useAccommodationReservedCustomer = true;" 
                    :class="customerType === 'accommodation' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                    class="flex-1 sm:flex-initial px-3 py-2.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="customerType === 'accommodation' ? 'text-blue-600' : 'text-gray-600'">Reserved</span>
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
    
    <!-- Accommodation Reserved Customer Toggle -->
    <div x-show="customerType === 'accommodation' && selectedAccommodation" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mb-5 p-4 sm:p-5 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-green-800">Accommodation Reserved Customer</h4>
                </div>
                <p class="text-xs text-green-600 leading-relaxed" x-text="useAccommodationReservedCustomer ? 'Automatically selected reserved customer for this accommodation' : 'Using reserved customer for accommodation to block dates'"></p>
            </div>
            <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" 
                       x-model="useAccommodationReservedCustomer" 
                       class="h-5 w-5 text-green-600 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                <span class="text-sm font-semibold text-green-700 group-hover:text-green-800 transition-colors">Use Reserved Customer</span>
            </label>
        </div>
    </div>
    
    <!-- Existing Customer Search -->
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
    
    <!-- Customer Input Fields - Hidden when Reserved Customer is selected -->
    <div x-show="!(customerType === 'b2b' && useB2BReservedCustomer) && !(customerType === 'accommodation' && useAccommodationReservedCustomer)" 
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
                           :required="!(customerType === 'b2b' && useB2BReservedCustomer) && !(customerType === 'accommodation' && useAccommodationReservedCustomer)">
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
                           :required="!(customerType === 'b2b' && useB2BReservedCustomer) && !(customerType === 'accommodation' && useAccommodationReservedCustomer)">
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
        
        <!-- Email Field - Hidden for new guests by default -->
        <div x-show="customerType !== 'new'">
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

    <!-- Accommodation Reserved Customer Info Display -->
    <div x-show="customerType === 'accommodation' && useAccommodationReservedCustomer && selectedAccommodation" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="p-4 sm:p-5 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border-2 border-purple-200 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-bold text-purple-800 mb-1">Using Accommodation Reserved Customer</h4>
                <p class="text-sm font-semibold text-purple-700 mb-2" x-text="selectedAccommodationReservedCustomer || 'Loading reserved customer details...'"></p>
                <div x-show="useAccommodationReservedCustomer && selectedAccommodationReservedCustomer && !selectedAccommodationReservedCustomer.includes('No reserved') && !selectedAccommodationReservedCustomer.includes('Error')" 
                     class="flex items-center gap-2 p-2 bg-white rounded-lg border border-purple-200">
                    <svg class="w-4 h-4 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs text-purple-600 font-medium">This customer is automatically selected for the accommodation</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden inputs for reserved customer flags -->
<input type="hidden" name="use_b2b_reserved_customer" :value="useB2BReservedCustomer ? '1' : '0'">
<input type="hidden" name="use_accommodation_reserved_customer" :value="useAccommodationReservedCustomer ? '1' : '0'">
