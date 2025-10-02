<!-- Photos Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Photos</h3>
                <p class="text-sm text-gray-600">Property images and gallery</p>
            </div>
        </div>
        <button onclick="openPhotosModal('{{ $property->uuid }}')" 
                class="w-full sm:w-auto px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition-colors">
            Manage Photos
        </button>
    </div>
    
    <div class="space-y-6">
        <!-- Main Photo -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">Main Photo</h4>
            <div class="relative">
                @if($property->photos->where('is_main', true)->first())
                    <img src="{{ Storage::url($property->photos->where('is_main', true)->first()->file_path) }}" 
                         alt="Main photo" 
                         class="w-full h-64 object-cover rounded-lg border border-gray-200">
                @else
                    <div class="w-full h-64 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">No main photo set</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Additional Photos -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">Additional Photos</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @forelse($property->photos->where('is_main', false) as $photo)
                    <div class="relative group">
                        <img src="{{ Storage::url($photo->file_path) }}" 
                             alt="Property photo" 
                             class="w-full h-24 object-cover rounded-lg border border-gray-200">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 rounded-lg transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <button onclick="deletePhoto('{{ $property->uuid }}', {{ $photo->id }})" 
                                    class="px-2 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700 transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No additional photos</h3>
                        <p class="text-gray-600 mb-4">Add photos to showcase your property</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Photos Modal -->
<div id="photosModal" class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40 hidden" style="z-index: 99999 !important;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Manage Photos</h3>
                    <p class="text-sm text-gray-600">Upload and manage property photos</p>
                </div>
                <button onclick="closePhotosModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="photosForm" enctype="multipart/form-data" class="space-y-6">
                    <!-- Upload New Photos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Photos</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-pink-400 transition-colors">
                            <input type="file" id="photos" name="photos[]" multiple accept="image/*" 
                                   class="hidden" onchange="previewPhotos(this)">
                            <label for="photos" class="cursor-pointer">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-sm text-gray-600">Click to upload photos or drag and drop</p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 10MB each</p>
                            </label>
                        </div>
                        
                        <!-- Photo Preview -->
                        <div id="photoPreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4 hidden">
                            <!-- Preview images will be inserted here -->
                        </div>
                    </div>
                    
                    <!-- Set Main Photo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Set Main Photo</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($property->photos as $photo)
                                <div class="relative">
                                    <img src="{{ Storage::url($photo->file_path) }}" 
                                         alt="Property photo" 
                                         class="w-full h-24 object-cover rounded-lg border border-gray-200">
                                    <div class="absolute top-2 right-2">
                                        <input type="radio" name="main_photo_id" value="{{ $photo->id }}" 
                                               {{ $photo->is_main ? 'checked' : '' }}
                                               class="w-4 h-4 text-pink-600 border-gray-300 focus:ring-pink-500">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button onclick="closePhotosModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="savePhotos('{{ $property->uuid }}')" 
                        class="px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 transition-colors">
                    Save Photos
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openPhotosModal(propertyUuid) {
    document.getElementById('photosModal').classList.remove('hidden');
}

function closePhotosModal() {
    document.getElementById('photosModal').classList.add('hidden');
}

function previewPhotos(input) {
    const preview = document.getElementById('photoPreview');
    preview.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        preview.classList.remove('hidden');
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}" 
                         class="w-full h-24 object-cover rounded-lg border border-gray-200">
                    <div class="absolute top-2 right-2">
                        <input type="radio" name="main_photo_new" value="${index}" 
                               class="w-4 h-4 text-pink-600 border-gray-300 focus:ring-pink-500">
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else {
        preview.classList.add('hidden');
    }
}

async function savePhotos(propertyUuid) {
    const form = document.getElementById('photosForm');
    const formData = new FormData(form);
    
    const saveButton = event.target;
    const originalText = saveButton.textContent;
    saveButton.textContent = 'Saving...';
    saveButton.disabled = true;
    
    try {
        const response = await fetch(`/properties/${propertyUuid}/photos`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Photos updated successfully!', 'success');
            closePhotosModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating photos', 'error');
        }
    } catch (error) {
        showToast('Error updating photos. Please try again.', 'error');
    } finally {
        saveButton.textContent = originalText;
        saveButton.disabled = false;
    }
}

async function deletePhoto(propertyUuid, photoId) {
    if (!confirm('Are you sure you want to delete this photo?')) {
        return;
    }
    
    try {
        const response = await fetch(`/properties/${propertyUuid}/photos/${photoId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Photo deleted successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error deleting photo', 'error');
        }
    } catch (error) {
        showToast('Error deleting photo. Please try again.', 'error');
    }
}
</script>
