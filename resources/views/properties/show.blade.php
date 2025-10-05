@extends('layouts.app')

@section('title', $property->name . ' - Property Overview')

@push('styles')
<style>
    .property-header-gradient {
        background: linear-gradient(135deg, #84cc16 0%, #65a30d 25%, #4d7c0f 50%, #365314 75%, #1a2e05 100%);
        box-shadow: 
            inset 8px 8px 16px rgba(0, 0, 0, 0.2),
            inset -8px -8px 16px rgba(255, 255, 255, 0.1),
            0 8px 32px rgba(132, 204, 22, 0.3);
    }
    .soft-glass-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: none;
        box-shadow: 
            8px 8px 16px rgba(0, 0, 0, 0.1),
            -8px -8px 16px rgba(255, 255, 255, 0.8),
            inset 1px 1px 2px rgba(255, 255, 255, 0.5),
            inset -1px -1px 2px rgba(0, 0, 0, 0.05);
        border-radius: 1rem;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        box-shadow: 
            8px 8px 16px rgba(0, 0, 0, 0.2),
            -8px -8px 16px rgba(255, 255, 255, 0.1),
            inset 2px 2px 4px rgba(255, 255, 255, 0.2),
            inset -2px -2px 4px rgba(0, 0, 0, 0.1);
    }
    .stat-card.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        box-shadow: 
            8px 8px 16px rgba(17, 153, 142, 0.3),
            -8px -8px 16px rgba(255, 255, 255, 0.1),
            inset 2px 2px 4px rgba(255, 255, 255, 0.2),
            inset -2px -2px 4px rgba(0, 0, 0, 0.1);
    }
    .stat-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        box-shadow: 
            8px 8px 16px rgba(240, 147, 251, 0.3),
            -8px -8px 16px rgba(255, 255, 255, 0.1),
            inset 2px 2px 4px rgba(255, 255, 255, 0.2),
            inset -2px -2px 4px rgba(0, 0, 0, 0.1);
    }
    .stat-card.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        box-shadow: 
            8px 8px 16px rgba(79, 172, 254, 0.3),
            -8px -8px 16px rgba(255, 255, 255, 0.1),
            inset 2px 2px 4px rgba(255, 255, 255, 0.2),
            inset -2px -2px 4px rgba(0, 0, 0, 0.1);
    }
    .stat-card.danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        box-shadow: 
            8px 8px 16px rgba(255, 107, 107, 0.3),
            -8px -8px 16px rgba(255, 255, 255, 0.1),
            inset 2px 2px 4px rgba(255, 255, 255, 0.2),
            inset -2px -2px 4px rgba(0, 0, 0, 0.1);
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
    <header class="relative overflow-hidden" style="background: linear-gradient(135deg, #e8e8e8 0%, #f0f0f0 50%, #e8e8e8 100%); min-height: 200px;">
        <!-- Subtle Background Pattern -->
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 left-0 w-96 h-96 rounded-full filter blur-3xl" style="background: radial-gradient(circle, rgba(200, 200, 200, 0.3), transparent);"></div>
            <div class="absolute top-0 right-0 w-96 h-96 rounded-full filter blur-3xl" style="background: radial-gradient(circle, rgba(180, 180, 180, 0.3), transparent);"></div>
            <div class="absolute bottom-0 left-1/2 w-96 h-96 rounded-full filter blur-3xl" style="background: radial-gradient(circle, rgba(190, 190, 190, 0.3), transparent);"></div>
        </div>
        
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-5px); }
            }
            .neumorphic {
                box-shadow: 8px 8px 16px rgba(163, 177, 198, 0.6), -8px -8px 16px rgba(255, 255, 255, 0.5);
            }
            .neumorphic-inset {
                box-shadow: inset 6px 6px 12px rgba(163, 177, 198, 0.5), inset -6px -6px 12px rgba(255, 255, 255, 0.7);
            }
            .neumorphic-hover:hover {
                box-shadow: 4px 4px 8px rgba(163, 177, 198, 0.6), -4px -4px 8px rgba(255, 255, 255, 0.5), inset 2px 2px 4px rgba(163, 177, 198, 0.2);
            }
        </style>
        
        <div class="relative px-4 py-6 sm:py-8 md:py-10 lg:py-12">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col space-y-6 lg:space-y-0 lg:flex-row lg:items-center lg:justify-between">
                    <!-- Left Section -->
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-start space-y-4 sm:space-y-0 mb-6">
                            <!-- Icon Container - Neumorphic -->
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl flex items-center justify-center sm:mr-4 flex-shrink-0 neumorphic" style="background: linear-gradient(145deg, #f0f0f0, #e0e0e0); animation: float 3s ease-in-out infinite;">
                                <i class="fas fa-building text-2xl sm:text-3xl" style="color: #7c9250; filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.2));"></i>
                            </div>
                            
                            <div class="flex-1">
                                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2 sm:mb-3" style="color: #4a5568; text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.8), -1px -1px 2px rgba(163, 177, 198, 0.4);">
                                    {{ $property->name }}
                                </h1>
                                <div class="flex items-center" style="color: #718096;">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-2 neumorphic-inset" style="background: #e8e8e8;">
                                        <i class="fas fa-map-marker-alt text-sm" style="color: #7c9250;"></i>
                                    </div>
                                    <span class="font-medium text-sm sm:text-base">{{ $property->location->city->name ?? 'Location not set' }}, {{ $property->location->city->district->state->name ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Badges - Neumorphic -->
                        <div class="flex flex-wrap gap-3">
                            <span class="inline-flex items-center px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-semibold neumorphic" style="background: linear-gradient(145deg, #f0f0f0, #e0e0e0); color: #4a5568;">
                                <div class="w-3 h-3 rounded-full mr-2 animate-pulse neumorphic-inset" style="background: radial-gradient(circle, #7c9250, #6b7f47); box-shadow: 0 0 8px rgba(124, 146, 80, 0.6), inset 1px 1px 2px rgba(0, 0, 0, 0.2);"></div>
                                {{ ucfirst($property->status) }}
                            </span>
                            <span class="inline-flex items-center px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-semibold neumorphic" style="background: linear-gradient(145deg, #f0f0f0, #e0e0e0); color: #4a5568;">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mr-2 neumorphic-inset" style="background: #e8e8e8;">
                                    <i class="fas fa-home text-xs" style="color: #7c9250;"></i>
                                </div>
                                {{ $property->category->name ?? 'Property Category' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Right Section - Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 lg:ml-6">
                        <a href="{{ route('properties.edit', $property) }}" class="rounded-2xl px-4 sm:px-6 py-3 transition-all flex items-center justify-center group neumorphic neumorphic-hover" style="background: linear-gradient(145deg, #f0f0f0, #e0e0e0);">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-2 neumorphic-inset group-hover:shadow-none transition-all" style="background: #e8e8e8;">
                                <i class="fas fa-edit text-sm" style="color: #7c9250;"></i>
                            </div>
                            <span class="font-semibold text-sm sm:text-base" style="color: #4a5568;">Edit Property</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 sm:px-6 py-3 transition-all flex items-center justify-center group neumorphic neumorphic-hover" style="background: linear-gradient(145deg, #f0f0f0, #e0e0e0);">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-2 neumorphic-inset group-hover:shadow-none transition-all" style="background: #e8e8e8;">
                                <i class="fas fa-arrow-left text-sm" style="color: #7c9250;"></i>
                            </div>
                            <span class="font-semibold text-sm sm:text-base" style="color: #4a5568;">Back to Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Snapshot Summary -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            <div class="stat-card success rounded-2xl p-4 md:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Occupancy</p>
                        <p class="text-2xl md:text-3xl font-bold">{{ $occupiedAccommodations }} / {{ $totalAccommodations }}</p>
                        <p class="text-xs md:text-sm text-white/70">{{ $totalAccommodations > 0 ? round(($occupiedAccommodations / $totalAccommodations) * 100, 1) : 0 }}% occupied</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-bed text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card info rounded-2xl p-4 md:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Guests Onsite</p>
                        <p class="text-2xl md:text-3xl font-bold">{{ $currentGuests->count() }}</p>
                        <p class="text-xs md:text-sm text-white/70">{{ $currentGuests->sum('adults') + $currentGuests->sum('children') }} total guests</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card warning rounded-2xl p-4 md:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Today's Revenue</p>
                        <p class="text-2xl md:text-3xl font-bold">₹{{ number_format($todaysRevenue) }}</p>
                        <p class="text-xs md:text-sm text-white/70">{{ $monthlyStats['revenue'] > 0 ? round(($todaysRevenue / $monthlyStats['revenue']) * 100, 1) : 0 }}% of monthly</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-rupee-sign text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card rounded-2xl p-4 md:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Staff On Duty</p>
                        <p class="text-2xl md:text-3xl font-bold">{{ $staffOnDuty->count() }}</p>
                        <p class="text-xs md:text-sm text-white/70">{{ $pendingTasks->count() }} pending tasks</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-tie text-lg md:text-xl"></i>
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
                        <h3 class="text-xl font-bold text-slate-800">Guests & Bookings Status</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-lg">Current</button>
                            <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Check-ins</button>
                            <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Check-outs</button>
                        </div>
                    </div>
                    
                    <!-- Current Guests -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-slate-700 mb-4">Current Guests ({{ $currentGuests->count() }})</h4>
                        @if($currentGuests->count() > 0)
                        <div class="space-y-3">
                            @foreach($currentGuests as $guest)
                            <div class="bg-gradient-to-r from-white to-blue-50 border-none rounded-lg p-4 hover:shadow-lg transition-all duration-300" style="box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.1), -6px -6px 12px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 flex items-center justify-center mr-3 shadow-md">
                                            <span class="text-white font-semibold text-sm">{{ substr($guest->guest->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-800">{{ $guest->guest->name }}</div>
                                            <div class="text-sm text-slate-600">{{ $guest->propertyAccommodation->name }} • {{ $guest->adults + $guest->children }} guests</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-slate-500 font-medium">Check-out</div>
                                        <div class="font-semibold text-slate-800">{{ $guest->check_out_date->format('M d') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-slate-600">
                            <i class="fas fa-users text-4xl mb-4 text-slate-400"></i>
                            <p class="font-medium">No guests currently checked in</p>
                        </div>
                        @endif
                    </div>

                    <!-- Next Check-ins -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-slate-700 mb-4">Next Check-ins ({{ $nextCheckins->count() }})</h4>
                        @if($nextCheckins->count() > 0)
                        <div class="space-y-3">
                            @foreach($nextCheckins as $checkin)
                            <div class="bg-gradient-to-r from-white to-green-50 border-none rounded-lg p-4 hover:shadow-lg transition-all duration-300" style="box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.1), -6px -6px 12px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center mr-3 shadow-md">
                                            <span class="text-white font-semibold text-sm">{{ substr($checkin->guest->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-800">{{ $checkin->guest->name }}</div>
                                            <div class="text-sm text-slate-600">{{ $checkin->propertyAccommodation->name }} • {{ $checkin->adults + $checkin->children }} guests</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-slate-500 font-medium">Check-in</div>
                                        <div class="font-semibold text-slate-800">{{ $checkin->check_in_date->format('M d, h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-slate-600">
                            <p class="font-medium">No check-ins scheduled for today</p>
                        </div>
                        @endif
                    </div>

                    <!-- Next Check-outs -->
                    <div>
                        <h4 class="text-lg font-semibold text-slate-700 mb-4">Next Check-outs ({{ $nextCheckouts->count() }})</h4>
                        @if($nextCheckouts->count() > 0)
                        <div class="space-y-3">
                            @foreach($nextCheckouts as $checkout)
                            <div class="bg-gradient-to-r from-white to-orange-50 border-none rounded-lg p-4 hover:shadow-lg transition-all duration-300" style="box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.1), -6px -6px 12px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-orange-500 to-red-500 flex items-center justify-center mr-3 shadow-md">
                                            <span class="text-white font-semibold text-sm">{{ substr($checkout->guest->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-800">{{ $checkout->guest->name }}</div>
                                            <div class="text-sm text-slate-600">{{ $checkout->propertyAccommodation->name }} • {{ $checkout->adults + $checkout->children }} guests</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-slate-500 font-medium">Check-out</div>
                                        <div class="font-semibold text-slate-800">{{ $checkout->check_out_date->format('M d, h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-slate-600">
                            <p class="font-medium">No check-outs scheduled for today</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Staff & Tasks Dashboard -->
                <div class="soft-glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-slate-800">Staff & Operations</h3>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Assign Task
                        </button>
                    </div>
                    
                    <!-- Active Staff -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-slate-700 mb-4">Active Staff ({{ $staffOnDuty->count() }})</h4>
                        @if($staffOnDuty->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($staffOnDuty as $staff)
                            <div class="bg-gradient-to-r from-white to-purple-50 border-none rounded-lg p-4 hover:shadow-lg transition-all duration-300" style="box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.1), -6px -6px 12px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 flex items-center justify-center mr-3 shadow-md">
                                        <span class="text-white font-semibold text-sm">{{ substr($staff->user->name, 0, 2) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-slate-800">{{ $staff->user->name }}</div>
                                        <div class="text-sm text-slate-600">{{ $staff->role->name ?? 'Staff' }}</div>
                                    </div>
                                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-slate-600">
                            <i class="fas fa-user-tie text-4xl mb-4 text-slate-400"></i>
                            <p class="font-medium">No staff assigned to this property</p>
                        </div>
                        @endif
                    </div>

                    <!-- Pending Tasks -->
                    <div>
                        <h4 class="text-lg font-semibold text-slate-700 mb-4">Pending Tasks ({{ $pendingTasks->count() }})</h4>
                        @if($pendingTasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($pendingTasks->take(5) as $task)
                            <div class="bg-gradient-to-r from-white to-yellow-50 border-none rounded-lg p-4 task-priority-{{ $task->priority }} hover:shadow-lg transition-all duration-300" style="box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.1), -6px -6px 12px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="font-semibold text-slate-800">{{ $task->task_name }}</div>
                                        <div class="text-sm text-slate-600">{{ $task->description }}</div>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($task->task_type) }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-slate-500 font-medium">{{ $task->scheduled_at ? $task->scheduled_at->format('M d, h:i A') : 'No schedule' }}</div>
                                        <div class="text-sm font-semibold text-slate-800">{{ $task->staffAssignment->user->name ?? 'Unassigned' }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-slate-600">
                            <i class="fas fa-tasks text-4xl mb-4 text-slate-400"></i>
                            <p class="font-medium">No pending tasks</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Accommodation Status -->
                <div class="soft-glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-slate-800">Accommodation Status</h3>
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
                        <div class="bg-gradient-to-r from-white to-indigo-50 border-none rounded-lg p-4 hover:shadow-lg transition-all duration-300" style="box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.1), -6px -6px 12px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="font-semibold text-slate-800">{{ $accommodation->name }}</h5>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $currentReservation ? 'room-status-occupied' : 'room-status-vacant' }}">
                                    {{ $currentReservation ? 'Occupied' : 'Vacant' }}
                                </span>
                            </div>
                            @if($currentReservation)
                            <div class="text-sm text-slate-600 mb-2">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2 text-slate-500"></i>
                                    <span class="font-medium">{{ $currentReservation->guest->name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2 text-slate-500"></i>
                                    <span>Check-out: {{ $currentReservation->check_out_date->format('M d') }}</span>
                                </div>
                            </div>
                            @else
                            <div class="text-sm text-slate-600">
                                <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                <span class="font-medium">Available for booking</span>
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
                    
                    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-1 gap-4">
                        <div class="bg-white border-none rounded-lg p-4" style="box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1), -4px -4px 8px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
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

                        <div class="bg-white border-none rounded-lg p-4" style="box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1), -4px -4px 8px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
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

                        <div class="bg-white border-none rounded-lg p-4" style="box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1), -4px -4px 8px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
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

                        <div class="bg-white border-none rounded-lg p-4" style="box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1), -4px -4px 8px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
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
                        <div class="bg-white border-none rounded-lg p-4" style="box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1), -4px -4px 8px rgba(255, 255, 255, 0.8), inset 1px 1px 2px rgba(255, 255, 255, 0.5), inset -1px -1px 2px rgba(0, 0, 0, 0.05);">
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
                    
                    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-1 gap-3">
                        <a href="{{ route('bookings.create') }}" class="w-full bg-blue-600 text-white rounded-lg p-3 hover:bg-blue-700 transition-colors flex items-center justify-center" style="box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2), -4px -4px 8px rgba(255, 255, 255, 0.1), inset 2px 2px 4px rgba(255, 255, 255, 0.2), inset -2px -2px 4px rgba(0, 0, 0, 0.1);">
                            <i class="fas fa-plus mr-2"></i>
                            New Booking
                        </a>
                        <a href="{{ route('owner.staff.index') }}" class="w-full bg-green-600 text-white rounded-lg p-3 hover:bg-green-700 transition-colors flex items-center justify-center" style="box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2), -4px -4px 8px rgba(255, 255, 255, 0.1), inset 2px 2px 4px rgba(255, 255, 255, 0.2), inset -2px -2px 4px rgba(0, 0, 0, 0.1);">
                            <i class="fas fa-users mr-2"></i>
                            Manage Staff
                        </a>
                        <a href="{{ route('owner.attendance.index') }}" class="w-full bg-orange-600 text-white rounded-lg p-3 hover:bg-orange-700 transition-colors flex items-center justify-center" style="box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2), -4px -4px 8px rgba(255, 255, 255, 0.1), inset 2px 2px 4px rgba(255, 255, 255, 0.2), inset -2px -2px 4px rgba(0, 0, 0, 0.1);">
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
