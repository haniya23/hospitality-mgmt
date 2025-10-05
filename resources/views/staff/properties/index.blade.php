@extends('layouts.staff')

@section('title', 'My Properties')

@section('staff-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Properties</h1>
            <p class="text-gray-600">Manage your assigned properties and view detailed information</p>
        </div>
        <div class="text-sm text-gray-500">
            <i class="fas fa-building mr-1"></i>
            {{ $assignedProperties->count() }} Properties Assigned
        </div>
    </div>

    @if($assignedProperties->isEmpty())
    <!-- Empty State -->
    <div class="text-center py-12">
        <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-building text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Properties Assigned</h3>
        <p class="text-gray-500 mb-6">You haven't been assigned to any properties yet. Contact your manager for property assignments.</p>
        <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>
    @else
    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($assignedProperties as $propertyData)
        @php
            $property = $propertyData['property'] ?? null;
            $assignment = $propertyData['assignment'] ?? null;
            $role = $propertyData['role'] ?? null;
            $stats = $propertyData['stats'] ?? [];
            
            if (!$property) continue;
        @endphp
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <!-- Property Header -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $property->name ?? 'Unnamed Property' }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $property->category->name ?? 'Property' }}</p>
                        <div class="flex items-center mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if(($property->status ?? '') === 'active') bg-green-100 text-green-800
                                @elseif(($property->status ?? '') === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($property->status ?? 'unknown') }}
                            </span>
                            @if($role)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $role->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property Stats -->
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['total_accommodations'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Accommodations</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['active_checklists'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Active Checklists</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['todays_bookings'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Today's Bookings</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $stats['upcoming_bookings'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Upcoming (7 days)</div>
                    </div>
                </div>

                @if(($stats['active_checklist_executions'] ?? 0) > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        <span class="text-sm font-medium text-yellow-800">
                            {{ $stats['active_checklist_executions'] }} checklist(s) in progress
                        </span>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <a href="{{ route('staff.properties.show', $property) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-eye mr-2"></i>
                        View Details
                    </a>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('staff.properties.accommodations', $property) }}" 
                           class="flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <i class="fas fa-bed mr-1"></i>
                            Rooms
                        </a>
                        <a href="{{ route('staff.properties.checklists', $property) }}" 
                           class="flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <i class="fas fa-clipboard-check mr-1"></i>
                            Checklists
                        </a>
                    </div>
                </div>
            </div>

            <!-- Property Footer -->
            <div class="px-6 py-3 bg-gray-50 rounded-b-xl">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        {{ $property->location->city->name ?? 'Location not set' }}
                    </span>
                    @if($assignment && $assignment->start_date)
                    <span>
                        <i class="fas fa-calendar mr-1"></i>
                        Assigned {{ $assignment->start_date->format('M d, Y') }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('staff.bookings') }}" 
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                <div class="h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">View All Bookings</div>
                    <div class="text-sm text-gray-500">Manage reservations across all properties</div>
                </div>
            </a>
            
            <a href="{{ route('staff.tasks') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                <div class="h-10 w-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-tasks text-white"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">My Tasks</div>
                    <div class="text-sm text-gray-500">View and manage assigned tasks</div>
                </div>
            </a>
            
            <a href="{{ route('staff.guest-service.index') }}" 
               class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                <div class="h-10 w-10 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-concierge-bell text-white"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">Guest Services</div>
                    <div class="text-sm text-gray-500">Handle check-ins and check-outs</div>
                </div>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
