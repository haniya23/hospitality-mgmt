@extends('layouts.app')

@section('title', 'Create Accommodation')

@section('header')
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Create Accommodation</h1>
            <p class="text-gray-600">Add a new room or accommodation to your property</p>
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
<div class="space-y-6">
    <form method="POST" action="{{ route('accommodations.store') }}" x-data="accommodationForm()" x-init="init()" class="space-y-6">
        @csrf
        
        <!-- Basic Information -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">Property *</label>
                    <select name="property_id" id="property_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 select2-dropdown">
                        <option value="">Select Property</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="custom_name" class="block text-sm font-medium text-gray-700 mb-2">Accommodation Name *</label>
                    <input type="text" name="custom_name" id="custom_name" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('custom_name') }}">
                    @error('custom_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="predefined_type_id" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="predefined_type_id" id="predefined_type_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 select2-dropdown">
                        <option value="">Select Type</option>
                        @foreach($predefinedTypes as $type)
                            <option value="{{ $type->id }}" {{ old('predefined_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">Base Price (₹) *</label>
                    <input type="number" name="base_price" id="base_price" required min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('base_price') }}">
                    @error('base_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_occupancy" class="block text-sm font-medium text-gray-700 mb-2">Max Occupancy *</label>
                    <input type="number" name="max_occupancy" id="max_occupancy" required min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('max_occupancy') }}">
                    @error('max_occupancy')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Size (sq ft)</label>
                    <input type="number" name="size" id="size" min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('size') }}">
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Describe the accommodation...">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Amenities -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Amenities</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($amenities as $amenity)
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                               {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                        <div class="flex items-center space-x-2">
                            <i class="{{ $amenity->icon }} text-gray-600"></i>
                            <span class="text-sm text-gray-700">{{ $amenity->name }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Photos -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Photos</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">Upload Photos</label>
                    <input type="file" name="photos[]" id="photos" multiple accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-500 mt-1">You can select multiple photos. Supported formats: JPEG, PNG, JPG, GIF (max 2MB each)</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('accommodations.index') }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Create Accommodation
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function accommodationForm() {
    return {
        init() {
            console.log('Accommodation form initialized');
        }
    }
}
</script>
@endpush
