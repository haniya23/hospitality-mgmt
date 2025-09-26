<!-- Basic Information Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                <p class="text-sm text-gray-600">Property name, type, and description</p>
            </div>
        </div>
        <button onclick="openBasicInfoModal('{{ $property->uuid }}')" 
                class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            Edit
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Property Name</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->name }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->category->name ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->description ?: 'No description provided' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Owner Name</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->owner->name ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    @if($property->status === 'active') bg-green-100 text-green-800
                    @elseif($property->status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($property->status) }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Basic Information Modal -->
<div id="basicInfoModal" class="fixed inset-0 z-50 overflow-y-auto backdrop-blur-sm bg-black/40 hidden">
    <div class="flex min-h-full items-center justify-center p-2 sm:p-4">
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-xl sm:rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Basic Information</h3>
                    <p class="text-sm text-gray-600">Update your property details</p>
                </div>
                <button onclick="closeBasicInfoModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="basicInfoForm" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Property Name</label>
                        <input type="text" id="name" name="name" value="{{ $property->name }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                    </div>
                    
                    <div>
                        <label for="property_category_id" class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                        <select id="property_category_id" name="property_category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-dropdown">
                            <option value="">Select Property Type</option>
                            @foreach(\App\Models\PropertyCategory::all() as $category)
                                <option value="{{ $category->id }}" {{ $property->property_category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $property->description }}</textarea>
                    </div>
                    
                    <div>
                        <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-2">Owner Name</label>
                        <input type="text" id="owner_name" name="owner_name" value="{{ $property->owner->name ?? '' }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button onclick="closeBasicInfoModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="saveBasicInfo('{{ $property->uuid }}')" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openBasicInfoModal(propertyUuid) {
    document.getElementById('basicInfoModal').classList.remove('hidden');
    
    // Initialize Select2 for property category dropdown
    $('#property_category_id').select2({
        placeholder: 'Select Property Type',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#basicInfoModal')
    });
}

function closeBasicInfoModal() {
    // Destroy Select2 instance before closing modal
    $('#property_category_id').select2('destroy');
    document.getElementById('basicInfoModal').classList.add('hidden');
}

async function saveBasicInfo(propertyUuid) {
    console.log('saveBasicInfo called with UUID:', propertyUuid);
    
    const form = document.getElementById('basicInfoForm');
    if (!form) {
        console.error('Form not found!');
        showToast('Form not found. Please refresh the page.', 'error');
        return;
    }
    
    // Collect form data as JSON
    const formData = {
        section: 'basic',
        name: form.querySelector('[name="name"]').value,
        property_category_id: form.querySelector('[name="property_category_id"]').value || null,
        description: form.querySelector('[name="description"]').value || null,
        owner_name: form.querySelector('[name="owner_name"]').value || null
    };
    
    // Debug: Log form data
    console.log('Form data being sent:', formData);
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
            showToast('Basic information updated successfully!', 'success');
            closeBasicInfoModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating basic information', 'error');
        }
    } catch (error) {
        console.error('Error saving basic info:', error);
        showToast('Error updating basic information. Please try again.', 'error');
    } finally {
        saveButton.textContent = originalText;
        saveButton.disabled = false;
    }
}
</script>
