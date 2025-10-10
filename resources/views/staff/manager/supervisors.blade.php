@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Supervisors</h1>
        <p class="mt-2 text-gray-600">Manage your supervisors and their teams</p>
    </div>

    {{-- Supervisors Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($supervisors as $supervisor)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                <div class="p-6">
                    {{-- Supervisor Info --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-bold text-xl">
                                    {{ substr($supervisor->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-lg">{{ $supervisor->user->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $supervisor->job_title ?? 'Supervisor' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Department --}}
                    @if($supervisor->department)
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                                style="background-color: {{ $supervisor->department->color }}20; color: {{ $supervisor->department->color }};">
                                <i class="{{ $supervisor->department->icon }} mr-2"></i>
                                {{ $supervisor->department->name }}
                            </span>
                        </div>
                    @endif

                    {{-- Stats --}}
                    <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b">
                        <div>
                            <p class="text-xs text-gray-500">Team Members</p>
                            <p class="text-xl font-bold">{{ $supervisor->subordinates->count() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Assigned Tasks</p>
                            <p class="text-xl font-bold text-blue-600">{{ $supervisor->assigned_tasks_count }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tasks Delegated</p>
                            <p class="text-xl font-bold text-green-600">{{ $supervisor->delegated_tasks_count }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $supervisor->getStatusBadgeColor() }}">
                                {{ ucfirst($supervisor->status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Team Members --}}
                    @if($supervisor->subordinates->count() > 0)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Team Members:</p>
                            <div class="space-y-2">
                                @foreach($supervisor->subordinates->take(3) as $member)
                                    <div class="flex items-center text-sm">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                            <span class="text-xs font-semibold">{{ substr($member->user->name, 0, 1) }}</span>
                                        </div>
                                        <span>{{ $member->user->name }}</span>
                                    </div>
                                @endforeach
                                @if($supervisor->subordinates->count() > 3)
                                    <p class="text-xs text-gray-500 ml-8">
                                        +{{ $supervisor->subordinates->count() - 3 }} more
                                    </p>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">No team members yet</p>
                    @endif

                    {{-- Contact Info --}}
                    <div class="mt-4 pt-4 border-t space-y-1">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-envelope mr-2"></i>
                            {{ $supervisor->user->email }}
                        </p>
                        @if($supervisor->phone)
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-phone mr-2"></i>
                                {{ $supervisor->phone }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-user-tie text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No supervisors found</p>
                <a href="{{ route('owner.staff.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                    Add a supervisor →
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection

