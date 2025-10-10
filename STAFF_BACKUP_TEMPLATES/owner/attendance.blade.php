@extends('layouts.app')

@section('title', 'Staff Attendance Overview')

@push('styles')
<style>
    .soft-header-gradient {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }
    .soft-glass-card {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .modern-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Header -->
    <header class="soft-header-gradient text-slate-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-white bg-opacity-10"></div>
        <div class="relative px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                        <i class="fas fa-calendar-check text-teal-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Staff Attendance Overview</h1>
                        <p class="text-sm text-slate-700">Monitor your staff attendance and working hours</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                        <i class="fas fa-arrow-left text-pink-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Back to Dashboard</span>
                    </a>
                    <a href="{{ route('owner.leave-requests.index') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                        <i class="fas fa-calendar-times text-orange-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Leave Requests</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card success rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Present Days</p>
                        <p class="text-3xl font-bold">{{ $attendanceStats['present_days'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card warning rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Absent Days</p>
                        <p class="text-3xl font-bold">{{ $attendanceStats['absent_days'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-times text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card info rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Total Hours</p>
                        <p class="text-3xl font-bold">{{ $attendanceStats['total_hours'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Attendance %</p>
                        <p class="text-3xl font-bold">{{ $attendanceStats['attendance_percentage'] }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-percentage text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="modern-card rounded-2xl overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <h3 class="text-lg font-bold text-gray-900">Today's Attendance</h3>
                <p class="text-sm text-gray-600">{{ today()->format('M d, Y') }} - {{ $todaysAttendance->count() }} staff members</p>
            </div>
            
            @if($todaysAttendance->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($todaysAttendance as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                        <span class="text-white font-semibold text-sm">
                                            {{ substr($record->staffAssignment->user->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $record->staffAssignment->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $record->staffAssignment->user->mobile_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->staffAssignment->property->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'present' => 'bg-green-100 text-green-800',
                                        'absent' => 'bg-red-100 text-red-800',
                                        'late' => 'bg-yellow-100 text-yellow-800',
                                        'half_day' => 'bg-blue-100 text-blue-800',
                                        'leave' => 'bg-purple-100 text-purple-800'
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->hours_worked ? number_format($record->hours_worked, 1) . 'h' : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-day text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Attendance Today</h3>
                <p class="text-gray-500">No attendance records found for today.</p>
            </div>
            @endif
        </div>

        <!-- Staff Members -->
        <div class="modern-card rounded-2xl overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <h3 class="text-lg font-bold text-gray-900">Staff Members</h3>
                <p class="text-sm text-gray-600">{{ $staffAssignments->count() }} staff members across your properties</p>
            </div>
            
            @if($staffAssignments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach($staffAssignments as $assignment)
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-3">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center mr-4">
                            <span class="text-white font-semibold">
                                {{ substr($assignment->user->name, 0, 2) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $assignment->user->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $assignment->role->name ?? 'Staff' }}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 mb-3">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-building text-gray-400 mr-2"></i>
                            <span>{{ $assignment->property->name }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                            <span>{{ $assignment->user->mobile_number }}</span>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <a href="{{ route('owner.attendance.staff', $assignment->uuid) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View Attendance <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Staff Members</h3>
                <p class="text-gray-500">You haven't assigned any staff members to your properties yet.</p>
            </div>
            @endif
        </div>

        <!-- Pending Leave Requests -->
        @if($pendingLeaveRequests->count() > 0)
        <div class="modern-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Pending Leave Requests</h3>
                        <p class="text-sm text-gray-600">{{ $pendingLeaveRequests->count() }} requests awaiting your approval</p>
                    </div>
                    <a href="{{ route('owner.leave-requests.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($pendingLeaveRequests->take(5) as $request)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-orange-500 to-red-600 flex items-center justify-center mr-4">
                                <span class="text-white font-semibold text-sm">
                                    {{ substr($request->staffAssignment->user->name, 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $request->staffAssignment->user->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $request->staffAssignment->property->name }}</p>
                            </div>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Leave Type:</span>
                            <div class="font-medium text-gray-900">{{ ucfirst($request->leave_type) }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">Duration:</span>
                            <div class="font-medium text-gray-900">
                                {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}
                                ({{ $request->start_date->diffInDays($request->end_date) + 1 }} days)
                            </div>
                        </div>
                    </div>
                    @if($request->reason)
                    <div class="mt-3 text-sm">
                        <span class="text-gray-500">Reason:</span>
                        <div class="text-gray-900 mt-1">{{ $request->reason }}</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
