@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Attendance</h1>
        <p class="mt-2 text-gray-600">Track your check-in, check-out, and work hours</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Present Days</div>
            <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['present_days'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Late Days</div>
            <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['late_days'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Absent Days</div>
            <div class="text-2xl font-bold text-red-600 mt-2">{{ $stats['absent_days'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Hours</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($stats['total_hours'], 1) }}h</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Today's Attendance --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                <h2 class="text-lg font-semibold text-blue-900">Today's Attendance</h2>
                <p class="text-sm text-blue-700">{{ now()->format('l, M d, Y') }}</p>
            </div>
            <div class="p-6">
                @if($todayAttendance)
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-gray-600">Check In:</span>
                            <span class="font-semibold {{ $todayAttendance->check_in_time ? 'text-green-600' : 'text-gray-400' }}">
                                @if($todayAttendance->check_in_time)
                                    {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('h:i A') }}
                                @else
                                    Not checked in
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-gray-600">Check Out:</span>
                            <span class="font-semibold {{ $todayAttendance->check_out_time ? 'text-blue-600' : 'text-gray-400' }}">
                                @if($todayAttendance->check_out_time)
                                    {{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('h:i A') }}
                                @else
                                    Not checked out
                                @endif
                            </span>
                        </div>
                        @if($todayAttendance->hours_worked)
                            <div class="flex justify-between items-center pb-3 border-b">
                                <span class="text-gray-600">Hours Worked:</span>
                                <span class="font-semibold text-purple-600">{{ number_format($todayAttendance->hours_worked, 2) }}h</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $todayAttendance->getStatusBadgeColor() }}">
                                {{ ucfirst($todayAttendance->status) }}
                            </span>
                        </div>
                        
                        {{-- Actions --}}
                        <div class="pt-4 border-t space-y-2">
                            @if(!$todayAttendance->check_in_time)
                                <form action="{{ route('staff.attendance.check-in') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold">
                                        <i class="fas fa-sign-in-alt mr-2"></i> Check In
                                    </button>
                                </form>
                            @elseif(!$todayAttendance->check_out_time)
                                <form action="{{ route('staff.attendance.check-out') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Check Out
                                    </button>
                                </form>
                            @else
                                <div class="text-center text-green-600 font-semibold py-3">
                                    <i class="fas fa-check-circle mr-2"></i> All done for today!
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-calendar-day text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500 mb-4">You haven't checked in today</p>
                        <form action="{{ route('staff.attendance.check-in') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold">
                                <i class="fas fa-sign-in-alt mr-2"></i> Check In Now
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Attendance History --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">This Month's Attendance</h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Check In</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Check Out</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Hours</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($attendance as $record)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $record->date->format('M d, D') }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $record->hours_worked ? number_format($record->hours_worked, 2) . 'h' : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $record->getStatusBadgeColor() }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        No attendance records for this month
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Leave Request Button --}}
    <div class="mt-8">
        <a href="{{ route('staff.leave-requests') }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700">
            <i class="fas fa-calendar-plus mr-2"></i> Request Leave
        </a>
    </div>
</div>
@endsection

