@extends('layouts.app')

@section('title', 'Maintenance & Renovation Details')

@section('header')
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Maintenance & Renovation Details</h1>
            <p class="text-gray-600">{{ $accommodation->custom_name }} - {{ $accommodation->property->name }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('accommodations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Accommodations
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('accommodations.maintenance.update', $accommodation) }}" class="space-y-6">
        @csrf
        
        <!-- Accommodation Info -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-bed text-white text-lg"></i>
                </div>
                <h2 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Accommodation Information</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Accommodation Name</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-xl font-medium text-gray-800">{{ $accommodation->custom_name }}</div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Property</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-xl font-medium text-gray-800">{{ $accommodation->property->name }}</div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-xl font-medium text-gray-800">{{ $accommodation->predefinedType->name ?? 'Custom' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Max Occupancy</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-xl font-medium text-gray-800">{{ $accommodation->max_occupancy }} guests</div>
                </div>
            </div>
        </div>

        <!-- Maintenance Details -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-wrench text-white text-lg"></i>
                </div>
                <h2 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">Maintenance Details</h2>
            </div>
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="maintenance_status" class="block text-sm font-semibold text-gray-700 mb-2">
                            Maintenance Status
                        </label>
                        <select name="maintenance_status" id="maintenance_status"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800">
                            <option value="none" {{ old('maintenance_status', $accommodation->maintenance_status) == 'none' ? 'selected' : '' }}>None</option>
                            <option value="scheduled" {{ old('maintenance_status', $accommodation->maintenance_status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="active" {{ old('maintenance_status', $accommodation->maintenance_status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('maintenance_status', $accommodation->maintenance_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="maintenance_cost" class="block text-sm font-semibold text-gray-700 mb-2">
                            Maintenance Cost (₹)
                        </label>
                        <input type="number" name="maintenance_cost" id="maintenance_cost" min="0" step="0.01"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                               value="{{ old('maintenance_cost', $accommodation->maintenance_cost) }}" placeholder="Enter maintenance cost">
                    </div>
                    
                    <div>
                        <label for="maintenance_start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            Start Date
                        </label>
                        <input type="date" name="maintenance_start_date" id="maintenance_start_date"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                               value="{{ old('maintenance_start_date', $accommodation->maintenance_start_date?->format('Y-m-d')) }}">
                    </div>
                    
                    <div>
                        <label for="maintenance_end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            End Date
                        </label>
                        <input type="date" name="maintenance_end_date" id="maintenance_end_date"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                               value="{{ old('maintenance_end_date', $accommodation->maintenance_end_date?->format('Y-m-d')) }}">
                    </div>
                </div>
                
                <div>
                    <label for="maintenance_description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Maintenance Description
                    </label>
                    <textarea name="maintenance_description" id="maintenance_description" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800 resize-none"
                              placeholder="Describe the maintenance work being done...">{{ old('maintenance_description', $accommodation->maintenance_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Renovation Details -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-hammer text-white text-lg"></i>
                </div>
                <h2 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-red-600 to-pink-600 bg-clip-text text-transparent">Renovation Details</h2>
            </div>
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="renovation_status" class="block text-sm font-semibold text-gray-700 mb-2">
                            Renovation Status
                        </label>
                        <select name="renovation_status" id="renovation_status"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800">
                            <option value="none" {{ old('renovation_status', $accommodation->renovation_status) == 'none' ? 'selected' : '' }}>None</option>
                            <option value="scheduled" {{ old('renovation_status', $accommodation->renovation_status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="active" {{ old('renovation_status', $accommodation->renovation_status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('renovation_status', $accommodation->renovation_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="renovation_cost" class="block text-sm font-semibold text-gray-700 mb-2">
                            Renovation Cost (₹)
                        </label>
                        <input type="number" name="renovation_cost" id="renovation_cost" min="0" step="0.01"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                               value="{{ old('renovation_cost', $accommodation->renovation_cost) }}" placeholder="Enter renovation cost">
                    </div>
                    
                    <div>
                        <label for="renovation_start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            Start Date
                        </label>
                        <input type="date" name="renovation_start_date" id="renovation_start_date"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                               value="{{ old('renovation_start_date', $accommodation->renovation_start_date?->format('Y-m-d')) }}">
                    </div>
                    
                    <div>
                        <label for="renovation_end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            End Date
                        </label>
                        <input type="date" name="renovation_end_date" id="renovation_end_date"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800"
                               value="{{ old('renovation_end_date', $accommodation->renovation_end_date?->format('Y-m-d')) }}">
                    </div>
                </div>
                
                <div>
                    <label for="renovation_description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Renovation Description
                    </label>
                    <textarea name="renovation_description" id="renovation_description" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800 resize-none"
                              placeholder="Describe the renovation work being done...">{{ old('renovation_description', $accommodation->renovation_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pb-20 lg:pb-8">
            <a href="{{ route('accommodations.index') }}" 
               class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-center">
                Cancel
            </a>
            <button type="submit" 
                    class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg hover:from-orange-700 hover:to-red-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
