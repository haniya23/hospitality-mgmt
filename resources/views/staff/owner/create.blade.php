@extends('layouts.app')

@section('title', 'Add Staff Member - Stay loops')
@section('page-title', 'Add Staff Member')

@push('scripts')
<script>
function staffForm() {
    return {
        selectedProperty: '{{ old("property_id") }}',
        selectedRole: '{{ old("staff_role") }}',
        selectedSupervisor: '{{ old("reports_to") }}',
        allStaff: @json($staffData ?? []),
        availableSupervisors: [],
        
        init() {
            this.filterSupervisors();
        },
        
        filterSupervisors() {
            this.availableSupervisors = [];
            
            if (!this.selectedProperty || !this.selectedRole) {
                return;
            }
            
            if (this.selectedRole === 'manager') {
                this.selectedSupervisor = '';
                return;
            }
            
            const propertyId = parseInt(this.selectedProperty);
            
            if (this.selectedRole === 'supervisor') {
                this.availableSupervisors = this.allStaff.filter(staff => 
                    staff.property_id === propertyId && 
                    staff.role === 'manager'
                ).map(staff => ({
                    id: staff.id,
                    label: `${staff.name} (Manager) - ${staff.job_title}`
                }));
            } else if (this.selectedRole === 'staff') {
                this.availableSupervisors = this.allStaff.filter(staff => 
                    staff.property_id === propertyId && 
                    (staff.role === 'supervisor' || staff.role === 'manager')
                ).map(staff => ({
                    id: staff.id,
                    label: `${staff.name} (${staff.role === 'manager' ? 'Manager' : 'Supervisor'}) - ${staff.job_title}`
                }));
            }
        }
    }
}
</script>
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
            <a href="{{ route('owner.staff.index') }}" class="hover:text-blue-600 transition-colors">Staff</a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-700 font-medium">Add New</span>
        </nav>
    </div>

    <div class="bg-gradient-to-br from-white/95 to-blue-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-4 sm:p-6 border border-white/20" 
         x-data="staffForm()">
        <!-- Enhanced Header with Icon -->
        <div class="flex items-center space-x-4 mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">Add New Staff Member</h2>
                <p class="text-sm text-blue-600 font-medium mt-1">Create a new staff account for your property</p>
                <div class="flex items-center mt-2 space-x-4 text-xs text-gray-500">
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-1"></i>
                        Takes 3 minutes
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Secure access
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('owner.staff.store') }}" method="POST" class="space-y-6 sm:space-y-8">
            @csrf

            <!-- Personal Information -->
            <div class="bg-white/50 rounded-xl p-4 sm:p-6 border border-white/30">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                        <p class="text-sm text-gray-600">Basic details about the staff member</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Enter full name">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="email@example.com">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="1234567890">
                        @error('mobile_number')
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Minimum 6 characters">
                        @error('password')
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone (Optional)</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Additional contact">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Emergency Contact</label>
                        <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Emergency contact number">
                    </div>
                </div>
            </div>

            <!-- Employment Details -->
            <div class="bg-white/50 rounded-xl p-4 sm:p-6 border border-white/30">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-briefcase text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Employment Details</h3>
                        <p class="text-sm text-gray-600">Work assignment and role information</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Property <span class="text-red-500">*</span>
                        </label>
                        <select name="property_id" required x-model="selectedProperty" @change="filterSupervisors()"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">Select Property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('property_id')
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Department</label>
                        <select name="department_id"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Staff Role <span class="text-red-500">*</span>
                        </label>
                        <select name="staff_role" required x-model="selectedRole" @change="filterSupervisors()"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">Select Role</option>
                            <option value="manager">Manager (Reports to: Owner only)</option>
                            <option value="supervisor">Supervisor (Reports to: Manager)</option>
                            <option value="staff">Staff (Reports to: Supervisor or Manager)</option>
                        </select>
                        @error('staff_role')
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1" x-show="selectedRole">
                            <template x-if="selectedRole === 'manager'">
                                <span><i class="fas fa-info-circle mr-1"></i> Managers report directly to the property owner</span>
                            </template>
                            <template x-if="selectedRole === 'supervisor'">
                                <span><i class="fas fa-info-circle mr-1"></i> Supervisors must report to a Manager</span>
                            </template>
                            <template x-if="selectedRole === 'staff'">
                                <span><i class="fas fa-info-circle mr-1"></i> Staff members report to Supervisors or Managers</span>
                            </template>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Job Title</label>
                        <input type="text" name="job_title" value="{{ old('job_title') }}"
                            placeholder="e.g., Senior Housekeeper"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>

                    <div class="sm:col-span-2" x-show="selectedRole && selectedRole !== 'manager'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Reports To (Supervisor) 
                            <span class="text-red-500" x-show="selectedRole === 'supervisor'">*</span>
                        </label>
                        <select name="reports_to" x-model="selectedSupervisor"
                            :required="selectedRole === 'supervisor'"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="" x-text="!selectedProperty ? 'Select property first' : (availableSupervisors.length > 0 ? 'Select supervisor' : 'No supervisors available')"></option>
                            <template x-for="supervisor in availableSupervisors" :key="supervisor.id">
                                <option :value="supervisor.id" x-text="supervisor.label"></option>
                            </template>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-show="selectedRole === 'supervisor'">
                                <i class="fas fa-exclamation-circle mr-1 text-amber-500"></i>
                                Supervisors must report to a Manager in the same property
                            </span>
                            <span x-show="selectedRole === 'staff'">
                                <i class="fas fa-info-circle mr-1"></i>
                                Select the immediate supervisor or manager this person reports to
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Employment Type <span class="text-red-500">*</span>
                        </label>
                        <select name="employment_type" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="temporary" {{ old('employment_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Join Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="join_date" value="{{ old('join_date', date('Y-m-d')) }}" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 sm:py-4 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-white/50 rounded-xl p-4 sm:p-6 border border-white/30">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-sticky-note text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Additional Information</h3>
                        <p class="text-sm text-gray-600">Optional notes and remarks</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="4"
                        class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Any additional information...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('owner.staff.index') }}" 
                    class="inline-flex justify-center items-center px-6 py-3 sm:py-4 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-semibold transition-all shadow-sm text-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <button type="submit" 
                    class="inline-flex justify-center items-center px-6 py-3 sm:py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-semibold transition-all shadow-lg text-center">
                    <i class="fas fa-user-plus mr-2"></i> Add Staff Member
                </button>
            </div>
        </form>
    </div>
@endsection
