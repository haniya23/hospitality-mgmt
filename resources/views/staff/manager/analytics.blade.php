@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Analytics & Reports</h1>
        <p class="mt-2 text-gray-600">Performance insights and metrics</p>
    </div>

    {{-- Task Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Tasks</div>
            <div class="text-2xl font-bold mt-2">{{ $taskStats['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Completed</div>
            <div class="text-2xl font-bold text-green-600 mt-2">{{ $taskStats['completed'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">In Progress</div>
            <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $taskStats['in_progress'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">{{ $taskStats['pending'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Overdue</div>
            <div class="text-2xl font-bold text-red-600 mt-2">{{ $taskStats['overdue'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Top Performers --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Staff Performance</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($staffPerformance->take(10) as $performance)
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center flex-1">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">
                                        {{ substr($performance['staff']->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="font-medium">{{ $performance['staff']->user->name }}</p>
                                    <div class="flex items-center gap-4 mt-1">
                                        <span class="text-xs text-gray-500">
                                            {{ $performance['completed_tasks'] }}/{{ $performance['total_tasks'] }} tasks
                                        </span>
                                        <div class="flex items-center">
                                            <div class="w-24 bg-gray-200 rounded-full h-1.5 mr-2">
                                                <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $performance['completion_rate'] }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold">{{ number_format($performance['completion_rate'], 0) }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Department Breakdown --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Department Breakdown</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($departmentStats as $stat)
                        @if($stat->department)
                            <div class="pb-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                                        style="background-color: {{ $stat->department->color }}20; color: {{ $stat->department->color }};">
                                        <i class="{{ $stat->department->icon }} mr-2"></i>
                                        {{ $stat->department->name }}
                                    </span>
                                    <span class="text-sm font-semibold">
                                        {{ $stat->completed }}/{{ $stat->total }} tasks
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-green-600 h-2 rounded-full" 
                                            style="width: {{ $stat->total > 0 ? ($stat->completed / $stat->total * 100) : 0 }}%">
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-600">
                                        {{ $stat->total > 0 ? number_format(($stat->completed / $stat->total * 100), 0) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

