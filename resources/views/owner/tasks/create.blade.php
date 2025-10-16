@extends('layouts.app')

@section('title', 'Assign Task - Stay loops')
@section('page-title', 'Assign Task')

@push('scripts')
<script>
function taskForm() {
    return {
        selectedProperty: '',
        selectedStaff: '',
        staffMembers: @json($staffMembers->groupBy('property_id')),
        filteredStaff: [],
        
        init() {
            this.filterStaff();
        },
        
        filterStaff() {
            if (!this.selectedProperty) {
                this.filteredStaff = [];
                return;
            }
            
            const propertyId = parseInt(this.selectedProperty);
            this.filteredStaff = this.staffMembers[propertyId] || [];
        }
    }
}
</script>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">
                <i class="fas fa-home"></i>
            </a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('owner.tasks.index') }}" class="hover:text-blue-600">Tasks</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-700 font-medium">Assign Task</span>
        </nav>
    </div>

    <div class="bg-gradient-to-br from-white/95 to-blue-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border border-white/20"
         x-data="taskForm()">
        <!-- Header -->
        <div class="flex items-center space-x-4 mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-tasks text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Assign New Task</h2>
                <p class="text-sm text-blue-600 font-medium mt-1">Assign work to your team members</p>
            </div>
        </div>

        <form action="{{ route('owner.tasks.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Property & Assignment -->
            <div class="bg-white/50 rounded-xl p-6 border border-white/30">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-building text-blue-600 mr-2"></i>Property & Assignment
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Property <span class="text-red-500">*</span>
                        </label>
                        <select name="property_id" x-model="selectedProperty" @change="filterStaff()" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                            <option value="">Select property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Assign To <span class="text-red-500">*</span>
                        </label>
                        <select name="assigned_to" x-model="selectedStaff" required
                            :disabled="!selectedProperty"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100">
                            <option value="">
                                <span x-show="!selectedProperty">Select property first</span>
                                <span x-show="selectedProperty && filteredStaff.length === 0">No staff in this property</span>
                                <span x-show="selectedProperty && filteredStaff.length > 0">Select staff member</span>
                            </option>
                            <template x-for="staff in filteredStaff" :key="staff.id">
                                <option :value="staff.id" x-text="`${staff.user.name} - ${staff.staff_role} (${staff.department?.name || 'No Dept'})`"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Department</label>
                        <select name="department_id"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                            <option value="">None</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Task Details -->
            <div class="bg-white/50 rounded-xl p-6 border border-white/30">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>Task Details
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Task Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g., Clean Room 101">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" rows="4" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500"
                            placeholder="Provide detailed instructions..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Task Type <span class="text-red-500">*</span>
                            </label>
                            <select name="task_type" required
                                class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                                <option value="cleaning">Cleaning</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inspection">Inspection</option>
                                <option value="delivery">Delivery</option>
                                <option value="customer_service">Customer Service</option>
                                <option value="administrative">Administrative</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" required
                                class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                                <option value="low">🟢 Low</option>
                                <option value="medium" selected>🟡 Medium</option>
                                <option value="high">🟠 High</option>
                                <option value="urgent">🔴 Urgent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                            <input type="text" name="location"
                                class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., Room 101, Lobby">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white/50 rounded-xl p-6 border border-white/30">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-calendar text-blue-600 mr-2"></i>Schedule
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Scheduled Date & Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="scheduled_at" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Due Date & Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="due_at" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="requires_photo_proof" value="1"
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="ml-3 text-sm font-semibold text-gray-700">
                            <i class="fas fa-camera mr-2 text-blue-600"></i>
                            Require photo proof upon completion
                        </span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('owner.tasks.index') }}" 
                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-semibold transition-all">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-semibold transition-all shadow-lg">
                    <i class="fas fa-check mr-2"></i>Assign Task
                </button>
            </div>
        </form>
    </div>
@endsection


