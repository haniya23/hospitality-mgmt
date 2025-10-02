<!-- Amount Calculation Section -->
<div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
    <!-- Header -->
    <div class="flex items-center gap-2 mb-6">
        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Amount Calculation</h3>
            <p class="text-sm text-gray-600">Pricing and payment details</p>
        </div>
    </div>
    
    <!-- Default Amount Display -->
    <div class="mb-6 p-4 sm:p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700" x-text="bookingType === 'per_day' ? 'Default Amount (Base Price × Days)' : 'Default Amount (Per Person Price × Guests × Days)'"></span>
            </div>
            <span class="text-2xl font-bold text-gray-900" x-text="'₹' + defaultAmount.toLocaleString()"></span>
        </div>
        <div class="text-xs text-gray-600 bg-white rounded-lg p-2 border border-gray-200">
            <span x-show="bookingType === 'per_day'" x-text="'₹' + (selectedAccommodationPrice || 0).toLocaleString() + ' × ' + days + ' days'"></span>
            <span x-show="bookingType === 'per_person'" x-text="'₹' + perPersonPrice.toLocaleString() + ' × ' + totalGuests + ' guests × ' + days + ' days'"></span>
        </div>
    </div>
    
    <!-- Amount Input Fields -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Set Amount
            </label>
            <input type="number" name="total_amount" x-model="totalAmount" @input="updateBalance()" step="0.01" min="0" 
                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800" 
                   placeholder="Enter total amount" required>
            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Shows calculated amount, can be overridden
            </p>
            @error('total_amount')
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
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                </svg>
                Advance Paid
            </label>
            <input type="number" name="advance_paid" x-model="advancePaid" @input="updateBalance()" step="0.01" min="0" 
                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800" 
                   placeholder="Enter advance amount" required>
            @error('advance_paid')
                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>
    
    <!-- Balance Calculation -->
    <div x-show="totalAmount > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="p-4 sm:p-5 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700">Balance Pending</span>
            </div>
            <span class="text-2xl font-bold text-green-600" x-text="'₹' + balancePending.toLocaleString()"></span>
        </div>
    </div>
</div>
