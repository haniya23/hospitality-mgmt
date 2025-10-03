<div class="space-y-4">
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Sample JSON Format</h3>
        
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">JSON Structure:</label>
                <button 
                    type="button"
                    onclick="copySampleToClipboard()"
                    class="text-xs px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    Copy to Clipboard
                </button>
            </div>
            
            <div class="relative">
                <pre id="sample-json" class="bg-gray-900 text-green-400 p-4 rounded-lg text-xs overflow-x-auto border"><code>{!! json_encode($sampleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}</code></pre>
            </div>
        </div>
        
        <div class="space-y-2">
            <h4 class="text-md font-medium text-gray-800 dark:text-gray-200">Instructions:</h4>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 ml-4">
                <li>• Each country can have multiple states</li>
                <li>• Each state can have multiple districts</ the>
<li>• Each district can have multiple cities</li>
<li>• Each city can have multiple pincodes</li>
<li>• All fields are required except state/district codes</li>
<li>• Save the file with .json extension</li>
            </ul>
        </div>
        
        <!-- Download Sample Button -->
        <div class="mt-4">
            <a href="{{ asset('sample-locations.json') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download Sample JSON File
            </a>
        </div>
    </div>
</div>

<script>
function copySampleToClipboard() {
    const sampleText = `{!! json_encode($sampleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}`;
    navigator.clipboard.writeText(sampleText).then(function() {
        // Create a temporary notification
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-blue-500', 'hover:bg-blue-600');
        }, 2000);
    });
}
</script>
