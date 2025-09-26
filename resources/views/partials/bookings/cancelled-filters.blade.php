<div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row gap-4">
        <!-- Search -->
        <div class="flex-1 relative">
            <input type="text" 
                   x-model="search" 
                   @input="filterBookings()" 
                   placeholder="Search by guest name, property, or accommodation..."
                   class="w-full border border-gray-300 rounded-xl px-4 py-3 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-sm"></i>
            </div>
        </div>

        <!-- Property Filter -->
        <div class="sm:w-64">
            <select x-model="selectedProperty" 
                    @change="filterBookings()" 
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                <option value="">All Properties</option>
                <template x-for="property in properties" :key="property.id">
                    <option :value="property.id" x-text="property.name"></option>
                </template>
            </select>
        </div>
    </div>
</div>
