
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="p-4 sm:p-6 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">Your Properties</h3>
            <a href="{{ route('properties.index') }}" class="text-blue-600 font-medium text-sm">View all</a>
        </div>
    </div>
    
    <div class="p-4 sm:p-6 space-y-4">
        <template x-for="property in properties.slice(0, 3)" :key="property.id">
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-300">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                    <div class="flex items-center space-x-3 mb-3 sm:mb-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 text-lg" x-text="property.name"></h3>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="text-xs px-3 py-1 rounded-full font-medium"
                                      :class="{
                                          'bg-green-100 text-green-800': property.status === 'active',
                                          'bg-yellow-100 text-yellow-800': property.status === 'pending',
                                          'bg-red-100 text-red-800': property.status === 'inactive'
                                      }"
                                      x-text="property.status.charAt(0).toUpperCase() + property.status.slice(1)"></span>
                                <span class="text-xs text-gray-500" x-text="property.category?.name"></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <div class="text-xl font-bold text-gray-800" x-text="(property.property_accommodations_count || 0) + ' accommodations'"></div>
                        <div class="text-sm text-gray-500" x-text="(property.bookings_count || 0) + ' bookings'"></div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-4">
                    <div class="flex items-center space-x-2 mb-3">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm text-gray-600 font-medium" x-text="property.location?.city?.name + ', ' + property.location?.city?.district?.state?.name"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800" x-text="property.property_accommodations_count || 0"></div>
                            <div class="text-xs text-gray-500">Accommodations</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800" x-text="property.bookings_count || 0"></div>
                            <div class="text-xs text-gray-500">Bookings</div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2">
                    <a :href="'/properties/' + property.uuid + '/edit'" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-blue-600 hover:to-blue-700 transition text-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Property
                    </a>
                    <div class="flex gap-2">
                        <a :href="'/bookings/create?property_id=' + property.id" class="bg-gradient-to-r from-green-100 to-green-200 text-green-700 py-3 px-4 rounded-xl font-medium text-sm hover:from-green-200 hover:to-green-300 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </template>
        
        <template x-if="properties.length === 0">
            <div class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Create Your First Property</h4>
                <p class="text-gray-500 text-sm mb-4">Start managing your hospitality business by adding a property.</p>
                <a href="{{ route('properties.create') }}" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl font-medium inline-block shadow-lg hover:shadow-xl transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Property
                </a>
            </div>
        </template>
    </div>
</div>