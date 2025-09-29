<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <!-- Calendar Header -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <button wire:click="previousMonth" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <h2 class="text-xl font-bold text-white">{{ $monthName }}</h2>
            
            <button wire:click="nextMonth" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="p-4">
        <!-- Day Headers -->
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div class="p-2 text-center text-sm font-medium text-gray-500">{{ $day }}</div>
            @endforeach
        </div>

        <!-- Calendar Days -->
        @foreach($calendarWeeks as $week)
            <div class="grid grid-cols-7 gap-1 mb-1">
                @foreach($week as $day)
                    <div class="min-h-[80px] p-1 border border-gray-100 rounded-lg relative
                        {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50' }}
                        {{ $day['isToday'] ? 'ring-2 ring-emerald-500' : '' }}
                        @if($day['bookingCount'] > 0)
                            @php
                                $statuses = $day['bookings']->pluck('status')->unique();
                                $hasPending = $statuses->contains('pending');
                                $hasConfirmed = $statuses->contains('confirmed') || $statuses->contains('active');
                                $hasCheckedIn = $statuses->contains('checked_in');
                                $hasCompleted = $statuses->contains('completed');
                                $hasCancelled = $statuses->contains('cancelled');
                            @endphp
                            @if($hasCancelled) bg-red-50 border-red-200
                            @elseif($hasCheckedIn) bg-blue-50 border-blue-200
                            @elseif($hasCompleted) bg-purple-50 border-purple-200
                            @elseif($hasConfirmed) bg-green-50 border-green-200
                            @elseif($hasPending) bg-yellow-50 border-yellow-200
                            @endif
                        @endif"
                        wire:click="selectDate('{{ $day['date']->format('Y-m-d') }}')"
                    >
                        <!-- Date Number -->
                        <div class="text-sm font-medium mb-1
                            {{ $day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400' }}
                            {{ $day['isToday'] ? 'text-emerald-600' : '' }}">
                            {{ $day['date']->format('j') }}
                        </div>

                        <!-- Booking Indicators -->
                        @if($day['bookingCount'] > 0)
                            <div class="space-y-1">
                                @foreach($day['bookings']->take(2) as $booking)
                                    <div class="text-xs px-2 py-1 rounded-full cursor-pointer
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'confirmed' || $booking->status === 'active') bg-green-100 text-green-800
                                        @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                                        @elseif($booking->status === 'completed') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif"
                                        wire:click.stop="openBooking({{ $booking->id }})"
                                    >
                                        {{ Str::limit($booking->guest->name, 10) }}
                                        @if($booking->b2bPartner)
                                            <span class="text-xs">•B2B</span>
                                        @endif
                                    </div>
                                @endforeach
                                
                                @if($day['bookingCount'] > 2)
                                    <div class="text-xs text-gray-500 text-center">
                                        +{{ $day['bookingCount'] - 2 }} more
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Check-in/Check-out Indicators -->
                        <div class="absolute bottom-1 right-1 flex space-x-1">
                            @if($day['hasCheckIn'])
                                <div class="w-2 h-2 bg-green-500 rounded-full" title="Check-in"></div>
                            @endif
                            @if($day['hasCheckOut'])
                                <div class="w-2 h-2 bg-red-500 rounded-full" title="Check-out"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Legend -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <div class="flex flex-wrap gap-4 text-xs">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-yellow-50 border border-yellow-200 rounded"></div>
                <span class="text-gray-600">Pending</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-50 border border-green-200 rounded"></div>
                <span class="text-gray-600">Confirmed</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-blue-50 border border-blue-200 rounded"></div>
                <span class="text-gray-600">Checked-in</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-purple-50 border border-purple-200 rounded"></div>
                <span class="text-gray-600">Completed</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-red-50 border border-red-200 rounded"></div>
                <span class="text-gray-600">Cancelled</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <span class="text-gray-600">Check-in</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                <span class="text-gray-600">Check-out</span>
            </div>
        </div>
    </div>
</div>