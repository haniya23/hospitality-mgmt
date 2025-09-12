<div class="space-y-6" x-data>
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

    <!-- Main Content -->
    <!-- List View -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
            </div>
            
            <div class="divide-y divide-gray-200">
                @forelse($recentBookings as $booking)
                    <div class="p-6 hover:bg-gray-50 cursor-pointer" wire:click="openBookingDetails({{ $booking->id }})">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $booking->guest->name }}</h4>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'confirmed' || $booking->status === 'active') bg-green-100 text-green-800
                                        @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                                        @elseif($booking->status === 'completed') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    @if($booking->b2bPartner)
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                            B2B
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div class="flex items-center space-x-4">
                                        <span>{{ $booking->accommodation->property->name }}</span>
                                        <span>{{ $booking->accommodation->display_name }}</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span>{{ $booking->check_in_date->format('M d, Y') }} - {{ $booking->check_out_date->format('M d, Y') }}</span>
                                        <span>{{ $booking->adults }} adult{{ $booking->adults > 1 ? 's' : '' }}{{ $booking->children > 0 ? ', ' . $booking->children . ' child' . ($booking->children > 1 ? 'ren' : '') : '' }}</span>
                                    </div>
                                    @if($booking->b2bPartner)
                                        <div class="text-blue-600">
                                            Partner: {{ $booking->b2bPartner->partner_name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-lg font-semibold text-gray-900">₹{{ number_format($booking->total_amount) }}</div>
                                @if($booking->advance_paid > 0)
                                    <div class="text-sm text-green-600">₹{{ number_format($booking->advance_paid) }} paid</div>
                                @endif
                                @if($booking->balance_pending > 0)
                                    <div class="text-sm text-orange-600">₹{{ number_format($booking->balance_pending) }} pending</div>
                                @endif
                            </div>
                        </div>
                        
                        @if($booking->special_requests)
                            <div class="mt-3 p-3 bg-yellow-50 rounded-lg">
                                <div class="text-sm text-yellow-800">
                                    <strong>Special Requests:</strong> {{ $booking->special_requests }}
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Bookings Yet</h3>
                        <p class="text-gray-600 mb-4">Create your first booking to get started.</p>
                        <button wire:click="openBookingModal" 
                                class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-6 py-3 rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200">
                            Create First Booking
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            <div class="text-2xl font-bold text-gray-900">{{ $recentBookings->where('status', 'pending')->count() }}</div>
            <div class="text-sm text-gray-600">Pending</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            <div class="text-2xl font-bold text-gray-900">{{ $recentBookings->where('status', 'confirmed')->count() }}</div>
            <div class="text-sm text-gray-600">Confirmed</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            <div class="text-2xl font-bold text-gray-900">{{ $recentBookings->where('status', 'checked_in')->count() }}</div>
            <div class="text-sm text-gray-600">Checked In</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            <div class="text-2xl font-bold text-gray-900">₹{{ number_format($recentBookings->sum('total_amount')) }}</div>
            <div class="text-sm text-gray-600">Total Value</div>
        </div>
    </div>

    <!-- Booking Modal -->
    <livewire:booking-modal :property-id="$selectedProperty" key="booking-modal" />
</div>

