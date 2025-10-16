<!-- Photos Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center space-x-3 mb-6">
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
    
    <!-- Property Photo -->
    <div>
        <h4 class="text-sm font-medium text-gray-700 mb-3">Property Photo</h4>
        @if($property->photos->first())
            <div class="relative group">
                <img src="{{ Storage::url($property->photos->first()->file_path) }}" 
                     alt="Property photo" class="w-full h-64 object-cover rounded-lg">
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center space-x-3">
                    <button onclick="document.getElementById('property-photo').click()" 
                            class="bg-white text-gray-900 px-4 py-2 rounded-lg font-medium">
                        Replace Photo
                    </button>
                    <button onclick="deletePhoto({{ $property->photos->first()->id }})" 
                            class="bg-red-500 text-white px-4 py-2 rounded-lg font-medium">
                        Delete Photo
                    </button>
                </div>
            </div>
        @else
            <div class="w-full h-64 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center cursor-pointer hover:border-pink-400" 
                 onclick="document.getElementById('property-photo').click()">
                <div class="text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <p class="text-sm text-gray-500">No photo uploaded</p>
                    <p class="text-xs text-gray-400">Click to upload</p>
                </div>
            </div>
        @endif
        <input type="file" id="property-photo" accept="image/*" class="hidden" onchange="uploadPhoto(this)">
    </div>
</div>

<script>
function uploadPhoto(input) {
    if (!input.files || !input.files[0]) return;
    
    const formData = new FormData();
    formData.append('photo', input.files[0]);
    formData.append('is_main', '1');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch(`/properties/{{ $property->uuid }}/photos/upload`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Upload failed: ' + data.error);
        }
    })
    .catch(error => {
        alert('Upload error: ' + error.message);
    });
}

function deletePhoto(photoId) {
    if (!confirm('Delete this photo?')) return;
    
    fetch(`/properties/{{ $property->uuid }}/photos/${photoId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Delete failed');
        }
    });
}
</script>
