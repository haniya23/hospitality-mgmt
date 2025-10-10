@extends('layouts.staff')

@section('title', 'Staff Checklists')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="staffChecklists()">
    <!-- Back Button -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('staff.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>
    
    <!-- Header -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Cleaning Checklists</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Manage your cleaning tasks and inspections</p>
            </div>
        </div>
    </div>

    <!-- Active Checklists -->
    @if($activeChecklists->count() > 0)
        <div class="modern-card rounded-2xl p-4 sm:p-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Active Checklists</h3>
            <div class="space-y-3">
                @foreach($activeChecklists as $execution)
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <div class="flex items-center flex-1 min-w-0">
                            <div class="flex-shrink-0 mr-3">
                                <i class="fas fa-clipboard-check text-blue-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm sm:text-base font-medium text-gray-900">{{ $execution->checklist->name }}</h4>
                                <p class="text-xs sm:text-sm text-gray-600">
                                    {{ $execution->checklist->property->name }}
                                    @if($execution->accommodation)
                                        - {{ $execution->accommodation->name }}
                                    @endif
                                </p>
                                <div class="flex items-center mt-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $execution->getCompletionPercentage() }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">{{ $execution->getCompletionPercentage() }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-3">
                            <a href="{{ route('staff.checklist.execute', $execution->uuid) }}" 
                               class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                Continue
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Available Checklists -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Available Checklists</h3>
        
        @if($availableChecklists->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                @foreach($availableChecklists as $checklist)
                    <div class="p-3 sm:p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm sm:text-base font-medium text-gray-900 truncate">{{ $checklist->name }}</h4>
                                <p class="text-xs text-gray-600 mt-1">{{ $checklist->property->name }}</p>
                            </div>
                            <div class="flex-shrink-0 ml-2">
                                <i class="fas fa-clipboard-list text-gray-400"></i>
                            </div>
                        </div>
                        
                        @if($checklist->description)
                            <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $checklist->description }}</p>
                        @endif
                        
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            <span><i class="fas fa-list mr-1"></i>{{ $checklist->items->count() }} items</span>
                            <span><i class="fas fa-clock mr-1"></i>{{ $checklist->estimated_duration ?? 'N/A' }} min</span>
                        </div>
                        
                        <button @click="startChecklist({{ $checklist->id }})" 
                                class="w-full px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            <i class="fas fa-play mr-2"></i>Start Checklist
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No checklists available</h3>
                <p class="text-gray-500">Contact your manager to assign cleaning checklists for your properties.</p>
            </div>
        @endif
    </div>
</div>

<script>
function staffChecklists() {
    return {
        async startChecklist(checklistId) {
            try {
                const response = await fetch(`/staff/checklists/${checklistId}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    const result = await response.json();
                    this.showNotification('Checklist started successfully!', 'success');
                    
                    // Redirect to checklist execution
                    if (result.redirect_url) {
                        window.location.href = result.redirect_url;
                    }
                } else {
                    const error = await response.json();
                    this.showNotification(error.message || 'Failed to start checklist', 'error');
                }
            } catch (error) {
                console.error('Error starting checklist:', error);
                this.showNotification('An error occurred while starting the checklist', 'error');
            }
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
}
</script>
@endsection
