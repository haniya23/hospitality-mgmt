@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 md:px-6 py-4 md:py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">Edit Access Permissions</h1>
                <p class="text-sm md:text-base text-gray-600 mt-2">Managing permissions for: <strong>{{ $staffMember->user->name }}</strong></p>
            </div>
            <a href="{{ route('staff.permissions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg text-center w-full md:w-auto">
                Back to Access Management
            </a>
        </div>
    </div>

    <!-- Staff Info Card -->
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div>
                <p class="text-xs md:text-sm text-gray-500">Staff Member</p>
                <p class="font-semibold text-sm md:text-base truncate">{{ $staffMember->user->name }}</p>
            </div>
            <div>
                <p class="text-xs md:text-sm text-gray-500">Role</p>
                <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $staffMember->getRoleBadgeColor() }}">
                    {{ ucfirst($staffMember->staff_role) }}
                </span>
            </div>
            <div>
                <p class="text-xs md:text-sm text-gray-500">Department</p>
                <p class="font-semibold text-sm md:text-base truncate">{{ $staffMember->department->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs md:text-sm text-gray-500">Status</p>
                <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $staffMember->getStatusBadgeColor() }}">
                    {{ ucfirst($staffMember->status) }}
                </span>
            </div>
        </div>
        
        @if($staffMember->permissions && $staffMember->permissions->last_updated_by)
            <div class="border-t pt-3">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-clock w-4 text-gray-400 mr-2"></i>
                    <span>
                        Last updated by <strong>{{ $staffMember->permissions->lastUpdatedBy->name }}</strong>
                        @if($staffMember->permissions->last_updated_at)
                            on 
                            @if(is_object($staffMember->permissions->last_updated_at) && method_exists($staffMember->permissions->last_updated_at, 'format'))
                                {{ $staffMember->permissions->last_updated_at->format('M j, Y \a\t g:i A') }}
                            @else
                                {{ \Carbon\Carbon::parse($staffMember->permissions->last_updated_at)->format('M j, Y \a\t g:i A') }}
                            @endif
                        @endif
                    </span>
                </div>
            </div>
        @endif
    </div>

    @if($staffMember->isManager())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-yellow-800">
                <strong>Note:</strong> Managers have all permissions by default. Changes made here will be overridden by the manager role.
            </p>
        </div>
    @endif

    <!-- Permission Form -->
    <form action="{{ route('staff.permissions.update', $staffMember) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            @foreach($permissionGroups as $groupName => $permissions)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-b">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">{{ $groupName }}</h3>
                    </div>
                    <div class="p-4 md:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                            @foreach($permissions as $key => $label)
                                <div class="flex items-center py-2 md:py-0">
                                    <input type="checkbox" 
                                           id="{{ $key }}" 
                                           name="{{ $key }}" 
                                           value="1"
                                           {{ $staffMember->permissions->$key ? 'checked' : '' }}
                                           class="h-4 w-4 md:h-5 md:w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded flex-shrink-0"
                                           {{ $staffMember->isManager() ? 'disabled' : '' }}>
                                    <label for="{{ $key }}" class="ml-3 text-sm md:text-base text-gray-700">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <!-- Primary Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium w-full sm:w-auto text-center">
                        Save Permissions
                    </button>
                    <a href="{{ route('staff.permissions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium w-full sm:w-auto text-center">
                        Cancel
                    </a>
                </div>
                
                <!-- Quick Actions -->
                @if(!$staffMember->isManager())
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" 
                                onclick="selectAll()" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-sm font-medium w-full sm:w-auto text-center">
                            Select All
                        </button>
                        <button type="button" 
                                onclick="deselectAll()" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg text-sm font-medium w-full sm:w-auto text-center">
                            Deselect All
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

<script>
function selectAll() {
    document.querySelectorAll('input[type="checkbox"]:not([disabled])').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('input[type="checkbox"]:not([disabled])').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection

