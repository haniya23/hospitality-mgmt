@extends('layouts.app')

@section('title', 'Staff Management - Stay loops')
@section('page-title', 'Staff Management')

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
            <span class="text-gray-700 font-medium">Staff Management</span>
        </nav>
    </div>

    <div class="bg-gradient-to-br from-white/95 to-blue-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-4 sm:p-6 border border-white/20 mb-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Staff Management</h2>
                    <p class="text-sm text-blue-600 font-medium mt-1">Manage your team across all properties</p>
                </div>
            </div>
            <a href="{{ route('owner.staff.create') }}" 
                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-semibold transition-all shadow-lg">
                <i class="fas fa-user-plus mr-2"></i> Add Staff Member
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Property</label>
                <select name="property_id" 
                    class="w-full border border-gray-200 rounded-xl shadow-sm py-2.5 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Department</label>
                <select name="department_id" 
                    class="w-full border border-gray-200 rounded-xl shadow-sm py-2.5 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Role</label>
                <select name="role" 
                    class="w-full border border-gray-200 rounded-xl shadow-sm py-2.5 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                    <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" 
                    class="w-full border border-gray-200 rounded-xl shadow-sm py-2.5 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row gap-2">
                <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold transition-all shadow-sm text-sm">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
                <a href="{{ route('owner.staff.index') }}" class="inline-flex justify-center items-center px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-semibold transition-all text-sm">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Staff Grid (Mobile-first design) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($staffMembers as $member)
            <div class="bg-gradient-to-br from-white/95 to-gray-50/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 overflow-hidden hover:shadow-2xl transition-all">
                <div class="p-4 sm:p-6">
                    <!-- Staff Header -->
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl {{ $member->getRoleBadgeColor() }} flex items-center justify-center text-xl sm:text-2xl font-bold shadow-lg flex-shrink-0">
                            {{ substr($member->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 text-base sm:text-lg truncate">{{ $member->user->name }}</h3>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">{{ $member->job_title ?? ucfirst($member->staff_role) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $member->property->name }}</p>
                        </div>
                    </div>

                    <!-- Badges -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $member->getRoleBadgeColor() }}">
                            {{ ucfirst($member->staff_role) }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $member->getStatusBadgeColor() }}">
                            {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                        </span>
                        @if($member->department)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" 
                                style="background-color: {{ $member->department->color }}20; color: {{ $member->department->color }};">
                                <i class="{{ $member->department->icon }} mr-1"></i> {{ $member->department->name }}
                            </span>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 text-sm mb-4 pb-4 border-b border-gray-200">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-envelope w-5 text-gray-400"></i>
                            <span class="truncate">{{ $member->user->email }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar w-5 text-gray-400"></i>
                            <span>Joined {{ $member->join_date->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('owner.staff.show', $member) }}" 
                            class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-all">
                            <i class="fas fa-eye mr-1.5"></i> View
                        </a>
                        <a href="{{ route('owner.staff.edit', $member) }}" 
                            class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-all">
                            <i class="fas fa-edit mr-1.5"></i> Edit
                        </a>
                        <form action="{{ route('owner.staff.destroy', $member) }}" method="POST" class="inline" 
                            onsubmit="return confirm('Remove this staff member?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-all">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 lg:col-span-3">
                <div class="bg-gradient-to-br from-white/95 to-gray-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-8 sm:p-12 border border-white/20 text-center">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-users text-blue-600 text-4xl sm:text-5xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">No Staff Members Yet</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">Start building your team by adding staff members to manage your properties efficiently.</p>
                    <a href="{{ route('owner.staff.create') }}" 
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-semibold transition-all shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i> Add Your First Staff Member
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($staffMembers->hasPages())
        <div class="mt-6">
            {{ $staffMembers->links() }}
        </div>
    @endif
@endsection
