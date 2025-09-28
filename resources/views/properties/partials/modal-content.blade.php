@if($section === 'basic')
    <!-- Basic Information Form -->
    <form id="propertyForm" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Property Name</label>
                <input type="text" name="name" value="{{ $property->name }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                <select name="property_category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select Property Type</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $property->property_category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="4" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $property->description }}</textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Owner Name</label>
                <input type="text" name="owner_name" value="{{ $property->owner_name }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="draft" {{ $property->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ $property->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ $property->status === 'active' ? 'selected' : '' }}>Active</option>
                </select>
            </div>
        </div>
    </form>

@elseif($section === 'location')
    <!-- Location Information Form -->
    <form id="propertyForm" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
            <textarea name="address" rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $property->location->address ?? '' }}</textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                <select name="country_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ ($property->location->country_id ?? '') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                <select name="state_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select State</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ ($property->location->state_id ?? '') == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">District</label>
                <select name="district_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ ($property->location->district_id ?? '') == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                <select name="city_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select City</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ ($property->location->city_id ?? '') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                <select name="pincode_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select Pincode</option>
                    @foreach($pincodes as $pincode)
                        <option value="{{ $pincode->id }}" {{ ($property->location->pincode_id ?? '') == $pincode->id ? 'selected' : '' }}>
                            {{ $pincode->code }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                <input type="text" name="latitude" value="{{ $property->location->latitude ?? '' }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
            <input type="text" name="longitude" value="{{ $property->location->longitude ?? '' }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </form>

@elseif($section === 'amenities')
    <!-- Amenities Form -->
    <form id="propertyForm" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Select Amenities</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($amenities as $amenity)
                    <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                               {{ $property->amenities->contains($amenity->id) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">{{ $amenity->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </form>

@elseif($section === 'policies')
    <!-- Policies Form -->
    <form id="propertyForm" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Time</label>
                <input type="time" name="check_in_time" value="{{ $property->policy->check_in_time ?? '' }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Time</label>
                <input type="time" name="check_out_time" value="{{ $property->policy->check_out_time ?? '' }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Policy</label>
            <textarea name="cancellation_policy" rows="4" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $property->policy->cancellation_policy ?? '' }}</textarea>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">House Rules</label>
            <textarea name="house_rules" rows="4" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $property->policy->house_rules ?? '' }}</textarea>
        </div>
    </form>
@endif

