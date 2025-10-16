@extends('layouts.app')

@section('title', 'Staff Management - Stay loops')
@section('page-title', 'Staff Management')

@push('styles')
<style>
    .hierarchy-tree {
        position: relative;
        padding-left: 0;
    }
    .hierarchy-tree ul {
        position: relative;
        padding-left: 30px;
        margin: 10px 0;
    }
    .hierarchy-tree li {
        position: relative;
        padding: 5px 0;
        list-style: none;
    }
    .hierarchy-tree li::before {
        content: '';
        position: absolute;
        top: 0;
        left: -20px;
        border-left: 2px solid #cbd5e1;
        border-bottom: 2px solid #cbd5e1;
        width: 20px;
        height: 20px;
    }
    .hierarchy-tree li::after {
        content: '';
        position: absolute;
        top: 20px;
        left: -20px;
        border-left: 2px solid #cbd5e1;
        height: 100%;
    }
    .hierarchy-tree li:last-child::after {
        display: none;
    }
    .hierarchy-tree > li:first-child::before,
    .hierarchy-tree > li:first-child::after {
        display: none;
    }
</style>
@endpush

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

    <div x-data="{ view: 'hierarchy', selectedProperty: 'all' }">
        <!-- Header -->
        <div class="bg-gradient-to-br from-white/95 to-blue-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-4 sm:p-6 border border-white/20 mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Staff Management</h2>
                        <p class="text-sm text-blue-600 font-medium mt-1">{{ $staffMembers->total() }} team members across {{ $properties->count() }} properties</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('staff.templates.index') }}" 
                        class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 font-semibold transition-all shadow-lg">
                        <i class="fas fa-sitemap mr-2"></i> Use Template
                    </a>
                    <a href="{{ route('owner.staff.create') }}" 
                        class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-semibold transition-all shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i> Add Staff Member
                    </a>
                </div>
            </div>

            <!-- View Toggle & Property Filter -->
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <!-- View Toggle -->
                <div class="inline-flex bg-white rounded-xl p-1 shadow-sm border border-gray-200">
                    <button @click="view = 'hierarchy'" 
                        :class="view === 'hierarchy' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg font-medium transition-all text-sm">
                        <i class="fas fa-sitemap mr-2"></i>Hierarchy View
                    </button>
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg font-medium transition-all text-sm">
                        <i class="fas fa-list mr-2"></i>List View
                    </button>
                </div>

                <!-- Property Filter -->
                <div class="flex items-center gap-3">
                    <label class="text-sm font-semibold text-gray-700">Filter by Property:</label>
                    <select x-model="selectedProperty" 
                        class="border border-gray-200 rounded-xl shadow-sm py-2 px-4 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Properties</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Hierarchy View -->
        <div x-show="view === 'hierarchy'" x-transition class="space-y-6">
            @foreach($properties as $property)
                @php
                    $propertyStaff = $staffMembers->where('property_id', $property->id);
                    $managers = $propertyStaff->where('staff_role', 'manager');
                @endphp
                
                <div x-show="selectedProperty === 'all' || selectedProperty === '{{ $property->id }}'" 
                    class="bg-gradient-to-br from-white/95 to-emerald-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-6 border border-white/20">
                    
                    <!-- Property Header -->
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-building text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $property->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $propertyStaff->count() }} team members</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold">
                                <i class="fas fa-user-tie mr-1"></i>{{ $managers->count() }} Managers
                            </span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                <i class="fas fa-user-friends mr-1"></i>{{ $propertyStaff->where('staff_role', 'supervisor')->count() }} Supervisors
                            </span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
                                <i class="fas fa-users mr-1"></i>{{ $propertyStaff->where('staff_role', 'staff')->count() }} Staff
                            </span>
                        </div>
                    </div>

                    @if($propertyStaff->isEmpty())
                        <div class="text-center py-12">
                            <i class="fas fa-users-slash text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500 font-medium">No staff members in this property yet</p>
                            <a href="{{ route('owner.staff.create') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold mt-3">
                                <i class="fas fa-plus-circle mr-2"></i>Add first staff member
                            </a>
                        </div>
                    @else
                        <!-- Hierarchy Tree -->
                        <div class="hierarchy-tree">
                            @foreach($managers as $manager)
                                <div class="mb-6">
                                    <!-- Manager Node -->
                                    <div class="flex items-center gap-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200 shadow-sm hover:shadow-md transition-all">
                                        <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-purple-700 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                                            {{ strtoupper(substr($manager->user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('owner.staff.show', $manager) }}" class="text-lg font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                                    {{ $manager->user->name }}
                                                </a>
                                                <span class="px-2 py-1 bg-purple-600 text-white rounded-lg text-xs font-semibold">
                                                    Manager
                                                </span>
                                                <span class="px-2 py-1 {{ $manager->getStatusBadgeColor() }} rounded-lg text-xs font-semibold">
                                                    {{ ucfirst($manager->status) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-briefcase mr-1"></i>{{ $manager->job_title ?? 'Manager' }}
                                                @if($manager->department)
                                                    <span class="mx-2">•</span>
                                                    <i class="fas fa-building mr-1"></i>{{ $manager->department->name }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('owner.staff.edit', $manager) }}" 
                                                class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all text-sm font-semibold">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>

                                    @php
                                        $managerSupervisors = $propertyStaff->where('reports_to', $manager->id)->where('staff_role', 'supervisor');
                                        $managerDirectStaff = $propertyStaff->where('reports_to', $manager->id)->where('staff_role', 'staff');
                                    @endphp

                                    @if($managerSupervisors->isNotEmpty() || $managerDirectStaff->isNotEmpty())
                                        <ul>
                                            @foreach($managerSupervisors as $supervisor)
                                                <li>
                                                    <!-- Supervisor Node -->
                                                    <div class="flex items-center gap-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-3 border border-blue-200 shadow-sm hover:shadow-md transition-all">
                                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center text-white font-bold shadow-md">
                                                            {{ strtoupper(substr($supervisor->user->name, 0, 1)) }}
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <a href="{{ route('owner.staff.show', $supervisor) }}" class="font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                                                    {{ $supervisor->user->name }}
                                                                </a>
                                                                <span class="px-2 py-0.5 bg-blue-600 text-white rounded text-xs font-semibold">
                                                                    Supervisor
                                                                </span>
                                                            </div>
                                                            <p class="text-xs text-gray-600">
                                                                <i class="fas fa-briefcase mr-1"></i>{{ $supervisor->job_title ?? 'Supervisor' }}
                                                                @if($supervisor->department)
                                                                    <span class="mx-2">•</span>{{ $supervisor->department->name }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <a href="{{ route('owner.staff.edit', $supervisor) }}" 
                                                            class="px-2 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all text-xs">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>

                                                    @php
                                                        $supervisorStaff = $propertyStaff->where('reports_to', $supervisor->id)->where('staff_role', 'staff');
                                                    @endphp

                                                    @if($supervisorStaff->isNotEmpty())
                                                        <ul>
                                                            @foreach($supervisorStaff as $staff)
                                                                <li>
                                                                    <!-- Staff Node -->
                                                                    <div class="flex items-center gap-3 bg-white rounded-lg p-2 border border-gray-200 shadow-sm hover:shadow-md transition-all">
                                                                        <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow">
                                                                            {{ strtoupper(substr($staff->user->name, 0, 1)) }}
                                                                        </div>
                                                                        <div class="flex-1">
                                                                            <div class="flex items-center gap-2">
                                                                                <a href="{{ route('owner.staff.show', $staff) }}" class="font-semibold text-gray-900 hover:text-blue-600 transition-colors text-sm">
                                                                                    {{ $staff->user->name }}
                                                                                </a>
                                                                                <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs font-semibold">
                                                                                    Staff
                                                                                </span>
                                                                            </div>
                                                                            <p class="text-xs text-gray-600">{{ $staff->job_title ?? 'Staff Member' }}</p>
                                                                        </div>
                                                                        <a href="{{ route('owner.staff.edit', $staff) }}" 
                                                                            class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-all text-xs">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach

                                            @foreach($managerDirectStaff as $staff)
                                                <li>
                                                    <div class="flex items-center gap-3 bg-white rounded-lg p-2 border border-gray-200 shadow-sm hover:shadow-md transition-all">
                                                        <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow">
                                                            {{ strtoupper(substr($staff->user->name, 0, 1)) }}
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <a href="{{ route('owner.staff.show', $staff) }}" class="font-semibold text-gray-900 hover:text-blue-600 transition-colors text-sm">
                                                                    {{ $staff->user->name }}
                                                                </a>
                                                                <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs font-semibold">Staff</span>
                                                            </div>
                                                            <p class="text-xs text-gray-600">{{ $staff->job_title ?? 'Staff Member' }}</p>
                                                        </div>
                                                        <a href="{{ route('owner.staff.edit', $staff) }}" 
                                                            class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-all text-xs">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Staff without supervisor -->
                            @php
                                $unassignedStaff = $propertyStaff->whereNull('reports_to')->whereIn('staff_role', ['supervisor', 'staff']);
                            @endphp
                            @if($unassignedStaff->isNotEmpty())
                                <div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-200">
                                    <h4 class="font-bold text-amber-900 mb-3 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Unassigned Staff ({{ $unassignedStaff->count() }})
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($unassignedStaff as $staff)
                                            <div class="flex items-center gap-3 bg-white rounded-lg p-2 border border-amber-200">
                                                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                    {{ strtoupper(substr($staff->user->name, 0, 1)) }}
                                                </div>
                                                <div class="flex-1">
                                                    <a href="{{ route('owner.staff.show', $staff) }}" class="font-semibold text-gray-900 hover:text-blue-600 text-sm">
                                                        {{ $staff->user->name }}
                                                    </a>
                                                    <p class="text-xs text-gray-600">{{ ucfirst($staff->staff_role) }} - {{ $staff->job_title ?? 'No title' }}</p>
                                                </div>
                                                <a href="{{ route('owner.staff.edit', $staff) }}" 
                                                    class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-all text-xs">
                                                    <i class="fas fa-edit"></i> Assign
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" x-transition class="bg-gradient-to-br from-white/95 to-blue-50/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20">
            <div class="p-6">
                <!-- Filters -->
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <input type="hidden" name="view" value="list">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Property</label>
                        <select name="property_id" onchange="this.form.submit()"
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
                        <select name="department_id" onchange="this.form.submit()"
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
                        <select name="role" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-2.5 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Roles</option>
                            <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-2.5 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                </form>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Staff</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Property</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Department</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Reports To</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($staffMembers as $staff)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 {{ $staff->getRoleBadgeColor() }} rounded-xl flex items-center justify-center font-bold">
                                                {{ strtoupper(substr($staff->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('owner.staff.show', $staff) }}" class="font-semibold text-gray-900 hover:text-blue-600">
                                                    {{ $staff->user->name }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $staff->job_title ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $staff->property->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $staff->department->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 {{ $staff->getRoleBadgeColor() }} rounded-full text-xs font-semibold">
                                            {{ ucfirst($staff->staff_role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $staff->supervisor ? $staff->supervisor->user->name : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 {{ $staff->getStatusBadgeColor() }} rounded-full text-xs font-semibold">
                                            {{ ucfirst($staff->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('owner.staff.show', $staff) }}" 
                                                class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all text-xs">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('owner.staff.edit', $staff) }}" 
                                                class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all text-xs">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <i class="fas fa-users-slash text-gray-400 text-4xl mb-3"></i>
                                        <p class="text-gray-500 font-medium">No staff members found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $staffMembers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
