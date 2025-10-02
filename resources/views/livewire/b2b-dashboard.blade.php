<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" style="z-index: 1 !important;">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-4 text-white" style="z-index: 1 !important;">
            <div class="text-2xl font-bold">{{ $stats['total_received'] }}</div>
            <div class="text-sm opacity-90">Received Bookings</div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white" style="z-index: 1 !important;">
            <div class="text-2xl font-bold">{{ $stats['total_sent'] }}</div>
            <div class="text-sm opacity-90">Sent Bookings</div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-4 text-white" style="z-index: 1 !important;">
            <div class="text-2xl font-bold">₹{{ number_format($stats['pending_commissions']) }}</div>
            <div class="text-sm opacity-90">Pending Payouts</div>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-4 text-white" style="z-index: 1 !important;">
            <div class="text-2xl font-bold">₹{{ number_format($stats['receivable_commissions']) }}</div>
            <div class="text-sm opacity-90">Receivable</div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="flex border-b border-gray-200">
            <button wire:click="switchTab('receiving')" 
                    class="flex-1 px-6 py-4 text-center font-medium transition-colors
                    {{ $activeTab === 'receiving' ? 'bg-emerald-50 text-emerald-600 border-b-2 border-emerald-500' : 'text-gray-600 hover:text-gray-900' }}">
                As Owner
                <span class="ml-2 px-2 py-1 text-xs bg-gray-100 rounded-full">{{ $stats['total_received'] }}</span>
            </button>
            <button wire:click="switchTab('sending')" 
                    class="flex-1 px-6 py-4 text-center font-medium transition-colors
                    {{ $activeTab === 'sending' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-500' : 'text-gray-600 hover:text-gray-900' }}">
                As Partner
                <span class="ml-2 px-2 py-1 text-xs bg-gray-100 rounded-full">{{ $stats['total_sent'] }}</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-wrap gap-4">
                <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="checked_in">Checked In</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>

                @if($activeTab === 'receiving')
                    <select wire:model.live="partnerFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="all">All Partners</option>
                        @foreach($availablePartners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->partner_name }}</option>
                        @endforeach
                    </select>
                @endif

                <button wire:click="resetFilters" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900">
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Content based on active tab -->
        @if($activeTab === 'receiving')
            <!-- Receiving Bookings (As Owner) -->
            <div class="divide-y divide-gray-200">
                @forelse($receivingBookings as $booking)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $booking->guest->name }}</h4>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                                        @elseif($booking->status === 'completed') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div class="flex items-center space-x-4">
                                        <span>Partner: <strong>{{ $booking->b2bPartner->partner_name }}</strong></span>
                                        <span>Property: {{ $booking->accommodation->property->name }}</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span>{{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d, Y') }}</span>
                                        <span>₹{{ number_format($booking->total_amount) }}</span>
                                        @if($booking->commission)
                                            <span class="text-orange-600">Commission: ₹{{ number_format($booking->commission->amount) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                @if($booking->commission && $booking->commission->status === 'pending')
                                    <button wire:click="markCommissionPaid({{ $booking->commission->id }}, {{ $booking->commission->amount }})"
                                            class="px-3 py-1 text-xs bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200">
                                        Mark Paid
                                    </button>
                                @elseif($booking->commission && $booking->commission->status === 'paid')
                                    <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-lg">
                                        Paid
                                    </span>
                                @endif
                                
                                <button class="p-2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p>No B2B bookings received yet</p>
                    </div>
                @endforelse
            </div>

            <!-- Incoming Requests -->
            @if($incomingRequests->count() > 0)
                <div class="border-t border-gray-200 bg-blue-50">
                    <div class="p-4">
                        <h3 class="font-medium text-gray-900 mb-3">Pending Requests ({{ $incomingRequests->count() }})</h3>
                        <div class="space-y-3">
                            @foreach($incomingRequests as $request)
                                <div class="bg-white p-3 rounded-lg border border-blue-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="text-sm">
                                                <strong>{{ $request->fromPartner->name }}</strong> requests booking
                                                for {{ $request->check_in_date->format('M d') }} - {{ $request->check_out_date->format('M d') }}
                                            </div>
                                            <div class="text-xs text-gray-600 mt-1">
                                                Quoted: ₹{{ number_format($request->quoted_price) }}
                                                @if($request->counter_price)
                                                    • Counter: ₹{{ number_format($request->counter_price) }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button wire:click="acceptRequest({{ $request->id }})"
                                                    class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">
                                                Accept
                                            </button>
                                            <button wire:click="openRequestModal({{ $request->id }})"
                                                    class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                                Counter
                                            </button>
                                            <button wire:click="rejectRequest({{ $request->id }})"
                                                    class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">
                                                Reject
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        @else
            <!-- Sending Bookings (As Partner) -->
            <div class="divide-y divide-gray-200">
                @forelse($sendingBookings as $booking)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $booking->accommodation->property->name }}</h4>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                                        @elseif($booking->status === 'completed') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div class="flex items-center space-x-4">
                                        <span>Guest: {{ $booking->guest->name }}</span>
                                        <span>{{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span>Amount: ₹{{ number_format($booking->total_amount) }}</span>
                                        @if($booking->commission)
                                            <span class="text-green-600">
                                                Commission: ₹{{ number_format($booking->commission->amount) }}
                                                @if($booking->commission->status === 'paid')
                                                    ✓
                                                @else
                                                    (Pending)
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <p>No bookings sent yet</p>
                        <button class="mt-2 text-sm text-blue-600 hover:text-blue-700">
                            Create your first booking request
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Outgoing Requests -->
            @if($outgoingRequests->count() > 0)
                <div class="border-t border-gray-200 bg-green-50">
                    <div class="p-4">
                        <h3 class="font-medium text-gray-900 mb-3">Your Requests ({{ $outgoingRequests->count() }})</h3>
                        <div class="space-y-3">
                            @foreach($outgoingRequests as $request)
                                <div class="bg-white p-3 rounded-lg border border-green-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="text-sm">
                                                Request to <strong>{{ $request->toProperty->name }}</strong>
                                                for {{ $request->check_in_date->format('M d') }} - {{ $request->check_out_date->format('M d') }}
                                            </div>
                                            <div class="text-xs text-gray-600 mt-1">
                                                Status: {{ ucfirst($request->status) }} • ₹{{ number_format($request->quoted_price) }}
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($request->status === 'accepted') bg-green-100 text-green-800
                                            @elseif($request->status === 'countered') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>