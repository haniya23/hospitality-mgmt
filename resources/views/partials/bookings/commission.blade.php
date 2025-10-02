<!-- Commission Section -->
<div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-6">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">Commission</h3>
        </div>
        
        @if($hasB2bPartners)
        <div class="flex bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl p-1 shadow-inner w-full sm:w-auto">
            <button type="button" 
                    @click="isB2B = false" 
                    :class="!isB2B ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                    class="flex-1 sm:flex-initial px-4 py-2.5 sm:py-2 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="!isB2B ? 'text-purple-600' : 'text-gray-600'">Direct</span>
            </button>
            <button type="button" 
                    @click="isB2B = true; calculateCommission()" 
                    :class="isB2B ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                    class="flex-1 sm:flex-initial px-4 py-2.5 sm:py-2 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                <span :class="isB2B ? 'text-purple-600' : 'text-gray-600'">B2B</span>
            </button>
        </div>
        @else
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 p-3 sm:p-0 bg-blue-50 sm:bg-transparent rounded-lg sm:rounded-none border border-blue-100 sm:border-0">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm text-gray-700 font-medium">Direct booking only</span>
            </div>
            <a href="{{ route('b2b.create') }}" 
               class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 font-semibold group">
                <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add B2B Partner
            </a>
        </div>
        @endif
    </div>
    
    <div x-show="isB2B && {{ $hasB2bPartners ? 'true' : 'false' }}" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="space-y-5">
        
        <!-- Partner Search -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                B2B Partner
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       x-model="partnerSearch" 
                       @input="searchPartners()" 
                       placeholder="Search partners..." 
                       class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300">
                
                <div x-show="filteredPartners.length > 0" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute z-10 w-full bg-white border-2 border-purple-200 rounded-xl mt-2 max-h-60 overflow-y-auto shadow-xl">
                    <template x-for="partner in filteredPartners" :key="partner.id">
                        <div @click="selectPartner(partner)" 
                             class="px-4 py-3 hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-all duration-150 group">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 group-hover:text-purple-600 transition-colors" x-text="partner.partner_name"></div>
                                    <div class="flex items-center gap-1 mt-1">
                                        <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600" x-text="partner.commission_rate + '% commission'"></span>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <input type="hidden" name="b2b_partner_id" x-model="selectedPartner">
        </div>
        
        <!-- Commission Type & Value -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Commission Type
                </label>
                <div class="flex bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl p-1 shadow-inner">
                    <button type="button" 
                            @click="commissionType = 'percentage'; calculateCommission()" 
                            :class="commissionType === 'percentage' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                            class="flex-1 px-3 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                        <span :class="commissionType === 'percentage' ? 'text-purple-600' : 'text-gray-600'">Percentage</span>
                    </button>
                    <button type="button" 
                            @click="commissionType = 'amount'; calculateCommission()" 
                            :class="commissionType === 'amount' ? 'bg-white shadow-md scale-105' : 'hover:bg-white/50'" 
                            class="flex-1 px-3 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 active:scale-95">
                        <span :class="commissionType === 'amount' ? 'text-purple-600' : 'text-gray-600'">Amount</span>
                    </button>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    Commission <span class="text-purple-600 font-bold" x-text="commissionType === 'percentage' ? '(%)' : '(₹)'"></span>
                </label>
                <div class="relative">
                    <input type="number" 
                           x-model="commissionValue" 
                           @input="calculateCommission()" 
                           :step="commissionType === 'percentage' ? '0.01' : '1'"
                           :min="commissionType === 'percentage' ? '0' : '0'"
                           :max="commissionType === 'percentage' ? '100' : ''"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                           placeholder="Enter value">
                </div>
            </div>
        </div>
        
        <!-- Commission Calculation Display -->
        <div x-show="commissionAmount > 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="p-4 sm:p-5 bg-gradient-to-br from-orange-50 to-red-50 rounded-xl border-2 border-orange-200 shadow-sm">
            
            <!-- Mobile Layout -->
            <div class="sm:hidden space-y-3">
                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Commission</span>
                    </div>
                    <span class="text-lg font-bold text-orange-600" x-text="'₹' + commissionAmount.toLocaleString()"></span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Net Amount</span>
                    </div>
                    <span class="text-lg font-bold text-green-600" x-text="'₹' + netAmount.toLocaleString()"></span>
                </div>
            </div>
            
            <!-- Desktop Layout -->
            <div class="hidden sm:block space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-orange-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Commission Amount</span>
                    </div>
                    <span class="text-2xl font-bold text-orange-600" x-text="'₹' + commissionAmount.toLocaleString()"></span>
                </div>
                
                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Net Amount (After Commission)</span>
                    </div>
                    <span class="text-2xl font-bold text-green-600" x-text="'₹' + netAmount.toLocaleString()"></span>
                </div>
            </div>
        </div>
        
        <!-- Hidden inputs for form submission -->
        <input type="hidden" name="commission_percentage" x-model="commissionPercentage">
        <input type="hidden" name="commission_amount" x-model="commissionAmount">
    </div>
</div>
