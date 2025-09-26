<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">B2B Partners (<span x-text="filteredPartners.length"></span>)</h3>
    </div>
    
    <div class="divide-y divide-gray-200">
        <template x-for="partner in filteredPartners" :key="partner.id">
            <div class="p-4 sm:p-6 hover:bg-gray-50">
                <!-- Mobile Layout -->
                <div class="block sm:hidden">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 text-sm" x-text="partner.partner_name"></h4>
                            <span class="inline-block px-2 py-1 text-xs rounded-full mt-1" 
                                  :class="partner.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                  x-text="partner.status.charAt(0).toUpperCase() + partner.status.slice(1)"></span>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500" x-text="partner.reservations_count + ' bookings'"></div>
                        </div>
                    </div>
                    
                    <div class="text-xs text-gray-600 space-y-1 mb-3">
                        <div>👤 <span x-text="partner.contact_user ? partner.contact_user.name : 'N/A'"></span></div>
                        <div>📱 <span x-text="partner.phone"></span></div>
                        <div x-show="partner.email">✉️ <span x-text="partner.email"></span></div>
                        <div class="text-gray-500">
                            Added <span x-text="formatDate(partner.created_at)"></span>
                        </div>
                    </div>
                    
                    <!-- Mobile Action Buttons -->
                    <div class="flex gap-2 justify-center">
                        <a :href="'tel:' + partner.phone" 
                           class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors">
                            <i class="fas fa-phone w-3 h-3"></i>
                        </a>
                        <a :href="'https://wa.me/' + partner.phone.replace(/[^0-9]/g, '')" 
                           target="_blank"
                           class="p-2 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors">
                            <i class="fab fa-whatsapp w-3 h-3"></i>
                        </a>
                        <a :href="'/b2b/' + partner.uuid" 
                           class="p-2 bg-purple-100 text-purple-600 rounded-full hover:bg-purple-200 transition-colors">
                            <i class="fas fa-eye w-3 h-3"></i>
                        </a>
                        <a :href="'/b2b/' + partner.uuid + '/edit'" 
                           class="p-2 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition-colors">
                            <i class="fas fa-edit w-3 h-3"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Desktop Layout -->
                <div class="hidden sm:flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h4 class="font-medium text-gray-900" x-text="partner.partner_name"></h4>
                            <span class="px-2 py-1 text-xs rounded-full" 
                                  :class="partner.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                  x-text="partner.status.charAt(0).toUpperCase() + partner.status.slice(1)"></span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div>👤 <span x-text="partner.contact_user ? partner.contact_user.name : 'N/A'"></span></div>
                            <div>📱 <span x-text="partner.phone"></span></div>
                            <div x-show="partner.email">✉️ <span x-text="partner.email"></span></div>
                            <div class="text-xs text-gray-500">
                                Added <span x-text="formatDate(partner.created_at)"></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="text-right mr-3">
                            <div class="text-sm text-gray-500" x-text="partner.reservations_count + ' bookings'"></div>
                        </div>
                        <div class="flex gap-2">
                            <a :href="'tel:' + partner.phone" 
                               class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors">
                                <i class="fas fa-phone w-4 h-4"></i>
                            </a>
                            <a :href="'https://wa.me/' + partner.phone.replace(/[^0-9]/g, '')" 
                               target="_blank"
                               class="p-2 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors">
                                <i class="fab fa-whatsapp w-4 h-4"></i>
                            </a>
                            <a :href="'/b2b/' + partner.uuid" 
                               class="p-2 bg-purple-100 text-purple-600 rounded-full hover:bg-purple-200 transition-colors">
                                <i class="fas fa-eye w-4 h-4"></i>
                            </a>
                            <a :href="'/b2b/' + partner.uuid + '/edit'" 
                               class="p-2 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition-colors">
                                <i class="fas fa-edit w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        
        <template x-if="filteredPartners.length === 0">
            <div class="p-6 sm:p-12 text-center text-gray-500">
                <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4 bg-gray-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-handshake text-xl sm:text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No B2B Partners Found</h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4">Start by adding your first business partner.</p>
                <a href="/b2b/create" 
                   class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all text-sm sm:text-base">
                    Add First Partner
                </a>
            </div>
        </template>
    </div>
</div>