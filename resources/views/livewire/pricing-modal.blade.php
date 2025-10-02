<div
    x-data="{ show: @entangle('isOpen'), mode: @entangle('mode') }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto backdrop-blur-sm bg-black/40"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <!-- Mobile & Desktop Overlay -->
    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
        
        <!-- Modal Container -->
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-md sm:max-w-lg lg:max-w-xl xl:max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col"
        >
            <!-- Header with Gradient Background -->
            <div class="flex items-center justify-between p-6 pb-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-2xl border-b border-blue-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight" id="modal-title">
                            <span x-show="mode === 'create'">New Pricing Rule</span>
                            <span x-show="mode === 'edit'">Edit Pricing Rule</span>
                        </h3>
                        <p class="text-sm text-blue-600 font-medium mt-1">
                            <span x-show="mode === 'create'">Create a new pricing rule</span>
                            <span x-show="mode === 'edit'">Update pricing rule</span>
                        </p>
                    </div>
                </div>
                <button 
                    wire:click="close" 
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white/80 rounded-xl transition-all duration-200 shadow-sm"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    
                    <!-- Property & Accommodation -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="relative" x-data="{ open: false }">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Property</label>
                            <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white text-left flex items-center justify-between">
                                <span>
                                    @if($property_id)
                                        @php $selectedProperty = collect($properties)->firstWhere('id', $property_id); @endphp
                                        {{ $selectedProperty['name'] ?? 'Select Property' }}
                                    @else
                                        Select Property
                                    @endif
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto">
                                <button wire:click="$set('property_id', null)" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900">
                                    Select Property
                                </button>
                                @foreach($properties as $property)
                                    <button wire:click="$set('property_id', {{ $property['id'] }})" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $property_id == $property['id'] ? 'bg-blue-50 text-blue-700' : '' }}">
                                        {{ $property['name'] }}
                                    </button>
                                @endforeach
                            </div>
                            @error('property_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="relative" x-data="{ open: false }">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Accommodation (Optional)</label>
                            <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white text-left flex items-center justify-between">
                                <span>
                                    @if($accommodation_id)
                                        @php $selectedAccommodation = collect($accommodations)->firstWhere('id', $accommodation_id); @endphp
                                        {{ $selectedAccommodation['display_name'] ?? 'All Accommodations' }}
                                    @else
                                        All Accommodations
                                    @endif
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto">
                                <button wire:click="$set('accommodation_id', null)" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900">
                                    All Accommodations
                                </button>
                                @foreach($accommodations as $accommodation)
                                    <button wire:click="$set('accommodation_id', {{ $accommodation['id'] }})" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $accommodation_id == $accommodation['id'] ? 'bg-blue-50 text-blue-700' : '' }}">
                                        {{ $accommodation['display_name'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Rule Name & Type -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rule Name</label>
                            <input type="text" wire:model="rule_name" placeholder="e.g., Summer Season 2024" 
                                   class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            @error('rule_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="relative" x-data="{ open: false }">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rule Type</label>
                            <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white text-left flex items-center justify-between">
                                <span>
                                    @switch($rule_type)
                                        @case('seasonal') Seasonal @break
                                        @case('promotional') Promotional @break
                                        @case('b2b_contract') B2B Contract @break
                                        @case('loyalty_discount') Loyalty Discount @break
                                        @default Select Type
                                    @endswitch
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg">
                                <button wire:click="$set('rule_type', 'seasonal')" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $rule_type == 'seasonal' ? 'bg-blue-50 text-blue-700' : '' }}">
                                    Seasonal
                                </button>
                                <button wire:click="$set('rule_type', 'promotional')" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $rule_type == 'promotional' ? 'bg-blue-50 text-blue-700' : '' }}">
                                    Promotional
                                </button>
                                <button wire:click="$set('rule_type', 'b2b_contract')" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $rule_type == 'b2b_contract' ? 'bg-blue-50 text-blue-700' : '' }}">
                                    B2B Contract
                                </button>
                                <button wire:click="$set('rule_type', 'loyalty_discount')" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $rule_type == 'loyalty_discount' ? 'bg-blue-50 text-blue-700' : '' }}">
                                    Loyalty Discount
                                </button>
                            </div>
                            @error('rule_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                            <input type="text" wire:model="start_date" 
                                   class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 datepicker-input"
                                   placeholder="Select start date" readonly>
                            @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                            <input type="text" wire:model="end_date" 
                                   class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 datepicker-input"
                                   placeholder="Select end date" readonly>
                            @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Rate Adjustment -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700">Rate Adjustment</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fixed Amount (₹)</label>
                                <input type="number" wire:model="rate_adjustment" step="0.01" placeholder="e.g., 500 or -200" 
                                       class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <p class="mt-1 text-xs text-gray-500">Positive for increase, negative for decrease</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Percentage (%)</label>
                                <input type="number" wire:model="percentage_adjustment" step="0.01" placeholder="e.g., 20 or -15" 
                                       class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <p class="mt-1 text-xs text-gray-500">Positive for increase, negative for discount</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stay Requirements -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700">Stay Requirements (Optional)</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stay (nights)</label>
                                <input type="number" wire:model="min_stay_nights" min="1" placeholder="e.g., 3" 
                                       class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Stay (nights)</label>
                                <input type="number" wire:model="max_stay_nights" min="1" placeholder="e.g., 14" 
                                       class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>
                    </div>

                    <!-- B2B Partner (for B2B Contract type) -->
                    @if($rule_type === 'b2b_contract')
                        <div class="relative" x-data="{ open: false }">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">B2B Partner</label>
                            <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white text-left flex items-center justify-between">
                                <span>
                                    @if($b2b_partner_id)
                                        @php $selectedPartner = collect($b2bPartners)->firstWhere('id', $b2b_partner_id); @endphp
                                        {{ $selectedPartner['partner_name'] ?? 'Select Partner' }}
                                    @else
                                        Select B2B Partner
                                    @endif
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto">
                                <button wire:click="$set('b2b_partner_id', null)" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900">
                                    Select B2B Partner
                                </button>
                                @foreach($b2bPartners as $partner)
                                    <button wire:click="$set('b2b_partner_id', {{ $partner['id'] }})" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $b2b_partner_id == $partner['id'] ? 'bg-blue-50 text-blue-700' : '' }}">
                                        {{ $partner['partner_name'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Promo Code (for Promotional type) -->
                    @if($rule_type === 'promotional')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Promo Code (Optional)</label>
                            <input type="text" wire:model="promo_code" placeholder="e.g., SUMMER2024" 
                                   class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                    @endif

                    <!-- Active Status -->
                    <div class="flex items-center gap-3 p-3 rounded-xl border transition-colors" :class="$wire.is_active ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only">
                            <div class="relative">
                                <div class="w-8 h-4 bg-gray-300 rounded-full shadow-inner transition-colors" :class="$wire.is_active ? 'bg-green-500' : 'bg-gray-300'"></div>
                                <div class="absolute w-3 h-3 bg-white rounded-full shadow top-0.5 transition-transform" :class="$wire.is_active ? 'translate-x-4' : 'translate-x-0.5'"></div>
                            </div>
                        </label>
                        <span class="text-sm font-medium transition-colors" :class="$wire.is_active ? 'text-green-800' : 'text-gray-700'">Rule is active</span>
                    </div>

                    <!-- Footer with Actions -->
                    <div class="border-t border-gray-100 bg-gray-50/80 backdrop-blur-sm px-6 py-4 rounded-b-2xl -mx-6 -mb-6 mt-8">
                        <div class="flex flex-col-reverse sm:flex-row gap-3">
                            <button 
                                type="button" 
                                wire:click="close" 
                                class="flex-1 sm:flex-none sm:px-6 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 shadow-sm"
                            >
                                Cancel
                            </button>
                            
                            @if($mode === 'edit')
                                <button 
                                    type="button"
                                    wire:click="delete"
                                    wire:confirm="Are you sure you want to delete this pricing rule?"
                                    class="flex-1 sm:flex-none sm:px-6 py-3 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl"
                                >
                                    Delete
                                </button>
                            @endif
                            
                            <button 
                                type="button"
                                wire:click="save"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="flex-1 sm:flex-none sm:px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            >
                                <span class="flex items-center justify-center space-x-2">
                                    <svg wire:loading.remove class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove x-show="mode === 'create'">Create Rule</span>
                                    <span wire:loading.remove x-show="mode === 'edit'">Update Rule</span>
                                    <span wire:loading>Saving...</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>