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
            <div class="flex items-center justify-between p-6 pb-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-t-2xl border-b border-emerald-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight" id="modal-title">
                            New Booking
                        </h3>
                        <p class="text-sm text-emerald-600 font-medium mt-1">
                            Create a new reservation
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Mode Toggle -->
                    <div class="flex bg-white/80 rounded-lg p-1 shadow-sm">
                        <button wire:click="$set('mode', 'quick')" 
                                class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
                                :class="mode === 'quick' ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-gray-600'">
                            Quick
                        </button>
                        <button wire:click="$set('mode', 'full')" 
                                class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
                                :class="mode === 'full' ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-gray-600'">
                            Full
                        </button>
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
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                        <!-- Property & Accommodation -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="relative" x-data="{ open: false }">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Property</label>
                                <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white text-left flex items-center justify-between">
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
                                        <button wire:click="$set('property_id', {{ $property['id'] }})" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $property_id == $property['id'] ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                            {{ $property['name'] }}
                                        </button>
                                    @endforeach
                                </div>
                                @error('property_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="relative" x-data="{ open: false }">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Accommodation</label>
                                <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white text-left flex items-center justify-between">
                                    <span>
                                        @if($accommodation_id)
                                            @php $selectedAccommodation = collect($accommodations)->firstWhere('id', $accommodation_id); @endphp
                                            {{ $selectedAccommodation['display_name'] ?? 'Select Accommodation' }}
                                        @else
                                            Select Accommodation
                                        @endif
                                    </span>
                                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto">
                                    <button wire:click="$set('accommodation_id', null)" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900">
                                        Select Accommodation
                                    </button>
                                    @foreach($accommodations as $accommodation)
                                        <button wire:click="$set('accommodation_id', {{ $accommodation['id'] }})" @click="open = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $accommodation_id == $accommodation['id'] ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                            {{ $accommodation['display_name'] }} (₹{{ number_format($accommodation['base_rate'] ?? 0) }}/night)
                                        </button>
                                    @endforeach
                                </div>
                                @error('accommodation_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Past Dates Toggle -->
                        <div class="flex items-center gap-3 p-3 rounded-xl border transition-colors" :class="$wire.allow_past_dates ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'" x-data="{ showTooltip: false }">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="allow_past_dates" class="sr-only">
                                <div class="relative">
                                    <div class="w-8 h-4 bg-gray-300 rounded-full shadow-inner transition-colors" :class="$wire.allow_past_dates ? 'bg-green-500' : 'bg-gray-300'"></div>
                                    <div class="absolute w-3 h-3 bg-white rounded-full shadow top-0.5 transition-transform" :class="$wire.allow_past_dates ? 'translate-x-4' : 'translate-x-0.5'"></div>
                                </div>
                            </label>
                            <span class="text-sm font-medium transition-colors" :class="$wire.allow_past_dates ? 'text-green-800' : 'text-gray-700'">Allow past dates</span>
                            <button @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" type="button" class="transition-colors relative" :class="$wire.allow_past_dates ? 'text-green-600 hover:text-green-700' : 'text-gray-400 hover:text-gray-600'">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div x-show="showTooltip" x-transition class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-50 px-2 py-1 text-xs text-white bg-gray-800 rounded shadow-lg whitespace-nowrap">
                                    Record booking - past dates
                                </div>
                            </button>
                        </div>

                        <!-- Dates & Guests -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in</label>
                                <input type="date" wire:model.live="check_in_date" 
                                       @if(!$allow_past_dates) min="{{ date('Y-m-d') }}" @endif
                                       class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                @error('check_in_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out</label>
                                <input type="date" wire:model.live="check_out_date" 
                                       @if(!$allow_past_dates) min="{{ date('Y-m-d') }}" @endif
                                       class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                @error('check_out_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Adults</label>
                                <input type="number" wire:model.live="adults" min="1" 
                                       class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                @error('adults') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Children</label>
                                <input type="number" wire:model.live="children" min="0" 
                                       class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                @error('children') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        @if($nights > 0)
                            <div class="bg-emerald-50 p-4 rounded-xl">
                                <p class="text-sm text-emerald-700">
                                    <span class="font-medium">{{ $nights }} night{{ $nights > 1 ? 's' : '' }}</span>
                                    @if($base_rate > 0)
                                        • Base rate: ₹{{ number_format($base_rate) }}/night
                                    @endif
                                </p>
                            </div>
                        @endif

                        <!-- Customer Selection -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-semibold text-gray-700">Customer</label>
                                <div class="flex bg-gray-100 rounded-lg p-1">
                                    <button type="button" wire:click="$set('create_new_guest', false)" 
                                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
                                            :class="!$wire.create_new_guest ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                        Select
                                    </button>
                                    <button type="button" wire:click="$set('create_new_guest', true)" 
                                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
                                            :class="$wire.create_new_guest ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                        Create
                                    </button>
                                </div>
                            </div>

                            @if($create_new_guest)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-xl">
                                    <div>
                                        <input type="text" wire:model.live="guest_name" placeholder="Full Name" 
                                               class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                        @error('guest_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <input type="tel" wire:model.live="guest_mobile" placeholder="Mobile Number" 
                                               class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                        @error('guest_mobile') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <input type="email" wire:model="guest_email" placeholder="Email (Optional)" 
                                               class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                    </div>
                                </div>
                            @else
                                <div class="relative" x-data="{ guestOpen: false }">
                                    <button @click="guestOpen = !guestOpen" type="button" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white text-left flex items-center justify-between">
                                        <span>
                                            @if($guest_id)
                                                @php $selectedGuest = collect($guests)->firstWhere('id', $guest_id); @endphp
                                                {{ $selectedGuest['name'] ?? 'Select Customer' }}
                                            @else
                                                Select Customer
                                            @endif
                                        </span>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': guestOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="guestOpen" @click.away="guestOpen = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto">
                                        <button wire:click="$set('guest_id', null)" @click="guestOpen = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900">
                                            Select Customer
                                        </button>
                                        @foreach($guests as $guest)
                                            <button wire:click="$set('guest_id', {{ $guest['id'] }})" @click="guestOpen = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $guest_id == $guest['id'] ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                                {{ $guest['name'] }} ({{ $guest['mobile_number'] ?? $guest['phone'] }})
                                            </button>
                                        @endforeach
                                    </div>
                                    @error('guest_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>

                        <!-- B2B Partner -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700">B2B Partner (Optional)</label>
                                <div class="flex bg-gray-100 rounded-lg p-1">
                                    <button type="button" wire:click="$set('create_new_partner', false)" 
                                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
                                            :class="!$wire.create_new_partner ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                        Select
                                    </button>
                                    <button type="button" wire:click="$set('create_new_partner', true)" 
                                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
                                            :class="$wire.create_new_partner ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                        Create
                                    </button>
                                </div>
                            </div>

                            @if($create_new_partner)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-blue-50 rounded-xl">
                                    <div>
                                        <input type="text" wire:model="partner_name" placeholder="Partner Name" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <input type="tel" wire:model.live="partner_mobile" placeholder="Partner Mobile" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            @else
                                <div class="relative" x-data="{ partnerOpen: false }">
                                    <button @click="partnerOpen = !partnerOpen" type="button" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-left flex items-center justify-between">
                                        <span>
                                            @if($b2b_partner_id)
                                                @php $selectedPartner = collect($partners)->firstWhere('id', $b2b_partner_id); @endphp
                                                {{ $selectedPartner['partner_name'] ?? 'No B2B Partner' }}
                                            @else
                                                No B2B Partner
                                            @endif
                                        </span>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': partnerOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="partnerOpen" @click.away="partnerOpen = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-auto">
                                        <button wire:click="$set('b2b_partner_id', null)" @click="partnerOpen = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900">
                                            No B2B Partner
                                        </button>
                                        @foreach($partners as $partner)
                                            <button wire:click="$set('b2b_partner_id', {{ $partner['id'] }})" @click="partnerOpen = false" type="button" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $b2b_partner_id == $partner['id'] ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                                {{ $partner['partner_name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Pricing -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                                    <input type="number" wire:model.live="total_amount" step="0.01" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    @error('total_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Advance Paid</label>
                                    <input type="number" wire:model.live="advance_paid" step="0.01" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    @error('advance_paid') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Balance Pending</label>
                                    <input type="number" value="{{ $balance_pending }}" readonly 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-600">
                                </div>
                            </div>

                            <!-- Rate Override (Full Mode) -->
                            <div x-show="mode === 'full'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rate Override</label>
                                    <input type="number" wire:model="rate_override" step="0.01" placeholder="Override total amount" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Override Reason</label>
                                    <input type="text" wire:model="override_reason" placeholder="Reason for override" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>

                            <!-- Applied Discounts -->
                            @if(count($applicable_discounts) > 0)
                                <div class="bg-green-50 p-4 rounded-xl">
                                    <h4 class="text-sm font-medium text-green-800 mb-2">Applied Discounts</h4>
                                    @foreach($applicable_discounts as $discount)
                                        <div class="flex justify-between text-sm text-green-700">
                                            <span>{{ $discount['name'] }}</span>
                                            <span>{{ $discount['adjustment'] > 0 ? '+' : '' }}₹{{ number_format($discount['adjustment']) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Notes (Full Mode) -->
                        <div x-show="mode === 'full'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                                <textarea wire:model="special_requests" rows="2" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                          placeholder="Any special requests from guest..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Internal Notes</label>
                                <textarea wire:model="notes" rows="2" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                          placeholder="Internal notes for staff..."></textarea>
                            </div>
                        </div>

                    <!-- Enhanced Footer with Better Actions -->
                    <div class="border-t border-gray-100 bg-gray-50/80 backdrop-blur-sm px-6 py-4 rounded-b-2xl">
                        <div class="flex flex-col-reverse sm:flex-row gap-3">
                            <button 
                                type="button" 
                                wire:click="close" 
                                class="flex-1 sm:flex-none sm:px-6 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 shadow-sm"
                            >
                                Cancel
                            </button>
                            
                            <button 
                                type="button"
                                wire:click="save"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="flex-1 sm:flex-none sm:px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            >
                                <span class="flex items-center justify-center space-x-2">
                                    <svg wire:loading.remove class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove>Create Booking</span>
                                    <span wire:loading>Creating...</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>