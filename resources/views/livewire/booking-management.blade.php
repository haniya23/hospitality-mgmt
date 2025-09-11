<div class="space-y-6">
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
            <div class="w-full">
                <select wire:model.live="selectedProperty" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- View Toggle and New Booking Button -->
            <div class="flex items-center justify-between gap-3">
                <!-- View Toggle -->
                <div class="flex bg-gray-100 rounded-lg p-1 flex-1 sm:flex-none">
                    <button wire:click="switchView('calendar')" 
                            class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-md transition-colors
                            {{ $view === 'calendar' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}">
                        Calendar
                    </button>
                    <button wire:click="switchView('list')" 
                            class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-md transition-colors
                            {{ $view === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}">
                        List
                    </button>
                </div>

                <!-- New Booking Button -->
                <button wire:click="openBookingModal" 
                        class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-4 py-3 rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 text-sm font-medium whitespace-nowrap">
                    + New
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    @if($view === 'calendar')
        <!-- Calendar View -->
        <livewire:booking-calendar :property-id="$selectedProperty" />
    @else
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
    @endif

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

