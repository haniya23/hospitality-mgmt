@extends('layouts.staff')

@section('title', 'Staff Assignments - ' . $property->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <div class="flex items-center">
                        <a href="{{ route('staff.properties.show', $property->uuid) }}" 
                           class="mr-3 p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Staff Assignments</h1>
                            <p class="text-sm sm:text-base text-gray-600">{{ $property->name }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('staff.properties.show', $property->uuid) }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Property
                    </a>
                </div>
            </div>
        </div>

        <!-- Staff Assignments List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Current Staff</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $staffAssignments->count() }} active staff members</p>
            </div>

            @if($staffAssignments->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Access</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasks</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($staffAssignments as $staffAssignment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-600">
                                                        {{ substr($staffAssignment['user']->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $staffAssignment['user']->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $staffAssignment['user']->mobile_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $staffAssignment['role']->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            @if($staffAssignment['assignment']->booking_access)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-calendar-check mr-1"></i>
                                                    Booking
                                                </span>
                                            @endif
                                            @if($staffAssignment['assignment']->guest_service_access)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    <i class="fas fa-concierge-bell mr-1"></i>
                                                    Guest Service
                                                </span>
                                            @endif
                                            @if(!$staffAssignment['assignment']->booking_access && !$staffAssignment['assignment']->guest_service_access)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    Basic Access
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="space-y-1">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Total:</span>
                                                <span class="font-medium">{{ $staffAssignment['stats']['total'] ?? 0 }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Completed:</span>
                                                <span class="font-medium text-green-600">{{ $staffAssignment['stats']['completed'] ?? 0 }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Pending:</span>
                                                <span class="font-medium text-orange-600">{{ $staffAssignment['stats']['pending'] ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-1">
                                                <div class="text-sm text-gray-900">{{ $staffAssignment['stats']['completion_rate'] ?? 0 }}%</div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" 
                                                         style="width: {{ $staffAssignment['stats']['completion_rate'] ?? 0 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $staffAssignment['assignment']->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($staffAssignment['assignment']->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden">
                    @foreach($staffAssignments as $staffAssignment)
                        <div class="border-b border-gray-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">
                                                {{ substr($staffAssignment['user']->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $staffAssignment['user']->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $staffAssignment['user']->mobile_number }}</div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $staffAssignment['assignment']->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($staffAssignment['assignment']->status) }}
                                </span>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $staffAssignment['role']->name }}
                                    </span>
                                </div>
                                
                                <div class="flex flex-wrap gap-2">
                                    @if($staffAssignment['assignment']->booking_access)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            Booking
                                        </span>
                                    @endif
                                    @if($staffAssignment['assignment']->guest_service_access)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-concierge-bell mr-1"></i>
                                            Guest Service
                                        </span>
                                    @endif
                                    @if(!$staffAssignment['assignment']->booking_access && !$staffAssignment['assignment']->guest_service_access)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Basic Access
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-600">Tasks Completed</div>
                                        <div class="font-medium">{{ $staffAssignment['stats']['completed'] ?? 0 }}/{{ $staffAssignment['stats']['total'] ?? 0 }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-600">Performance</div>
                                        <div class="font-medium">{{ $staffAssignment['stats']['completion_rate'] ?? 0 }}%</div>
                                    </div>
                                </div>
                                
                                @if(($staffAssignment['stats']['completion_rate'] ?? 0) > 0)
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ $staffAssignment['stats']['completion_rate'] ?? 0 }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <i class="fas fa-users text-4xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No staff assigned</h3>
                    <p class="mt-1 text-sm text-gray-500">This property doesn't have any active staff assignments.</p>
                </div>
            @endif
        </div>

        <!-- Summary Stats -->
        @if($staffAssignments->count() > 0)
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Total Staff</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $staffAssignments->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-tasks text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Total Tasks</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $staffAssignments->sum(function($assignment) { return $assignment['stats']['total_tasks'] ?? 0; }) }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-chart-line text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Avg Performance</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $staffAssignments->count() > 0 ? round($staffAssignments->avg(function($assignment) { return $assignment['stats']['completion_rate'] ?? 0; }), 1) : 0 }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Any additional JavaScript for staff assignments page can go here
</script>
@endpush
