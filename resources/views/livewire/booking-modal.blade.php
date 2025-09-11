<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ mode: @entangle('mode') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="close"></div>

                <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">New Booking</h3>
                            <p class="text-sm text-gray-600">Create a new reservation</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <!-- Mode Toggle -->
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button wire:click="$set('mode', 'quick')" 
                                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
                                        :class="mode === 'quick' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                    Quick
                                </button>
                                <button wire:click="$set('mode', 'full')" 
                                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
                                        :class="mode === 'full' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                    Full
                                </button>
                            </div>
                            <button wire:click="close" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit="save" class="space-y-6">
                        <!-- Property & Accommodation -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                                <select wire:model.live="property_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">Select Property</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                                    @endforeach
                                </select>
                                @error('property_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Accommodation</label>
                                <select wire:model.live="accommodation_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">Select Accommodation</option>
                                    @foreach($accommodations as $accommodation)
                                        <option value="{{ $accommodation->id }}">{{ $accommodation->display_name }} (₹{{ number_format($accommodation->base_rate ?? 0) }}/night)</option>
                                    @endforeach
                                </select>
                                @error('accommodation_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Dates & Guests -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Check-in</label>
                                <input type="date" wire:model.live="check_in_date" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                @error('check_in_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Check-out</label>
                                <input type="date" wire:model.live="check_out_date" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                @error('check_out_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                                <input type="number" wire:model.live="adults" min="1" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                @error('adults') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Children</label>
                                <input type="number" wire:model.live="children" min="0" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
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
                                <label class="block text-sm font-medium text-gray-700">Customer</label>
                                <button type="button" wire:click="$toggle('create_new_guest')" 
                                        class="text-sm text-emerald-600 hover:text-emerald-700">
                                    {{ $create_new_guest ? 'Select Existing' : 'Create New' }}
                                </button>
                            </div>

                            @if($create_new_guest)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-xl">
                                    <div>
                                        <input type="text" wire:model.live="guest_name" placeholder="Full Name" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        @error('guest_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <input type="tel" wire:model.live="guest_mobile" placeholder="Mobile Number" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        @error('guest_mobile') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <input type="email" wire:model="guest_email" placeholder="Email (Optional)" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                </div>
                            @else
                                <div>
                                    <select wire:model="guest_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">Select Customer</option>
                                        @foreach($guests as $guest)
                                            <option value="{{ $guest->id }}">{{ $guest->name }} ({{ $guest->mobile_number ?? $guest->phone }})</option>
                                        @endforeach
                                    </select>
                                    @error('guest_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>

                        <!-- B2B Partner (Full Mode) -->
                        <div x-show="mode === 'full'" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700">B2B Partner (Optional)</label>
                                <button type="button" wire:click="$toggle('create_new_partner')" 
                                        class="text-sm text-emerald-600 hover:text-emerald-700">
                                    {{ $create_new_partner ? 'Select Existing' : 'Add New' }}
                                </button>
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
                                <div>
                                    <select wire:model.live="b2b_partner_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">No B2B Partner</option>
                                        @foreach($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->partner_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <!-- Pricing -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <button type="button" wire:click="close" 
                                    class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200">
                                Create Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>