<!-- Bulk Download Button -->
<div class="bg-white rounded-2xl p-4 shadow-sm mb-4">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
        <button @click="openBulkDownloadModal()" 
                class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium text-sm hover:bg-purple-700 transition flex items-center space-x-2">
            <i class="fas fa-download"></i>
            <span>Bulk Download Invoices</span>
        </button>
    </div>
</div>

<!-- Property Filter -->
<div class="bg-white rounded-2xl p-4 shadow-sm">
    <select x-model="selectedProperty" 
            class="w-full bg-transparent border-none text-gray-800 font-medium focus:ring-0">
        <option value="">All Properties</option>
        <template x-for="property in properties" :key="property.id">
            <option :value="property.name" x-text="property.name"></option>
        </template>
    </select>
</div>