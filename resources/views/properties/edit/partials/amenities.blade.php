<!-- Amenities Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Amenities</h3>
                <p class="text-sm text-gray-600">Facilities and services available</p>
            </div>
        </div>
        <button onclick="openAmenitiesModal('{{ $property->uuid }}')" 
                class="w-full sm:w-auto px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
            Edit Amenities
        </button>
    </div>
    
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @forelse($property->amenities as $amenity)
            <div class="flex items-center space-x-2 p-3 bg-orange-50 rounded-lg border border-orange-200">
                <svg class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm font-medium text-orange-800">{{ $amenity->name }}</span>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No amenities selected</h3>
                <p class="text-gray-600 mb-4">Add amenities to showcase what your property offers</p>
                <button onclick="openAmenitiesModal('{{ $property->uuid }}')" 
                        class="px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                    Add Amenities
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Amenities Modal -->
<div id="amenitiesModal" class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40 hidden" style="z-index: 99999 !important;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Amenities</h3>
                    <p class="text-sm text-gray-600">Select the amenities available at your property</p>
                </div>
                <button onclick="closeAmenitiesModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="amenitiesForm" class="space-y-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach(\App\Models\Amenity::all() as $amenity)
                            <div class="flex items-center">
                                <input type="checkbox" id="amenity_{{ $amenity->id }}" name="amenities[]" value="{{ $amenity->id }}" 
                                    {{ $property->amenities->contains($amenity->id) ? 'checked' : '' }}
                                    class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <label for="amenity_{{ $amenity->id }}" class="ml-2 block text-sm text-gray-900">{{ $amenity->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button onclick="closeAmenitiesModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="saveAmenities('{{ $property->uuid }}')" 
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors">
                    Save Amenities
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openAmenitiesModal(propertyUuid) {
    document.getElementById('amenitiesModal').classList.remove('hidden');
}

function closeAmenitiesModal() {
    document.getElementById('amenitiesModal').classList.add('hidden');
}

async function saveAmenities(propertyUuid) {
    console.log('saveAmenities called with UUID:', propertyUuid);
    
    const form = document.getElementById('amenitiesForm');
    if (!form) {
        console.error('Amenities form not found!');
        showToast('Form not found. Please refresh the page.', 'error');
        return;
    }
    
    // Collect selected amenities
    const selectedAmenities = [];
    const checkboxes = form.querySelectorAll('input[name="amenities[]"]:checked');
    checkboxes.forEach(checkbox => {
        selectedAmenities.push(parseInt(checkbox.value));
    });
    
    // Collect form data as JSON
    const formData = {
        section: 'amenities',
        amenities: selectedAmenities
    };
    
    // Debug: Log form data
    console.log('Amenities form data being sent:', formData);
    console.log('JSON stringified:', JSON.stringify(formData));
    
    const saveButton = event.target;
    const originalText = saveButton.textContent;
    saveButton.textContent = 'Saving...';
    saveButton.disabled = true;
    
    // Test with test endpoint first
    const testUrl = `/properties/${propertyUuid}/test-ajax`;
    console.log('Testing with URL:', testUrl);
    
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
        
        console.log('Test response status:', testResponse.status);
        const testData = await testResponse.json();
        console.log('Test response data:', testData);
        
        if (!testData.success) {
            showToast('Test failed: ' + testData.message, 'error');
            return;
        }
        
        // If test passes, proceed with actual update
        const url = `/properties/${propertyUuid}/update-section`;
        console.log('Making actual request to:', url);
        
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
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Response error:', errorText);
            showToast('Server error: ' + response.status, 'error');
            return;
        }
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            showToast('Amenities updated successfully!', 'success');
            closeAmenitiesModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating amenities', 'error');
        }
    } catch (error) {
        console.error('Error saving amenities:', error);
        showToast('Error updating amenities. Please try again.', 'error');
    } finally {
        saveButton.textContent = originalText;
        saveButton.disabled = false;
    }
}
</script>
