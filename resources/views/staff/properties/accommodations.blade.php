@extends('layouts.staff')

@section('title', $property->name . ' - Accommodations')

@section('staff-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('staff.properties.show', $property) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $property->name }} - Accommodations</h1>
                    <p class="text-gray-600">Manage rooms and accommodation-wise checklists</p>
                </div>
            </div>
        </div>
        <div class="text-sm text-gray-500">
            <i class="fas fa-bed mr-1"></i>
            {{ $accommodations->count() }} Accommodations
        </div>
    </div>

    @if($accommodations->isEmpty())
    <!-- Empty State -->
    <div class="text-center py-12">
        <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-bed text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Accommodations Found</h3>
        <p class="text-gray-500 mb-6">This property doesn't have any accommodations configured yet.</p>
    </div>
    @else
    <!-- Accommodations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($accommodations as $accommodationData)
        @php
            $accommodation = $accommodationData['accommodation'];
            $currentBooking = $accommodationData['current_booking'];
            $nextBooking = $accommodationData['next_booking'];
            $checklistExecutions = $accommodationData['checklist_executions'];
            $upcomingBookingsCount = $accommodationData['upcoming_bookings_count'];
        @endphp
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <!-- Accommodation Header -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $accommodation->display_name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $accommodation->accommodation_type->name ?? 'Room' }}</p>
                        <div class="flex items-center mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($accommodation->status === 'active') bg-green-100 text-green-800
                                @elseif($accommodation->status === 'maintenance') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($accommodation->status) }}
                            </span>
                            @if($currentBooking)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Occupied
                            </span>
                            @else
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Available
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bed text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Booking -->
            @if($currentBooking)
            <div class="p-4 bg-blue-50 border-b border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-900">{{ $currentBooking->guest->name ?? 'Guest' }}</p>
                        <p class="text-xs text-blue-700">Check-out: {{ $currentBooking->check_out_date->format('M d, Y') }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ ucfirst(str_replace('_', ' ', $currentBooking->status)) }}
                    </span>
                </div>
            </div>
            @endif

            <!-- Next Booking -->
            @if($nextBooking)
            <div class="p-4 bg-yellow-50 border-b border-yellow-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-900">Next: {{ $nextBooking->guest->name ?? 'Guest' }}</p>
                        <p class="text-xs text-yellow-700">Check-in: {{ $nextBooking->check_in_date->format('M d, Y') }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Confirmed
                    </span>
                </div>
            </div>
            @endif

            <!-- Checklist Executions -->
            @if($checklistExecutions->isNotEmpty())
            <div class="p-4 bg-orange-50 border-b border-orange-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-orange-900">Active Checklists</span>
                    <span class="text-xs text-orange-700">{{ $checklistExecutions->count() }} in progress</span>
                </div>
                <div class="space-y-2">
                    @foreach($checklistExecutions as $execution)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-orange-800">{{ $execution->cleaningChecklist->name }}</span>
                        <a href="{{ route('staff.checklist.execute', $execution->uuid) }}" 
                           class="text-xs text-orange-600 hover:text-orange-800 font-medium">
                            Continue
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Accommodation Stats -->
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-xl font-bold text-blue-600">{{ $upcomingBookingsCount }}</div>
                        <div class="text-xs text-gray-500">Upcoming Bookings</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-green-600">{{ $accommodation->cleaningChecklists->count() }}</div>
                        <div class="text-xs text-gray-500">Checklists</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    @if($accommodation->cleaningChecklists->isNotEmpty())
                    <button onclick="showChecklistModal('{{ $accommodation->id }}')" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        Start Checklist
                    </button>
                    @endif
                    
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('staff.bookings') }}?accommodation={{ $accommodation->id }}" 
                           class="flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <i class="fas fa-calendar mr-1"></i>
                            Bookings
                        </a>
                        <button onclick="showAccommodationDetails('{{ $accommodation->id }}')" 
                                class="flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <i class="fas fa-info mr-1"></i>
                            Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Accommodation Footer -->
            <div class="px-6 py-3 bg-gray-50 rounded-b-xl">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>
                        <i class="fas fa-users mr-1"></i>
                        {{ $accommodation->max_occupancy ?? 'N/A' }} guests
                    </span>
                    <span>
                        <i class="fas fa-tag mr-1"></i>
                        ₹{{ number_format($accommodation->base_price ?? 0) }}/night
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Checklist Selection Modal -->
<div id="checklist-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Select Checklist</h3>
                <button onclick="closeChecklistModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="checklist-list" class="space-y-2 max-h-64 overflow-y-auto">
                <!-- Checklists will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Accommodation Details Modal -->
<div id="accommodation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Accommodation Details</h3>
                <button onclick="closeAccommodationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="accommodation-details">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
let currentAccommodationId = null;

function showChecklistModal(accommodationId) {
    currentAccommodationId = accommodationId;
    
    // Get checklists for this accommodation
    const accommodation = @json($accommodations->pluck('accommodation')->keyBy('id'));
    const checklists = accommodation[accommodationId].cleaning_checklists;
    
    const checklistList = document.getElementById('checklist-list');
    checklistList.innerHTML = '';
    
    if (checklists.length === 0) {
        checklistList.innerHTML = '<p class="text-gray-500 text-center py-4">No checklists available for this accommodation.</p>';
    } else {
        checklists.forEach(checklist => {
            const checklistItem = document.createElement('div');
            checklistItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer';
            checklistItem.innerHTML = `
                <div>
                    <p class="text-sm font-medium text-gray-900">${checklist.name}</p>
                    <p class="text-xs text-gray-500">${checklist.description || 'No description'}</p>
                </div>
                <button onclick="startChecklist(${checklist.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Start
                </button>
            `;
            checklistList.appendChild(checklistItem);
        });
    }
    
    document.getElementById('checklist-modal').classList.remove('hidden');
}

function closeChecklistModal() {
    document.getElementById('checklist-modal').classList.add('hidden');
    currentAccommodationId = null;
}

function startChecklist(checklistId) {
    // Redirect to checklist execution
    window.location.href = `/staff/checklists/${checklistId}/start?accommodation_id=${currentAccommodationId}`;
}

function showAccommodationDetails(accommodationId) {
    const accommodation = @json($accommodations->pluck('accommodation')->keyBy('id'));
    const acc = accommodation[accommodationId];
    
    const detailsDiv = document.getElementById('accommodation-details');
    detailsDiv.innerHTML = `
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <p class="text-sm text-gray-900">${acc.display_name}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <p class="text-sm text-gray-900">${acc.accommodation_type?.name || 'Not specified'}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Occupancy</label>
                <p class="text-sm text-gray-900">${acc.max_occupancy || 'Not specified'} guests</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Base Price</label>
                <p class="text-sm text-gray-900">₹${acc.base_price ? acc.base_price.toLocaleString() : '0'} per night</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                    ${acc.status === 'active' ? 'bg-green-100 text-green-800' : 
                      acc.status === 'maintenance' ? 'bg-red-100 text-red-800' : 
                      'bg-gray-100 text-gray-800'}">
                    ${acc.status.charAt(0).toUpperCase() + acc.status.slice(1)}
                </span>
            </div>
            ${acc.description ? `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <p class="text-sm text-gray-900">${acc.description}</p>
            </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('accommodation-modal').classList.remove('hidden');
}

function closeAccommodationModal() {
    document.getElementById('accommodation-modal').classList.add('hidden');
}
</script>
@endsection
