<!-- Policies Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Policies</h3>
                <p class="text-sm text-gray-600">Check-in/out times and house rules</p>
            </div>
        </div>
        <button onclick="openPoliciesModal('{{ $property->uuid }}')" 
                class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            Edit Policies
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Time</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->policy?->check_in_time ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Time</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->policy?->check_out_time ?? 'Not set' }}</p>
            </div>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Policy</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->policy?->cancellation_policy ?: 'No cancellation policy set' }}</p>
            </div>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">House Rules</label>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-900">{{ $property->policy?->house_rules ?: 'No house rules set' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Policies Modal -->
<div id="policiesModal" class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40 hidden" style="z-index: 99999 !important;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Policies</h3>
                    <p class="text-sm text-gray-600">Set check-in/out times and house rules</p>
                </div>
                <button onclick="closePoliciesModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="policiesForm" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="check_in_time" class="block text-sm font-medium text-gray-700 mb-2">Check-in Time</label>
                            <input type="time" id="check_in_time" name="check_in_time" 
                                   value="{{ $property->policy?->check_in_time ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="check_out_time" class="block text-sm font-medium text-gray-700 mb-2">Check-out Time</label>
                            <input type="time" id="check_out_time" name="check_out_time" 
                                   value="{{ $property->policy?->check_out_time ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div>
                        <label for="cancellation_policy" class="block text-sm font-medium text-gray-700 mb-2">Cancellation Policy</label>
                        <textarea id="cancellation_policy" name="cancellation_policy" rows="4" 
                                  placeholder="Describe your cancellation policy..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ $property->policy?->cancellation_policy ?? '' }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Be clear about cancellation terms and refund policies</p>
                    </div>
                    
                    <div>
                        <label for="house_rules" class="block text-sm font-medium text-gray-700 mb-2">House Rules</label>
                        <textarea id="house_rules" name="house_rules" rows="4" 
                                  placeholder="List important house rules and guidelines..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ $property->policy?->house_rules ?? '' }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Include rules about smoking, pets, parties, noise, etc.</p>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button onclick="closePoliciesModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="savePolicies('{{ $property->uuid }}')" 
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                    Save Policies
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openPoliciesModal(propertyUuid) {
    document.getElementById('policiesModal').classList.remove('hidden');
}

function closePoliciesModal() {
    document.getElementById('policiesModal').classList.add('hidden');
}

async function savePolicies(propertyUuid) {
    const form = document.getElementById('policiesForm');
    if (!form) {
        showToast('Form not found. Please refresh the page.', 'error');
        return;
    }
    
    // Collect form data as JSON
    const formData = {
        section: 'policies',
        check_in_time: form.querySelector('[name="check_in_time"]').value || null,
        check_out_time: form.querySelector('[name="check_out_time"]').value || null,
        cancellation_policy: form.querySelector('[name="cancellation_policy"]').value || null,
        house_rules: form.querySelector('[name="house_rules"]').value || null
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
            showToast('Policies updated successfully!', 'success');
            closePoliciesModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating policies', 'error');
        }
    } catch (error) {
        showToast('Error updating policies. Please try again.', 'error');
    } finally {
        saveButton.textContent = originalText;
        saveButton.disabled = false;
    }
}
</script>
