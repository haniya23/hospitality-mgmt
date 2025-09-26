<form id="accommodationForm" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Accommodation Type</label>
            <select name="predefined_type_id" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Select Type (Optional)</option>
                @foreach($predefinedTypes as $type)
                    <option value="{{ $type->id }}" {{ ($accommodation && $accommodation->predefined_type_id == $type->id) ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Choose a predefined type or leave blank for custom</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Display Name</label>
            <input type="text" name="custom_name" 
                   value="{{ $accommodation ? $accommodation->custom_name : '' }}"
                   placeholder="e.g., Deluxe Room, Villa Suite, etc."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   required>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Occupancy</label>
            <input type="number" name="max_occupancy" 
                   value="{{ $accommodation ? $accommodation->max_occupancy : '2' }}"
                   min="1" max="20"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   required>
            <p class="text-xs text-gray-500 mt-1">Maximum number of guests</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Base Price (₹)</label>
            <input type="number" name="base_price" 
                   value="{{ $accommodation ? $accommodation->base_price : '0' }}"
                   min="0" step="0.01"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   required>
            <p class="text-xs text-gray-500 mt-1">Price per night</p>
        </div>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
        <textarea name="description" rows="4" 
                  placeholder="Describe the accommodation, amenities, and special features..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $accommodation ? $accommodation->description : '' }}</textarea>
    </div>
    
    <div class="flex items-center">
        <input type="checkbox" name="is_active" value="1" 
               {{ ($accommodation && $accommodation->is_active) || !$accommodation ? 'checked' : '' }}
               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <label class="ml-2 text-sm text-gray-700">Active (available for booking)</label>
    </div>
    
    @if($isEdit)
        <input type="hidden" name="accommodation_id" value="{{ $accommodation->id }}">
    @endif
</form>

