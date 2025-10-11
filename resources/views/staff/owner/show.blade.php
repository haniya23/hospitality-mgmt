@extends('layouts.app')

@section('title', $staff->user->name . ' - Staff Details')
@section('page-title', 'Staff Details')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('owner.staff.index') }}" class="hover:text-blue-600 transition-colors">Staff</a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-700 font-medium">{{ $staff->user->name }}</span>
        </nav>
    </div>

    <!-- Staff Header Card -->
    <div class="bg-gradient-to-br from-white/95 to-blue-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-4 sm:p-6 border border-white/20 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl {{ $staff->getRoleBadgeColor() }} flex items-center justify-center shadow-lg text-2xl sm:text-3xl font-bold">
                    {{ substr($staff->user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $staff->user->name }}</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">{{ $staff->job_title ?? ucfirst($staff->staff_role) }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $staff->getRoleBadgeColor() }}">
                            {{ ucfirst($staff->staff_role) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $staff->getStatusBadgeColor() }}">
                            {{ ucfirst(str_replace('_', ' ', $staff->status)) }}
                        </span>
                        @if($staff->department)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" 
                                style="background-color: {{ $staff->department->color }}20; color: {{ $staff->department->color }};">
                                <i class="{{ $staff->department->icon }} mr-1"></i> {{ $staff->department->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('owner.staff.edit', $staff) }}" 
                    class="inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-semibold transition-all shadow-lg text-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('owner.staff.index') }}" 
                    class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-semibold transition-all shadow-sm text-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-6 mb-6">
        <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tasks text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['total_tasks'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Total Tasks</div>
        </div>

        <div class="bg-gradient-to-br from-white to-green-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $stats['completed_tasks'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Completed</div>
        </div>

        <div class="bg-gradient-to-br from-white to-yellow-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-yellow-600">{{ $stats['pending_tasks'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Pending</div>
        </div>

        <div class="bg-gradient-to-br from-white to-purple-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-percentage text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-purple-600">{{ number_format($stats['completion_rate'], 0) }}%</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Completion</div>
        </div>

        <div class="bg-gradient-to-br from-white to-indigo-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-indigo-600">{{ $stats['subordinates_count'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Team Members</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gradient-to-br from-white/95 to-gray-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-id-card text-blue-600 mr-2"></i>
                    Profile Information
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl {{ $staff->getRoleBadgeColor() }} flex items-center justify-center text-4xl sm:text-5xl font-bold shadow-lg">
                            {{ substr($staff->user->name, 0, 1) }}
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $staff->user->email }}</p>
                        </div>

                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Mobile</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $staff->user->mobile_number }}</p>
                        </div>

                        @if($staff->phone)
                            <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Phone</p>
                                <p class="font-medium text-gray-900 text-sm">{{ $staff->phone }}</p>
                            </div>
                        @endif

                        @if($staff->emergency_contact)
                            <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Emergency Contact</p>
                                <p class="font-medium text-gray-900 text-sm">{{ $staff->emergency_contact }}</p>
                            </div>
                        @endif

                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Property</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $staff->property->name }}</p>
                        </div>

                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Employment Type</p>
                            <p class="font-medium text-gray-900 text-sm">{{ ucfirst(str_replace('_', ' ', $staff->employment_type)) }}</p>
                        </div>

                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Join Date</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $staff->join_date->format('M d, Y') }}</p>
                        </div>

                        @if($staff->supervisor)
                            <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Reports To</p>
                                <p class="font-medium text-gray-900 text-sm">{{ $staff->supervisor->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($staff->supervisor->staff_role) }}</p>
                            </div>
                        @endif

                        @if($staff->notes)
                            <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Notes</p>
                                <p class="text-sm text-gray-700">{{ $staff->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($staff->subordinates->count() > 0)
                <div class="bg-gradient-to-br from-white/95 to-indigo-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-users text-indigo-600 mr-2"></i>
                        Team Members ({{ $staff->subordinates->count() }})
                    </h2>
                    <div class="space-y-3">
                        @foreach($staff->subordinates as $subordinate)
                            <div class="flex items-center bg-white/50 rounded-xl p-3 border border-white/30">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold shadow-sm">
                                    {{ substr($subordinate->user->name, 0, 1) }}
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $subordinate->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $subordinate->job_title ?? ucfirst($subordinate->staff_role) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Activity & Tasks -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Tasks -->
            <div class="bg-gradient-to-br from-white/95 to-purple-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                    Recent Tasks
                </h2>
                <div class="space-y-4">
                    @forelse($staff->assignedTasks as $task)
                        <div class="bg-white/70 rounded-xl p-4 border border-white/50 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $task->title }}</h3>
                                    @if($task->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                    @endif
                                    @if($task->location)
                                        <p class="text-xs text-gray-500 mt-2">
                                            <i class="fas fa-map-marker-alt mr-1"></i> {{ $task->location }}
                                        </p>
                                    @endif
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $task->getPriorityBadgeColor() }} flex-shrink-0">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                                <span class="text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $task->scheduled_at ? $task->scheduled_at->format('M d, h:i A') : 'Not scheduled' }}
                                </span>
                                <span class="px-3 py-1 rounded-full font-semibold {{ $task->getStatusBadgeColor() }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white/50 rounded-xl">
                            <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">No tasks assigned yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Attendance History -->
            <div class="bg-gradient-to-br from-white/95 to-green-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-calendar-check text-green-600 mr-2"></i>
                    Recent Attendance
                </h2>
                <div class="space-y-3">
                    @forelse($staff->attendance as $attendance)
                        <div class="flex items-center justify-between bg-white/70 rounded-xl p-4 border border-white/50">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $attendance->date->format('M d, Y (D)') }}</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    <i class="fas fa-sign-in-alt mr-1"></i>
                                    {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') : 'N/A' }}
                                    <i class="fas fa-sign-out-alt ml-2 mr-1"></i>
                                    {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') : 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $attendance->getStatusBadgeColor() }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                                @if($attendance->hours_worked)
                                    <p class="text-xs text-gray-500 mt-1">{{ number_format($attendance->hours_worked, 1) }}h</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white/50 rounded-xl">
                            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">No attendance records yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Leave Requests -->
            <div class="bg-gradient-to-br from-white/95 to-amber-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-calendar-alt text-amber-600 mr-2"></i>
                    Recent Leave Requests
                </h2>
                <div class="space-y-3">
                    @forelse($staff->leaveRequests as $leave)
                        <div class="bg-white/70 rounded-xl p-4 border border-white/50">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-start gap-2">
                                    <i class="{{ $leave->getLeaveTypeIcon() }} text-amber-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ ucfirst($leave->leave_type) }} Leave</p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                            <span class="text-gray-500">({{ $leave->total_days }} days)</span>
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">{{ Str::limit($leave->reason, 80) }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $leave->getStatusBadgeColor() }} flex-shrink-0">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white/50 rounded-xl">
                            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">No leave requests yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
