@extends('layouts.staff')

@section('title', $property->name . ' - Property Details')

@section('staff-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('staff.properties') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $property->name }}</h1>
                    <p class="text-gray-600">{{ $property->category->name ?? 'Property' }} • {{ $assignment->role->name }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($property->status === 'active') bg-green-100 text-green-800
                @elseif($property->status === 'pending') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ ucfirst($property->status) }}
            </span>
            <div class="text-sm text-gray-500">
                <i class="fas fa-calendar mr-1"></i>
                Assigned {{ $assignment->start_date->format('M d, Y') }}
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bed text-blue-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_accommodations'] }}</div>
                    <div class="text-sm text-gray-500">Total Accommodations</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-green-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['active_checklists'] }}</div>
                    <div class="text-sm text-gray-500">Active Checklists</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="h-12 w-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sign-in-alt text-orange-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['todays_checkins'] }}</div>
                    <div class="text-sm text-gray-500">Today's Check-ins</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['upcoming_bookings'] }}</div>
                    <div class="text-sm text-gray-500">Upcoming Bookings</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Property Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Property Name</label>
                        <p class="text-sm text-gray-900">{{ $property->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <p class="text-sm text-gray-900">{{ $property->category->name ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($property->status === 'active') bg-green-100 text-green-800
                            @elseif($property->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($property->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Role</label>
                        <p class="text-sm text-gray-900">{{ $assignment->role->name }}</p>
                    </div>
                </div>
                
                @if($property->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <p class="text-sm text-gray-900">{{ $property->description }}</p>
                </div>
                @endif

                @if($property->location)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <div class="text-sm text-gray-900">
                        @if($property->location->address)
                            <p>{{ $property->location->address }}</p>
                        @endif
                        <p>{{ $property->location->city->name ?? '' }}{{ $property->location->state ? ', ' . $property->location->state->name : '' }}{{ $property->location->country ? ', ' . $property->location->country->name : '' }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                    <a href="{{ route('staff.bookings') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View All
                    </a>
                </div>
                
                @if($recentBookings->isEmpty())
                <div class="text-center py-8">
                    <i class="fas fa-calendar-alt text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500">No recent bookings found</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($recentBookings as $booking)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $booking->guest->name ?? 'Guest' }}</p>
                                <p class="text-xs text-gray-500">{{ $booking->propertyAccommodation->display_name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d') }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                                @elseif($booking->status === 'checked_out') bg-gray-100 text-gray-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('staff.properties.accommodations', $property) }}" 
                       class="w-full flex items-center justify-between p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-blue-600 mr-3"></i>
                            <span class="font-medium text-gray-900">View Accommodations</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('staff.properties.checklists', $property) }}" 
                       class="w-full flex items-center justify-between p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-check text-green-600 mr-3"></i>
                            <span class="font-medium text-gray-900">Manage Checklists</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('staff.properties.staff-assignments', $property) }}" 
                       class="w-full flex items-center justify-between p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-users text-purple-600 mr-3"></i>
                            <span class="font-medium text-gray-900">Staff Assignments</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </div>
            </div>

            <!-- Active Checklist Executions -->
            @if($activeChecklistExecutions->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Active Checklists</h3>
                <div class="space-y-3">
                    @foreach($activeChecklistExecutions as $execution)
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900">{{ $execution->cleaningChecklist->name }}</h4>
                            <span class="text-xs text-yellow-600 font-medium">In Progress</span>
                        </div>
                        <p class="text-xs text-gray-600 mb-2">Started by {{ $execution->staffAssignment->user->name }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">{{ $execution->started_at->format('M d, Y H:i') }}</span>
                            <a href="{{ route('staff.checklist.execute', $execution->uuid) }}" 
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Continue
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Assignment Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Assignment</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                        <p class="text-sm text-gray-900">{{ $assignment->role->name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Start Date</label>
                        <p class="text-sm text-gray-900">{{ $assignment->start_date->format('M d, Y') }}</p>
                    </div>
                    @if($assignment->end_date)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">End Date</label>
                        <p class="text-sm text-gray-900">{{ $assignment->end_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            @if($assignment->status === 'active') bg-green-100 text-green-800
                            @elseif($assignment->status === 'inactive') bg-gray-100 text-gray-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
