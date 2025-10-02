<!-- Check-in Details Section -->
<div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
    <!-- Header -->
    <div class="flex items-center gap-2 mb-6">
        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0v1a1 1 0 01-1 1H6a1 1 0 01-1-1V7a1 1 0 011-1h1m0 0V3a1 1 0 011-1h2a1 1 0 011 1v4H9z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">Check-in Details</h3>
            <p class="text-sm text-gray-600">Select dates and guest information</p>
        </div>
    </div>
    
    <!-- Date and Guest Grid -->
    <div class="space-y-4 sm:space-y-0 mb-6">
        <!-- Check-in and Check-out Dates -->
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-3 sm:gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0v1a1 1 0 01-1 1H6a1 1 0 01-1-1V7a1 1 0 011-1h1m0 0V3a1 1 0 011-1h2a1 1 0 011 1v4H9z"></path>
                    </svg>
                    <span class="hidden sm:inline">Check-in Date</span>
                    <span class="sm:hidden">Check-in</span>
                </label>
                <input type="text" name="check_in_date" x-model="checkInDate" 
                       @change="updateCheckOutDate(); checkPastBooking(); calculateAmount()" 
                       @input="updateCheckOutDate(); checkPastBooking(); calculateAmount()"
                       onchange="if(typeof updateCheckoutToNextDay === 'function') updateCheckoutToNextDay(this.value)"
                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800 datepicker-input text-sm sm:text-base" 
                       placeholder="In" readonly required>
                @error('check_in_date')
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
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0v1a1 1 0 01-1 1H6a1 1 0 01-1-1V7a1 1 0 011-1h1m0 0V3a1 1 0 011-1h2a1 1 0 011 1v4H9z"></path>
                    </svg>
                    <span class="hidden sm:inline">Check-out Date</span>
                    <span class="sm:hidden">Check-out</span>
                </label>
                <input type="text" name="check_out_date" x-model="checkOutDate" 
                       @change="calculateDaysNights()" 
                       @input="calculateDaysNights()"
                       onchange="if(typeof updateDaysFromCheckout === 'function') updateDaysFromCheckout(this.value)"
                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800 datepicker-input text-sm sm:text-base" 
                       placeholder="Out" readonly required>
                @error('check_out_date')
                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Past booking warning -->
        <div x-show="isPastBooking" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="p-3 bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-sm font-semibold text-yellow-800">You're recording a past booking</span>
            </div>
        </div>
        
        <!-- Guest Count -->
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-3 sm:gap-5 mt-4 sm:mt-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Adults
                </label>
                <input type="number" name="adults" x-model="adults" @change="calculateTotalGuests(); calculateAmount()" value="{{ old('adults', 1) }}" min="1" 
                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800 text-sm sm:text-base" 
                       placeholder="Number of adults" required>
                @error('adults')
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
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15M9 10V9a2 2 0 012-2h2a2 2 0 012 2v1M9 10v5a2 2 0 002 2h2a2 2 0 002-2v-5"></path>
                    </svg>
                    Children
                </label>
                <input type="number" name="children" x-model="children" @change="calculateTotalGuests(); calculateAmount()" value="{{ old('children', 0) }}" min="0" 
                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800 text-sm sm:text-base" 
                       placeholder="Number of children" required>
                @error('children')
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
    
    <!-- Booking Type -->
    <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Booking Type
        </label>
        <div class="flex bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl p-1 shadow-inner">
            <button type="button" 
                    @click="bookingType = 'per_day'; calculateAmount()" 
                    :class="bookingType === 'per_day' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="bookingType === 'per_day' ? 'text-purple-600' : 'text-gray-600'">Per Day</span>
            </button>
            <button type="button" 
                    @click="bookingType = 'per_person'; calculateAmount()" 
                    :class="bookingType === 'per_person' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="bookingType === 'per_person' ? 'text-purple-600' : 'text-gray-600'">Per Person</span>
            </button>
        </div>
        <input type="hidden" name="booking_type" x-model="bookingType">
    </div>
    
    <!-- Per Person Price (shown when per_person is selected) -->
    <div x-show="bookingType === 'per_person'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            Price Per Person (₹)
        </label>
        <input type="number" x-model="perPersonPrice" @input="calculateAmount()" 
               step="0.01" min="0"
               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
               placeholder="Enter price per person">
        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Default: ₹1,000 per person per day
        </p>
    </div>
    
    <!-- Days and Nights Display -->
    @include('partials.bookings.days-nights-display')
    
    <!-- Special Requests -->
    <div class="mt-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
            Special Requests <span class="text-gray-500 font-normal">(Optional)</span>
        </label>
        <textarea name="special_requests" rows="4" 
                  placeholder="Any special requests or notes for this booking..." 
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800 resize-none">{{ old('special_requests') }}</textarea>
        @error('special_requests')
            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ $message }}
            </p>
        @enderror
    </div>
</div>
