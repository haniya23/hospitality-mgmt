@extends('layouts.app')

@section('title', $property->name . ' - Property Overview')

@push('styles')
<style>
    .property-header-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .soft-glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .stat-card.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stat-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-card.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stat-card.danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    }
    .room-status-occupied {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
    }
    .room-status-vacant {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    .room-status-maintenance {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    .task-priority-low { border-left: 4px solid #10b981; }
    .task-priority-medium { border-left: 4px solid #f59e0b; }
    .task-priority-high { border-left: 4px solid #ef4444; }
    .task-priority-urgent { border-left: 4px solid #dc2626; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50">
    <!-- Property Header -->
    <header class="property-header-gradient text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white bg-opacity-10"></div>
        <div class="relative px-4 py-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="mb-6 lg:mb-0">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 rounded-2xl soft-glass-card flex items-center justify-center mr-4">
                                <i class="fas fa-building text-2xl text-purple-600"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-2">{{ $property->name }}</h1>
                                <div class="flex items-center text-white/80">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>{{ $property->location->city->name ?? 'Location not set' }}, {{ $property->location->city->district->state->name ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                {{ ucfirst($property->status) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                                <i class="fas fa-home mr-2"></i>
                                {{ $property->category->name ?? 'Property Category' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('properties.edit', $property) }}" class="soft-glass-card rounded-xl px-6 py-3 hover:bg-opacity-80 transition-all flex items-center">
                            <i class="fas fa-edit mr-2"></i>
                            <span class="font-medium">Edit Property</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="soft-glass-card rounded-xl px-6 py-3 hover:bg-opacity-80 transition-all flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span class="font-medium">Back to Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Snapshot Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card success rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Occupancy</p>
                        <p class="text-3xl font-bold">{{ $occupiedAccommodations }} / {{ $totalAccommodations }}</p>
                        <p class="text-sm text-white/70">{{ $totalAccommodations > 0 ? round(($occupiedAccommodations / $totalAccommodations) * 100, 1) : 0 }}% occupied</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-bed text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card info rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Guests Onsite</p>
                        <p class="text-3xl font-bold">{{ $currentGuests->count() }}</p>
                        <p class="text-sm text-white/70">{{ $currentGuests->sum('adults') + $currentGuests->sum('children') }} total guests</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card warning rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Today's Revenue</p>
                        <p class="text-3xl font-bold">₹{{ number_format($todaysRevenue) }}</p>
                        <p class="text-sm text-white/70">{{ $monthlyStats['revenue'] > 0 ? round(($todaysRevenue / $monthlyStats['revenue']) * 100, 1) : 0 }}% of monthly</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-rupee-sign text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Staff On Duty</p>
                        <p class="text-3xl font-bold">{{ $staffOnDuty->count() }}</p>
                        <p class="text-sm text-white/70">{{ $pendingTasks->count() }} pending tasks</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-tie text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Guests & Bookings Status -->
                <div class="soft-glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Guests & Bookings Status</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-lg">Current</button>
                            <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Check-ins</button>
                            <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Check-outs</button>
                        </div>
                    </div>
                    
                    <!-- Current Guests -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Current Guests ({{ $currentGuests->count() }})</h4>
                        @if($currentGuests->count() > 0)
                        <div class="space-y-3">
                            @foreach($currentGuests as $guest)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-sm">{{ substr($guest->guest->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $guest->guest->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $guest->propertyAccommodation->name }} • {{ $guest->adults + $guest->children }} guests</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Check-out</div>
                                        <div class="font-medium text-gray-900">{{ $guest->check_out_date->format('M d') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-users text-4xl mb-4"></i>
                            <p>No guests currently checked in</p>
                        </div>
                        @endif
                    </div>

                    <!-- Next Check-ins -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Next Check-ins ({{ $nextCheckins->count() }})</h4>
                        @if($nextCheckins->count() > 0)
                        <div class="space-y-3">
                            @foreach($nextCheckins as $checkin)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-green-500 to-teal-600 flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-sm">{{ substr($checkin->guest->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $checkin->guest->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $checkin->propertyAccommodation->name }} • {{ $checkin->adults + $checkin->children }} guests</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Check-in</div>
                                        <div class="font-medium text-gray-900">{{ $checkin->check_in_date->format('M d, h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-gray-500">
                            <p>No check-ins scheduled for today</p>
                        </div>
                        @endif
                    </div>

                    <!-- Next Check-outs -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Next Check-outs ({{ $nextCheckouts->count() }})</h4>
                        @if($nextCheckouts->count() > 0)
                        <div class="space-y-3">
                            @foreach($nextCheckouts as $checkout)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-orange-500 to-red-600 flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-sm">{{ substr($checkout->guest->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $checkout->guest->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $checkout->propertyAccommodation->name }} • {{ $checkout->adults + $checkout->children }} guests</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Check-out</div>
                                        <div class="font-medium text-gray-900">{{ $checkout->check_out_date->format('M d, h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-gray-500">
                            <p>No check-outs scheduled for today</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Staff & Tasks Dashboard -->
                <div class="soft-glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Staff & Operations</h3>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Assign Task
                        </button>
                    </div>
                    
                    <!-- Active Staff -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Active Staff ({{ $staffOnDuty->count() }})</h4>
                        @if($staffOnDuty->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($staffOnDuty as $staff)
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center mr-3">
                                        <span class="text-white font-semibold text-sm">{{ substr($staff->user->name, 0, 2) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $staff->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $staff->role->name ?? 'Staff' }}</div>
                                    </div>
                                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-user-tie text-4xl mb-4"></i>
                            <p>No staff assigned to this property</p>
                        </div>
                        @endif
                    </div>

                    <!-- Pending Tasks -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Pending Tasks ({{ $pendingTasks->count() }})</h4>
                        @if($pendingTasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($pendingTasks->take(5) as $task)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 task-priority-{{ $task->priority }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $task->task_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $task->description }}</div>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($task->task_type) }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">{{ $task->scheduled_at ? $task->scheduled_at->format('M d, h:i A') : 'No schedule' }}</div>
                                        <div class="text-sm font-medium text-gray-900">{{ $task->staffAssignment->user->name ?? 'Unassigned' }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-tasks text-4xl mb-4"></i>
                            <p>No pending tasks</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Accommodation Status -->
                <div class="soft-glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Accommodation Status</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-lg">All</button>
                            <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Occupied</button>
                            <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Vacant</button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($property->propertyAccommodations as $accommodation)
                        @php
                            $currentReservation = $accommodation->reservations()
                                ->where('status', 'checked_in')
                                ->where('check_in_date', '<=', today())
                                ->where('check_out_date', '>', today())
                                ->first();
                        @endphp
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="font-semibold text-gray-900">{{ $accommodation->name }}</h5>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $currentReservation ? 'room-status-occupied' : 'room-status-vacant' }}">
                                    {{ $currentReservation ? 'Occupied' : 'Vacant' }}
                                </span>
                            </div>
                            @if($currentReservation)
                            <div class="text-sm text-gray-600 mb-2">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    <span>{{ $currentReservation->guest->name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    <span>Check-out: {{ $currentReservation->check_out_date->format('M d') }}</span>
                                </div>
                            </div>
                            @else
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-check-circle mr-2"></i>
                                Available for booking
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
                <!-- Property Alerts -->
                <div class="soft-glass-card rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Property Alerts</h3>
                    
                    <div class="space-y-4">
                        @if($overdueTasks->count() > 0)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                <div>
                                    <div class="font-medium text-red-800">{{ $overdueTasks->count() }} Overdue Tasks</div>
                                    <div class="text-sm text-red-600">Tasks past their scheduled time</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($maintenanceTickets->count() > 0)
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-wrench text-orange-500 mr-3"></i>
                                <div>
                                    <div class="font-medium text-orange-800">{{ $maintenanceTickets->count() }} Maintenance Tickets</div>
                                    <div class="text-sm text-orange-600">Open maintenance requests</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($nextCheckouts->count() > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-sign-out-alt text-blue-500 mr-3"></i>
                                <div>
                                    <div class="font-medium text-blue-800">{{ $nextCheckouts->count() }} Check-outs Today</div>
                                    <div class="text-sm text-blue-600">Prepare for guest departures</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($nextCheckins->count() > 0)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-sign-in-alt text-green-500 mr-3"></i>
                                <div>
                                    <div class="font-medium text-green-800">{{ $nextCheckins->count() }} Check-ins Today</div>
                                    <div class="text-sm text-green-600">Prepare for new arrivals</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Performance Insights -->
                <div class="soft-glass-card rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Performance Insights</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-500">Occupancy Rate</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $monthlyStats['occupancy_rate'] }}%</div>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-line text-blue-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-500">Monthly Revenue</div>
                                    <div class="text-2xl font-bold text-gray-900">₹{{ number_format($monthlyStats['revenue']) }}</div>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-rupee-sign text-green-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-500">Total Bookings</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $monthlyStats['total_bookings'] }}</div>
                                </div>
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-purple-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-500">Avg Stay Duration</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $monthlyStats['average_stay'] }} days</div>
                                </div>
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-orange-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- B2B Partners -->
                @if($b2bPartners->count() > 0)
                <div class="soft-glass-card rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">B2B Partners</h3>
                    
                    <div class="space-y-4">
                        @foreach($b2bPartners as $partnerData)
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900">{{ $partnerData['partner']->partner_name }}</div>
                                <span class="text-sm text-gray-500">{{ $partnerData['bookings_count'] }} bookings</span>
                            </div>
                            <div class="text-sm text-gray-600">₹{{ number_format($partnerData['revenue']) }} revenue</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="soft-glass-card rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('bookings.create') }}" class="w-full bg-blue-600 text-white rounded-lg p-3 hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>
                            New Booking
                        </a>
                        <a href="{{ route('owner.staff.index') }}" class="w-full bg-green-600 text-white rounded-lg p-3 hover:bg-green-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-users mr-2"></i>
                            Manage Staff
                        </a>
                        <a href="{{ route('owner.attendance.index') }}" class="w-full bg-orange-600 text-white rounded-lg p-3 hover:bg-orange-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Staff Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-refresh data every 30 seconds
setInterval(function() {
    // You can implement AJAX calls here to refresh specific sections
    console.log('Refreshing property data...');
}, 30000);

// Real-time updates for critical data
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for interactive elements
    const taskItems = document.querySelectorAll('[class*="task-priority-"]');
    taskItems.forEach(item => {
        item.addEventListener('click', function() {
            // Implement task detail modal or navigation
            console.log('Task clicked:', this);
        });
    });
});
</script>
@endpush
@endsection
