<!-- Location Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Location</h3>
                <p class="text-sm text-gray-600">Address and geographical details</p>
            </div>
        </div>
        <button onclick="openLocationModal('{{ $property->uuid }}')" 
                class="w-full sm:w-auto px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
            Edit
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->location->address ?? 'No address provided' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->location->country->name ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->location->state->name ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">District</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->location->district->name ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->location->city->name ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->location->pincode->code ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->location->latitude ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->location->longitude ?? 'Not set' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Location Modal -->
<div id="locationModal" class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40 hidden" style="z-index: 99999 !important;">
    <div class="flex min-h-full items-center justify-center p-2 sm:p-4">
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-xl sm:rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Location</h3>
                    <p class="text-sm text-gray-600">Update your property location details</p>
                </div>
                <button onclick="closeLocationModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="locationForm" class="space-y-6">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <input type="text" id="address" name="address" value="{{ $property->location->address ?? '' }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="country_id" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select id="country_id" name="country_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent select2-dropdown">
                                <option value="">Select Country</option>
                                @foreach(\App\Models\Country::all() as $country)
                                    <option value="{{ $country->id }}" {{ ($property->location->country_id ?? null) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="state_id" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                            <select id="state_id" name="state_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent select2-dropdown">
                                <option value="">Select State</option>
                                @foreach(\App\Models\State::all() as $state)
                                    <option value="{{ $state->id }}" {{ ($property->location->state_id ?? null) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="district_id" class="block text-sm font-medium text-gray-700 mb-2">District</label>
                            <select id="district_id" name="district_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent select2-dropdown">
                                <option value="">Select District</option>
                                @foreach(\App\Models\District::all() as $district)
                                    <option value="{{ $district->id }}" {{ ($property->location->district_id ?? null) == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="city_id" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <select id="city_id" name="city_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent select2-dropdown">
                                <option value="">Select City</option>
                                @foreach(\App\Models\City::all() as $city)
                                    <option value="{{ $city->id }}" {{ ($property->location->city_id ?? null) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="pincode_id" class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                            <select id="pincode_id" name="pincode_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent select2-dropdown">
                                <option value="">Select Pincode</option>
                                @foreach(\App\Models\Pincode::all() as $pincode)
                                    <option value="{{ $pincode->id }}" {{ ($property->location->pincode_id ?? null) == $pincode->id ? 'selected' : '' }}>
                                        {{ $pincode->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                            <input type="text" id="latitude" name="latitude" value="{{ $property->location->latitude ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                            <input type="text" id="longitude" name="longitude" value="{{ $property->location->longitude ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button onclick="closeLocationModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="saveLocation('{{ $property->uuid }}')" 
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openLocationModal(propertyUuid) {
    document.getElementById('locationModal').classList.remove('hidden');
    
    // Initialize Select2 for all location dropdowns
    $('#country_id, #state_id, #district_id, #city_id, #pincode_id').select2({
        placeholder: 'Select option',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#locationModal')
    });
}

function closeLocationModal() {
    // Destroy Select2 instances before closing modal
    $('#country_id, #state_id, #district_id, #city_id, #pincode_id').select2('destroy');
    document.getElementById('locationModal').classList.add('hidden');
}

async function saveLocation(propertyUuid) {
    const form = document.getElementById('locationForm');
    if (!form) {
        showToast('Form not found. Please refresh the page.', 'error');
        return;
    }
    
    // Collect form data as JSON
    const formData = {
        section: 'location',
        address: form.querySelector('[name="address"]').value || null,
        country_id: form.querySelector('[name="country_id"]').value || null,
        state_id: form.querySelector('[name="state_id"]').value || null,
        district_id: form.querySelector('[name="district_id"]').value || null,
        city_id: form.querySelector('[name="city_id"]').value || null,
        pincode_id: form.querySelector('[name="pincode_id"]').value || null,
        latitude: form.querySelector('[name="latitude"]').value || null,
        longitude: form.querySelector('[name="longitude"]').value || null
    };
    
    const saveButton = event.target;
    const originalText = saveButton.textContent;
    saveButton.textContent = 'Saving...';
    saveButton.disabled = true;
    
    // Test with test endpoint first
    const testUrl = `/properties/${propertyUuid}/test-ajax`;
    
    try {
        // First test the test endpoint
        const testResponse = await fetch(testUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const testData = await testResponse.json();
        
        if (!testData.success) {
            showToast('Test failed: ' + testData.message, 'error');
            return;
        }
        
        // If test passes, proceed with actual update
        const url = `/properties/${propertyUuid}/update-section`;
        
        const response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            showToast('Server error: ' + response.status, 'error');
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Location updated successfully!', 'success');
            closeLocationModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating location', 'error');
        }
    } catch (error) {
        showToast('Error updating location. Please try again.', 'error');
    } finally {
        saveButton.textContent = originalText;
        saveButton.disabled = false;
    }
}
</script>
