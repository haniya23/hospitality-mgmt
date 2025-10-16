@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 md:px-6 py-4 md:py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Access Management</h1>
                <p class="text-sm md:text-base text-gray-600 mt-2">Manage staff permissions and access controls</p>
            </div>
            <a href="{{ route('staff.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg text-center w-full md:w-auto">
                Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Hierarchy Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 md:p-6 mb-6">
        <h2 class="text-base md:text-lg font-semibold text-blue-800 mb-4">Access Control Hierarchy</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg">
                <div class="flex items-center mb-2">
                    <div class="w-3 h-3 rounded-full bg-purple-500 mr-2"></div>
                    <h3 class="font-semibold text-purple-800">Manager</h3>
                </div>
                <ul class="text-sm text-gray-600 space-y-1 ml-5">
                    <li>• Full access to all features</li>
                    <li>• Can manage all staff</li>
                    <li>• Can assign/revoke permissions</li>
                    <li>• View financial reports</li>
                </ul>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <div class="flex items-center mb-2">
                    <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                    <h3 class="font-semibold text-blue-800">Supervisor</h3>
                </div>
                <ul class="text-sm text-gray-600 space-y-1 ml-5">
                    <li>• Manage reservations & guests</li>
                    <li>• Assign & verify tasks</li>
                    <li>• View team reports</li>
                    <li>• Manage direct reports</li>
                </ul>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <div class="flex items-center mb-2">
                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                    <h3 class="font-semibold text-green-800">Staff</h3>
                </div>
                <ul class="text-sm text-gray-600 space-y-1 ml-5">
                    <li>• View reservations & guests</li>
                    <li>• Complete assigned tasks</li>
                    <li>• Basic data entry</li>
                    <li>• View own information</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
        <div class="relative">
            <input type="text" 
                   id="searchInput" 
                   placeholder="Search by name, role, department, or property..." 
                   class="w-full px-4 py-3 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Staff Members List by Property -->
    @php
        $staffByProperty = $staffMembers->groupBy('property_id');
        $roleIcons = [
            'manager' => 'fa-user-tie',
            'supervisor' => 'fa-user-cog',
            'staff' => 'fa-user'
        ];
    @endphp

    @foreach($staffByProperty as $propertyId => $propertyStaff)
        @php
            $property = $propertyStaff->first()->property;
            $managerCount = $propertyStaff->where('staff_role', 'manager')->count();
            $supervisorCount = $propertyStaff->where('staff_role', 'supervisor')->count();
            $staffCount = $propertyStaff->where('staff_role', 'staff')->count();
        @endphp
        
        <!-- Property Section -->
        <div class="mb-8 property-section" data-property="{{ $propertyId }}">
            <!-- Property Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-md p-4 md:p-6 mb-4">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold">{{ $property->name }}</h2>
                            <p class="text-sm md:text-base text-blue-100">{{ $propertyStaff->count() }} Total Staff Members</p>
                        </div>
                    </div>
                    <div class="hidden md:flex space-x-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ $managerCount }}</div>
                            <div class="text-xs text-blue-100">Managers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ $supervisorCount }}</div>
                            <div class="text-xs text-blue-100">Supervisors</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ $staffCount }}</div>
                            <div class="text-xs text-blue-100">Staff</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($propertyStaff->sortBy(function($staff) {
                    // Sort order: manager, supervisor, staff
                    $order = ['manager' => 1, 'supervisor' => 2, 'staff' => 3];
                    return $order[$staff->staff_role] ?? 4;
                }) as $staff)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow staff-card" 
                             data-name="{{ strtolower($staff->user->name) }}" 
                             data-role="{{ strtolower($staff->staff_role) }}"
                             data-department="{{ strtolower($staff->department->name ?? '') }}"
                             data-property="{{ strtolower($property->name) }}">
                            <!-- Card Header -->
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        {{ substr($staff->user->name, 0, 2) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base font-semibold text-gray-900 truncate">{{ $staff->user->name }}</h3>
                                        <p class="text-sm text-gray-500 truncate">{{ $staff->user->email ?? $staff->phone }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $staff->getStatusBadgeColor() }}">
                                        {{ ucfirst($staff->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 space-y-3">
                                <!-- Department -->
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-building w-5 text-gray-400"></i>
                                    <span class="text-gray-600 ml-2">{{ $staff->department->name ?? 'No Department' }}</span>
                                </div>

                                <!-- Reports To -->
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-user-friends w-5 text-gray-400"></i>
                                    <span class="text-gray-600 ml-2">
                                        @if($staff->supervisor)
                                            Reports to {{ $staff->supervisor->user->name }}
                                        @else
                                            <span class="text-gray-400">No supervisor</span>
                                        @endif
                                    </span>
                                </div>

                                <!-- Role Badge -->
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-shield-alt w-5 text-gray-400"></i>
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $staff->getRoleBadgeColor() }}">
                                        {{ ucfirst($staff->staff_role) }}
                                    </span>
                                </div>

                                <!-- Last Updated Info -->
                                @if($staff->permissions && $staff->permissions->last_updated_by)
                                    <div class="flex items-center text-xs text-gray-500 border-t pt-2 mt-2">
                                        <i class="fas fa-clock w-4 text-gray-400"></i>
                                        <span class="ml-2">
                                            Last updated by <strong>{{ $staff->permissions->lastUpdatedBy->name }}</strong>
                                            @if($staff->permissions->last_updated_at)
                                                <br>
                                                @if(is_object($staff->permissions->last_updated_at) && method_exists($staff->permissions->last_updated_at, 'format'))
                                                    {{ $staff->permissions->last_updated_at->format('M j, Y g:i A') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($staff->permissions->last_updated_at)->format('M j, Y g:i A') }}
                                                @endif
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Actions -->
                            @if($currentStaff->canManage($staff) || $currentStaff->isManager())
                                <div class="p-4 bg-gray-50 border-t border-gray-100">
                                    <div class="flex gap-2">
                                        <a href="{{ route('staff.permissions.edit', $staff) }}" 
                                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-3 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md h-[44px] flex items-center justify-center border-0">
                                            <i class="fas fa-edit mr-2"></i>Edit
                                        </a>
                                        
                                        @if(!$staff->isManager())
                                            <form action="{{ route('staff.permissions.reset', $staff) }}" method="POST" class="flex-1" onsubmit="return confirm('Reset permissions to default?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-3 py-3 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md h-[44px] flex items-center justify-center border-0">
                                                    <i class="fas fa-undo mr-2"></i>Reset
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('staff.permissions.revoke', $staff) }}" method="POST" class="flex-1" onsubmit="return confirm('Revoke all permissions?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-3 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md h-[44px] flex items-center justify-center border-0">
                                                    <i class="fas fa-ban mr-2"></i>Revoke
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
                                    <span class="text-gray-400 text-sm">No Access</span>
                                </div>
                            @endif
                        </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @if($staffMembers->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Staff Members Found</h3>
            <p class="text-gray-500">There are no staff members to display.</p>
        </div>
    @endif

    <!-- No Results Message (Hidden by default) -->
    <div id="noResults" class="hidden bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Results Found</h3>
        <p class="text-gray-500">Try adjusting your search criteria.</p>
    </div>

    <!-- Permission Legend -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Quick Actions Guide</h3>
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-blue-600 font-medium"><i class="fas fa-edit"></i> Edit:</span>
                <span class="text-gray-600"> Customize individual permissions</span>
            </div>
            <div>
                <span class="text-yellow-600 font-medium"><i class="fas fa-undo"></i> Reset:</span>
                <span class="text-gray-600"> Restore default permissions for role</span>
            </div>
            <div>
                <span class="text-red-600 font-medium"><i class="fas fa-ban"></i> Revoke:</span>
                <span class="text-gray-600"> Remove all permissions (emergency use)</span>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const staffCards = document.querySelectorAll('.staff-card');
    const roleSections = document.querySelectorAll('.role-section');
    const noResults = document.getElementById('noResults');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let hasVisibleCards = false;
        const propertySections = document.querySelectorAll('.property-section');
        
        // If search is empty, show all cards
        if (searchTerm === '') {
            staffCards.forEach(card => {
                card.classList.remove('hidden');
            });
            propertySections.forEach(section => {
                section.classList.remove('hidden');
            });
            noResults.classList.add('hidden');
            return;
        }
        
        // Search through each card
        staffCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const role = card.getAttribute('data-role');
            const department = card.getAttribute('data-department');
            const property = card.getAttribute('data-property');
            
            const matches = name.includes(searchTerm) || 
                          role.includes(searchTerm) || 
                          department.includes(searchTerm) ||
                          property.includes(searchTerm);
            
            if (matches) {
                card.classList.remove('hidden');
                hasVisibleCards = true;
            } else {
                card.classList.add('hidden');
            }
        });
        
        // Hide property sections that have no visible cards
        propertySections.forEach(section => {
            const visibleCards = section.querySelectorAll('.staff-card:not(.hidden)');
            if (visibleCards.length === 0) {
                section.classList.add('hidden');
            } else {
                section.classList.remove('hidden');
            }
        });
        
        // Show/hide no results message
        if (hasVisibleCards) {
            noResults.classList.add('hidden');
        } else {
            noResults.classList.remove('hidden');
        }
    });
    
    // Clear search on escape key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.dispatchEvent(new Event('input'));
        }
    });
});
</script>
@endsection

