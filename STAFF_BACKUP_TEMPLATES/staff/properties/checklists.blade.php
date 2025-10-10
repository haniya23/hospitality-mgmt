@extends('layouts.staff')

@section('title', 'Cleaning Checklists - ' . $property->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <div class="flex items-center">
                        <a href="{{ route('staff.properties.show', $property->uuid) }}" 
                           class="mr-3 p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Cleaning Checklists</h1>
                            <p class="text-sm sm:text-base text-gray-600">{{ $property->name }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('staff.properties.show', $property->uuid) }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Property
                    </a>
                </div>
            </div>
        </div>

        <!-- Checklists List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Available Checklists</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $checklists->count() }} cleaning checklists</p>
            </div>

            @if($checklists->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($checklists as $checklist)
                        <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex-1 mb-4 sm:mb-0">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-clipboard-check text-green-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $checklist->name }}</h3>
                                            @if($checklist->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $checklist->description }}</p>
                                            @endif
                                            <div class="flex items-center mt-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $checklist->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                                    {{ $checklist->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                <span class="ml-2 text-xs text-gray-500">
                                                    Created {{ $checklist->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <button onclick="startChecklist('{{ $checklist->uuid }}')"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <i class="fas fa-play mr-2"></i>
                                        Start Checklist
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <i class="fas fa-clipboard-list text-4xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No checklists available</h3>
                    <p class="mt-1 text-sm text-gray-500">This property doesn't have any cleaning checklists set up.</p>
                </div>
            @endif
        </div>

        <!-- Recent Executions -->
        @if($assignment && $assignment->checklistExecutions()->count() > 0)
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Executions</h2>
                    <p class="text-sm text-gray-600 mt-1">Your recent checklist completions</p>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @foreach($assignment->checklistExecutions()->latest()->limit(5)->get() as $execution)
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $execution->cleaningChecklist->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $execution->propertyAccommodation->custom_name ?? 'Accommodation' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $execution->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($execution->status) }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $execution->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Start Checklist Modal -->
<div id="startChecklistModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <i class="fas fa-clipboard-check text-blue-600"></i>
            </div>
            <div class="mt-2 px-7 py-3">
                <h3 class="text-lg font-medium text-gray-900 text-center">Start Cleaning Checklist</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center">
                        Select the accommodation you want to clean and start the checklist.
                    </p>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Accommodation</label>
                    <select id="accommodationSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose accommodation...</option>
                        @foreach($property->accommodations()->where('is_active', true)->get() as $accommodation)
                            <option value="{{ $accommodation->uuid }}">{{ $accommodation->custom_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <div class="flex space-x-3">
                    <button id="cancelChecklist" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button id="confirmStartChecklist" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Start Checklist
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentChecklistId = null;

    function startChecklist(checklistId) {
        currentChecklistId = checklistId;
        document.getElementById('startChecklistModal').classList.remove('hidden');
    }

    document.getElementById('cancelChecklist').addEventListener('click', function() {
        document.getElementById('startChecklistModal').classList.add('hidden');
        currentChecklistId = null;
    });

    document.getElementById('confirmStartChecklist').addEventListener('click', function() {
        const accommodationSelect = document.getElementById('accommodationSelect');
        const accommodationId = accommodationSelect.value;
        
        if (!accommodationId) {
            alert('Please select an accommodation');
            return;
        }

        // Start the checklist
        fetch(`/staff/checklists/${currentChecklistId}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                accommodation_id: accommodationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to checklist execution page
                window.location.href = `/staff/checklists/${data.execution_id}/execute`;
            } else {
                alert('Failed to start checklist: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to start checklist');
        })
        .finally(() => {
            document.getElementById('startChecklistModal').classList.add('hidden');
            currentChecklistId = null;
        });
    });
</script>
@endpush
