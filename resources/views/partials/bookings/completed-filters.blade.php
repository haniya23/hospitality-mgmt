<!-- Quick Actions -->
<div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300 mb-4">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
            <i class="fas fa-bolt text-green-500 mr-2"></i>
            Quick Actions
        </h3>
        <div class="flex space-x-3">
            <button @click="exportCompletedBookings()" 
                    class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-2 rounded-xl font-medium text-sm hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center space-x-2">
                <i class="fas fa-download"></i>
                <span>Export Data</span>
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
    <div class="flex flex-col sm:flex-row gap-4">
        <!-- Search -->
        <div class="flex-1 relative">
            <input type="text" 
                   x-model="search" 
                   @input="filterBookings()" 
                   placeholder="Search by guest name, property, or accommodation..."
                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800 pl-12">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-lg"></i>
            </div>
        </div>

        <!-- Property Filter -->
        <div class="sm:w-64">
            <select x-model="selectedProperty" 
                    @change="filterBookings()" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white hover:border-gray-300 font-semibold text-gray-800">
                <option value="">All Properties</option>
                <template x-for="property in properties" :key="property.id">
                    <option :value="property.id" x-text="property.name"></option>
                </template>
            </select>
        </div>
    </div>
</div>
