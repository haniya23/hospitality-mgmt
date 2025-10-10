@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Dashboard</h1>
        <p class="mt-2 text-gray-600">Welcome back, {{ $staff->user->name }}!</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-tasks text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">My Tasks</p>
                    <p class="text-2xl font-bold">{{ $stats['pending_tasks'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Completed Today</p>
                    <p class="text-2xl font-bold">{{ $stats['completed_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Overdue</p>
                    <p class="text-2xl font-bold">{{ $stats['overdue_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Completion Rate</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['completion_rate'], 0) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Attendance Card --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Today's Attendance</h2>
            </div>
            <div class="p-6">
                @if($todayAttendance)
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Check In:</span>
                            <span class="font-semibold">
                                @if($todayAttendance->check_in_time)
                                    {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('h:i A') }}
                                @else
                                    Not yet
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Check Out:</span>
                            <span class="font-semibold">
                                @if($todayAttendance->check_out_time)
                                    {{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('h:i A') }}
                                @else
                                    Not yet
                                @endif
                            </span>
                        </div>
                        @if($todayAttendance->hours_worked)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Hours Worked:</span>
                                <span class="font-semibold">{{ number_format($todayAttendance->hours_worked, 2) }}h</span>
                            </div>
                        @endif
                        <div class="pt-4 border-t">
                            @if(!$todayAttendance->check_in_time)
                                <form action="{{ route('staff.attendance.check-in') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                                        <i class="fas fa-sign-in-alt mr-2"></i> Check In
                                    </button>
                                </form>
                            @elseif(!$todayAttendance->check_out_time)
                                <form action="{{ route('staff.attendance.check-out') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Check Out
                                    </button>
                                </form>
                            @else
                                <div class="text-center text-green-600 font-semibold">
                                    <i class="fas fa-check-circle mr-2"></i> All done for today!
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <form action="{{ route('staff.attendance.check-in') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700">
                            <i class="fas fa-sign-in-alt mr-2"></i> Check In Now
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Today's Tasks --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold">Today's Tasks ({{ $todaysTasks->count() }})</h2>
                <a href="{{ route('staff.my-tasks') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            <div class="p-6">
                @forelse($todaysTasks as $task)
                    <div class="py-4 border-b border-gray-100 last:border-0">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="font-medium">{{ $task->title }}</h3>
                                @if($task->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                @endif
                                @if($task->location)
                                    <p class="text-xs text-gray-400 mt-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $task->location }}
                                    </p>
                                @endif
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $task->getPriorityBadgeColor() }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $task->getStatusBadgeColor() }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                            <div class="flex gap-2">
                                @if($task->status === 'assigned')
                                    <form action="{{ route('staff.tasks.start', $task) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                            <i class="fas fa-play mr-1"></i> Start
                                        </button>
                                    </form>
                                @elseif($task->status === 'in_progress')
                                    <a href="{{ route('staff.tasks.show', $task) }}" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i> Complete
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No tasks scheduled for today</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Overdue Tasks Alert --}}
    @if($overdueTasks->count() > 0)
        <div class="mt-8 bg-red-50 border-l-4 border-red-600 p-6 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-600 text-xl mt-1"></i>
                <div class="ml-4">
                    <h3 class="text-red-800 font-semibold">{{ $overdueTasks->count() }} Overdue Task(s)</h3>
                    <p class="text-red-700 text-sm mt-1">Please complete these tasks as soon as possible.</p>
                    <a href="{{ route('staff.my-tasks') }}?status=overdue" class="text-red-800 underline text-sm mt-2 inline-block">
                        View overdue tasks →
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

