@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Team</h1>
        <p class="mt-2 text-gray-600">Manage your team members and their performance</p>
    </div>

    {{-- Team Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Team Size</div>
            <div class="text-2xl font-bold mt-2">{{ $team->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Tasks</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">{{ $team->sum('assigned_tasks_count') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Completed</div>
            <div class="text-2xl font-bold text-green-600 mt-2">{{ $team->sum('completed_tasks_count') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $team->sum('pending_tasks_count') }}</div>
        </div>
    </div>

    {{-- Team Members --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff Member</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Tasks</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pending</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completion Rate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($team as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <span class="text-green-600 font-semibold">{{ substr($member->user->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $member->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $member->job_title ?? 'Staff Member' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($member->department)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                    style="background-color: {{ $member->department->color }}20; color: {{ $member->department->color }};">
                                    <i class="{{ $member->department->icon }} mr-1"></i> {{ $member->department->name }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $member->assigned_tasks_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">{{ $member->completed_tasks_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-semibold">{{ $member->pending_tasks_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $member->completion_rate }}%"></div>
                                </div>
                                <span class="text-sm font-semibold">{{ number_format($member->completion_rate, 0) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $member->getStatusBadgeColor() }}">
                                {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

