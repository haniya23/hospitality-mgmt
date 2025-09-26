<!-- Accommodations Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Accommodations</h3>
                <p class="text-sm text-gray-600">Rooms and accommodation types</p>
            </div>
        </div>
        <button onclick="openAccommodationModal('{{ $property->uuid }}')" 
                class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
            Add Accommodation
        </button>
    </div>
    
    <div class="space-y-4">
        @forelse($property->propertyAccommodations as $accommodation)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-emerald-300 transition-colors">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">{{ $accommodation->display_name }}</h4>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">Max Occupancy:</span>
                                <span>{{ $accommodation->max_occupancy }} guests</span>
                            </div>
                            <div>
                                <span class="font-medium">Base Price:</span>
                                <span>₹{{ number_format($accommodation->base_price) }}/night</span>
                            </div>
                            <div>
                                <span class="font-medium">Status:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $accommodation->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $accommodation->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium">Type:</span>
                                <span>{{ $accommodation->predefinedType->name ?? 'Custom' }}</span>
                            </div>
                        </div>
                        @if($accommodation->description)
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">{{ Str::limit($accommodation->description, 100) }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2 lg:ml-4">
                        <button onclick="openAccommodationModal('{{ $property->uuid }}', {{ $accommodation->id }})" 
                                class="px-3 py-1 text-sm text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors">
                            Edit
                        </button>
                        <button onclick="deleteAccommodation('{{ $property->uuid }}', {{ $accommodation->id }})" 
                                class="px-3 py-1 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No accommodations yet</h3>
                <p class="text-gray-600 mb-4">Add your first accommodation to get started</p>
                <button onclick="openAccommodationModal('{{ $property->uuid }}')" 
                        class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    Add First Accommodation
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Accommodation Modal -->
<div id="accommodationModal" class="fixed inset-0 z-50 overflow-y-auto backdrop-blur-sm bg-black/40 hidden">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900" id="accommodationModalTitle">Add Accommodation</h3>
                    <p class="text-sm text-gray-600">Configure room and accommodation details</p>
                </div>
                <button onclick="closeAccommodationModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="accommodationForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Accommodation Type</label>
                            <select name="predefined_type_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent select2-dropdown">
                                <option value="">Select Type (Optional)</option>
                                @foreach(\App\Models\PredefinedAccommodationType::all() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Choose a predefined type or leave blank for custom</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Display Name</label>
                            <input type="text" name="custom_name" 
                                   placeholder="e.g., Deluxe Room, Villa Suite, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Occupancy</label>
                            <input type="number" name="max_occupancy" 
                                   value="2" min="1" max="20"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Maximum number of guests</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Base Price (₹)</label>
                            <input type="number" name="base_price" 
                                   value="0" min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Price per night</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4" 
                                  placeholder="Describe the accommodation, amenities, and special features..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <label class="ml-2 text-sm text-gray-700">Active (available for booking)</label>
                    </div>
                    
                    <input type="hidden" name="accommodation_id" id="accommodationId">
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button onclick="closeAccommodationModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="saveAccommodation('{{ $property->uuid }}')" 
                        class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                    Save Accommodation
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentAccommodationId = null;

function openAccommodationModal(propertyUuid, accommodationId = null) {
    currentAccommodationId = accommodationId;
    const modal = document.getElementById('accommodationModal');
    const title = document.getElementById('accommodationModalTitle');
    const form = document.getElementById('accommodationForm');
    
    if (accommodationId) {
        title.textContent = 'Edit Accommodation';
        // Load accommodation data
        loadAccommodationData(propertyUuid, accommodationId);
    } else {
        title.textContent = 'Add Accommodation';
        form.reset();
        document.getElementById('accommodationId').value = '';
    }
    
    modal.classList.remove('hidden');
    
    // Initialize Select2 for accommodation type dropdown
    $('select[name="predefined_type_id"]').select2({
        placeholder: 'Select Type (Optional)',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#accommodationModal')
    });
}

function closeAccommodationModal() {
    // Destroy Select2 instance before closing modal
    $('select[name="predefined_type_id"]').select2('destroy');
    document.getElementById('accommodationModal').classList.add('hidden');
    currentAccommodationId = null;
}

async function loadAccommodationData(propertyUuid, accommodationId) {
    try {
        const response = await fetch(`/properties/${propertyUuid}/accommodations/${accommodationId}/edit`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const accommodation = data.accommodation;
            const form = document.getElementById('accommodationForm');
            
            form.querySelector('select[name="predefined_type_id"]').value = accommodation.predefined_type_id || '';
            form.querySelector('input[name="custom_name"]').value = accommodation.custom_name || '';
            form.querySelector('input[name="max_occupancy"]').value = accommodation.max_occupancy || '';
            form.querySelector('input[name="base_price"]').value = accommodation.base_price || '';
            form.querySelector('textarea[name="description"]').value = accommodation.description || '';
            form.querySelector('input[name="is_active"]').checked = accommodation.is_active;
            document.getElementById('accommodationId').value = accommodation.id;
        }
    } catch (error) {
        console.error('Error loading accommodation:', error);
        showToast('Error loading accommodation data', 'error');
    }
}

async function saveAccommodation(propertyUuid) {
    console.log('saveAccommodation called with UUID:', propertyUuid);
    
    const form = document.getElementById('accommodationForm');
    if (!form) {
        console.error('Accommodation form not found!');
        showToast('Form not found. Please refresh the page.', 'error');
        return;
    }
    
    // Collect form data as JSON
    const formData = {
        predefined_type_id: form.querySelector('[name="predefined_type_id"]').value || null,
        custom_name: form.querySelector('[name="custom_name"]').value,
        max_occupancy: parseInt(form.querySelector('[name="max_occupancy"]').value),
        base_price: parseFloat(form.querySelector('[name="base_price"]').value),
        description: form.querySelector('[name="description"]').value || null,
        is_active: form.querySelector('[name="is_active"]').checked
    };
    
    // Debug: Log form data
    console.log('Accommodation form data being sent:', formData);
    console.log('JSON stringified:', JSON.stringify(formData));
    
    const saveButton = event.target;
    const originalText = saveButton.textContent;
    saveButton.textContent = 'Saving...';
    saveButton.disabled = true;
    
    const url = currentAccommodationId 
        ? `/properties/${propertyUuid}/accommodations/${currentAccommodationId}/update`
        : `/properties/${propertyUuid}/accommodations/store`;
    
    console.log('Making request to:', url);
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message || 'Accommodation saved successfully!', 'success');
            closeAccommodationModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error saving accommodation', 'error');
        }
    } catch (error) {
        console.error('Error saving accommodation:', error);
        showToast('Error saving accommodation. Please try again.', 'error');
    } finally {
        saveButton.textContent = originalText;
        saveButton.disabled = false;
    }
}

async function deleteAccommodation(propertyUuid, accommodationId) {
    if (!confirm('Are you sure you want to delete this accommodation?')) {
        return;
    }
    
    try {
        const response = await fetch(`/properties/${propertyUuid}/accommodations/${accommodationId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Accommodation deleted successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error deleting accommodation', 'error');
        }
    } catch (error) {
        console.error('Error deleting accommodation:', error);
        showToast('Error deleting accommodation. Please try again.', 'error');
    }
}
</script>
