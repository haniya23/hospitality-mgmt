<div class="space-y-6" x-data>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="space-y-4">
        <!-- Title -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Booking Management</h2>
            <p class="text-gray-600">Manage your property bookings and calendar</p>
        </div>
        
        <!-- Controls - Mobile Responsive -->
        <div class="flex flex-col space-y-3 sm:space-y-0">
            <!-- Property Selector - Full width on mobile -->
            <div class="w-full relative" x-data="{ open: false }">
                <button @click="open = !open" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white text-left flex items-center justify-between">
                    <span class="text-gray-900">
                        @if($selectedProperty)
                            {{ $properties->find($selectedProperty)?->name ?? 'All Properties' }}
                        @else
                            All Properties
                        @endif
                    </span>
                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-auto">
                    <button wire:click="$set('selectedProperty', null)" @click="open = false" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 border-b border-gray-100">
                        All Properties
                    </button>
                    @foreach($properties as $property)
                        <button wire:click="$set('selectedProperty', {{ $property->id }})" @click="open = false" class="w-full px-4 py-3 text-left hover:bg-gray-50 text-sm text-gray-900 {{ $selectedProperty == $property->id ? 'bg-emerald-50 text-emerald-700' : '' }}">
                            {{ $property->name }}
                        </button>
                    @endforeach
                </div>
            </div>
            
            <!-- New Booking Button -->
            <div class="flex items-center justify-end gap-3">
                <!-- New Booking Button -->
                <div class="relative inline-flex items-center justify-center group">
                    <div class="absolute inset-0 duration-1000 opacity-60 transition-all bg-gradient-to-r from-emerald-500 via-teal-500 to-green-400 rounded-xl blur-lg filter group-hover:opacity-100 group-hover:duration-200"></div>
                    <button wire:click="openBookingModal" class="group relative inline-flex items-center justify-center text-sm sm:text-base rounded-xl bg-gray-900 px-4 sm:px-8 py-2 sm:py-3 font-semibold text-white transition-all duration-200 hover:bg-gray-800 hover:shadow-lg hover:-translate-y-0.5 hover:shadow-gray-600/30">
                        <span class="hidden sm:inline">+ New Booking</span>
                        <span class="sm:hidden">+ New Booking</span>
                        <svg aria-hidden="true" viewBox="0 0 10 10" height="10" width="10" fill="none" class="mt-0.5 ml-2 -mr-1 stroke-white stroke-2">
                            <path d="M0 5h7" class="transition opacity-0 group-hover:opacity-100"></path>
                            <path d="M1 1l4 4-4 4" class="transition group-hover:translate-x-[3px]"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content - Two Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Bookings -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
                <h3 class="text-lg font-semibold text-yellow-800">Pending Bookings ({{ $pendingBookings->count() }})</h3>
            </div>
            
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($pendingBookings as $booking)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $booking->guest->name }}</h4>
                                    @if($booking->b2bPartner)
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">B2B</span>
                                    @endif
                                </div>
                                
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>{{ $booking->accommodation->property->name }} - {{ $booking->accommodation->display_name }}</div>
                                    <div>{{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d, Y') }}</div>
                                </div>
                            </div>
                            
                            <div class="text-right space-y-1">
                                <div class="font-semibold text-gray-900">₹{{ number_format($booking->total_amount) }}</div>
                                @if($booking->accommodation->property->owner_id === auth()->id())
                                    <div class="space-y-1">
                                        <button wire:click="toggleBookingStatus({{ $booking->id }})" 
                                                class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition-colors">
                                            Activate
                                        </button>
                                        <button wire:click="openCancelModal({{ $booking->id }})" 
                                                class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <div class="text-sm">No pending bookings</div>
                    </div>
                @endforelse
            </div>
            @if($pendingBookings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pendingBookings->links() }}
                </div>
            @endif
        </div>

        <!-- Active Bookings -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                <h3 class="text-lg font-semibold text-green-800">Active Bookings ({{ $activeBookings->count() }})</h3>
            </div>
            
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($activeBookings as $booking)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $booking->guest->name }}</h4>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    @if($booking->b2bPartner)
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">B2B</span>
                                    @endif
                                </div>
                                
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>{{ $booking->accommodation->property->name }} - {{ $booking->accommodation->display_name }}</div>
                                    <div>{{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d, Y') }}</div>
                                </div>
                            </div>
                            
                            <div class="text-right space-y-1">
                                <div class="font-semibold text-gray-900">₹{{ number_format($booking->total_amount) }}</div>
                                @if($booking->balance_pending > 0)
                                    <div class="text-xs text-orange-600">₹{{ number_format($booking->balance_pending) }} pending</div>
                                @endif
                                @if($booking->accommodation->property->owner_id === auth()->id())
                                    <button wire:click="openCancelModal({{ $booking->id }})" 
                                            class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition-colors">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <div class="text-sm">No active bookings</div>
                    </div>
                @endforelse
            </div>
            @if($activeBookings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $activeBookings->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            <div class="text-2xl font-bold text-yellow-600">{{ $pendingBookings->count() }}</div>
            <div class="text-sm text-gray-600">Pending</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            <div class="text-2xl font-bold text-green-600">{{ $activeBookings->where('status', 'confirmed')->count() }}</div>
            <div class="text-sm text-gray-600">Confirmed</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            <div class="text-2xl font-bold text-blue-600">{{ $activeBookings->where('status', 'checked_in')->count() }}</div>
            <div class="text-sm text-gray-600">Checked In</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            <div class="text-2xl font-bold text-gray-900">₹{{ number_format($pendingBookings->sum('total_amount') + $activeBookings->sum('total_amount')) }}</div>
            <div class="text-sm text-gray-600">Total Value</div>
        </div>
    </div>

    <!-- View Cancelled Bookings Button -->
    <div class="text-center">
        <button wire:click="$toggle('showCancelledBookings')" 
                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
            {{ $showCancelledBookings ? 'Hide' : 'View' }} Cancelled Bookings ({{ $cancelledBookings->count() }})
        </button>
    </div>

    <!-- Cancelled Bookings Section -->
    @if($showCancelledBookings)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                <h3 class="text-lg font-semibold text-red-800">Cancelled Bookings</h3>
            </div>
            
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($cancelledBookings as $booking)
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $booking->guest->name }}</h4>
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Cancelled</span>
                                </div>
                                
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>{{ $booking->accommodation->property->name }} - {{ $booking->accommodation->display_name }}</div>
                                    <div>{{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d, Y') }}</div>
                                    @if($booking->cancelledBooking)
                                        <div class="text-red-600">Reason: {{ $booking->cancelledBooking->reason }}</div>
                                        @if($booking->cancelledBooking->description)
                                            <div class="text-gray-500">{{ $booking->cancelledBooking->description }}</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="font-semibold text-gray-900">₹{{ number_format($booking->total_amount) }}</div>
                                @if($booking->cancelledBooking)
                                    <div class="text-xs text-gray-500">{{ $booking->cancelledBooking->cancelled_at->format('M d, Y') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <div class="text-sm">No cancelled bookings</div>
                    </div>
                @endforelse
            </div>
            @if($cancelledBookings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $cancelledBookings->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Cancel Modal -->
    @if($showCancelModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50" x-data="{ show: true }">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancel Booking</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Cancellation</label>
                            <div class="relative" x-data="{ reasonOpen: false }">
                                <button @click="reasonOpen = !reasonOpen" type="button" class="w-full border border-gray-300 rounded-xl px-3 py-2 text-left flex items-center justify-between bg-white">
                                    <span class="{{ $cancelReason ? 'text-gray-900' : 'text-gray-500' }}">
                                        {{ $cancelReason ?: 'Select reason' }}
                                    </span>
                                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': reasonOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="reasonOpen" @click.away="reasonOpen = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-xl shadow-lg">
                                    <button wire:click="$set('cancelReason', 'Guest Request')" @click="reasonOpen = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm text-gray-900">
                                        Guest Request
                                    </button>
                                    <button wire:click="$set('cancelReason', 'No Show')" @click="reasonOpen = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm text-gray-900">
                                        No Show
                                    </button>
                                    <button wire:click="$set('cancelReason', 'Payment Issue')" @click="reasonOpen = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm text-gray-900">
                                        Payment Issue
                                    </button>
                                    <button wire:click="$set('cancelReason', 'Property Issue')" @click="reasonOpen = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm text-gray-900">
                                        Property Issue
                                    </button>
                                    <button wire:click="$set('cancelReason', 'Overbooking')" @click="reasonOpen = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm text-gray-900">
                                        Overbooking
                                    </button>
                                    <button wire:click="$set('cancelReason', 'Other')" @click="reasonOpen = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm text-gray-900">
                                        Other
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea wire:model="cancelDescription" rows="3" 
                                      class="w-full border border-gray-300 rounded-xl px-3 py-2" 
                                      placeholder="Optional description..."></textarea>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mt-6">
                        <button wire:click="closeCancelModal" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button wire:click="cancelBooking" 
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">
                            Confirm Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Booking Modal -->
    <livewire:booking-modal :property-id="$selectedProperty" key="booking-modal" />
</div>

