@push('styles')
<style>
    .partner-card { 
        background: white; 
        transition: all 0.3s ease;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    .partner-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 40px rgba(0,0,0,0.15); 
    }
    .status-active { 
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #059669; 
    }
    .status-pending { 
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706; 
    }
    .status-inactive { 
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626; 
    }
    .partner-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .action-btn {
        transition: all 0.2s ease;
        border-radius: 12px;
    }
    .action-btn:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

<div class="space-y-4 overflow-y-auto">
    <template x-for="partner in filteredPartners" :key="partner.id">
        <div class="partner-card p-4 sm:p-6">
            <!-- Action buttons moved to top for better mobile experience -->
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl partner-icon flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2 mb-1">
                            <h3 class="font-semibold text-gray-800 text-lg truncate" x-text="partner.partner_name"></h3>
                            <template x-if="partner.status">
                                <span class="inline-block px-3 py-1 text-xs rounded-full font-medium self-start sm:self-auto mt-1 sm:mt-0" 
                                      :class="{
                                          'status-active': partner.status === 'active',
                                          'status-pending': partner.status === 'pending',
                                          'status-inactive': partner.status === 'inactive'
                                      }"
                                      x-text="partner.status.charAt(0).toUpperCase() + partner.status.slice(1)">
                                </span>
                            </template>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs text-gray-500" x-text="partner.partner_type"></span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-500" x-text="partner.contact_user ? partner.contact_user.name : 'No contact'"></span>
                        </div>
                    </div>
                </div>
                <!-- Edit and Delete buttons moved to top right -->
                <div class="flex gap-2 flex-shrink-0 ml-3">
                    <!-- View button -->
                    <a :href="'/b2b/' + partner.uuid" 
                       class="w-10 h-10 bg-gradient-to-r from-purple-100 to-purple-200 text-purple-700 hover:from-purple-200 hover:to-purple-300 rounded-xl font-medium text-sm transition action-btn flex items-center justify-center"
                       title="View Partner">
                        <i class="fas fa-eye"></i>
                    </a>
                    
                    <!-- Edit button -->
                    <a :href="'/b2b/' + partner.uuid + '/edit'" 
                       class="w-10 h-10 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-700 hover:from-blue-200 hover:to-blue-300 rounded-xl font-medium text-sm transition action-btn flex items-center justify-center"
                       title="Edit Partner">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
            
            <!-- Partner stats -->
            <div class="flex justify-between items-center mb-4 text-center sm:text-left">
                <div>
                    <div class="text-xl font-bold text-gray-800" x-text="(partner.reservations_count || 0) + ' bookings'"></div>
                    <div class="text-sm text-gray-500" x-text="partner.commission_rate + '% commission'"></div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-4">
                <div class="flex items-center space-x-2 mb-3">
                    <i class="fas fa-user text-gray-500"></i>
                    <span class="text-sm text-gray-600 font-medium" x-text="partner.contact_person"></span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-800" x-text="partner.reservations_count || 0"></div>
                        <div class="text-xs text-gray-500">Total Bookings</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-800" x-text="partner.commission_rate + '%'"></div>
                        <div class="text-xs text-gray-500">Commission</div>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3 mb-4">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-phone text-blue-500"></i>
                        <span class="text-gray-700" x-text="partner.phone"></span>
                    </div>
                    <div class="flex gap-2">
                        <a :href="'tel:' + partner.phone" 
                           class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors">
                            <i class="fas fa-phone w-3 h-3"></i>
                        </a>
                        <a :href="'https://wa.me/' + partner.phone.replace(/[^0-9]/g, '')" 
                           target="_blank"
                           class="p-2 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors">
                            <i class="fab fa-whatsapp w-3 h-3"></i>
                        </a>
                    </div>
                </div>
                <div x-show="partner.email" class="flex items-center space-x-2 mt-2 text-sm">
                    <i class="fas fa-envelope text-blue-500"></i>
                    <span class="text-gray-700" x-text="partner.email"></span>
                </div>
            </div>

            <!-- Action buttons for bookings -->
            <div class="flex flex-col sm:flex-row gap-2">
                <button @click="openB2BBookingModal(partner)" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-green-600 hover:to-green-700 transition text-center action-btn">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Create Booking
                </button>
                <a :href="'/bookings/create?b2b_partner_uuid=' + partner.uuid" class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-purple-600 hover:to-purple-700 transition text-center action-btn">
                    <i class="fas fa-handshake mr-2"></i>
                    B2B Booking
                </a>
            </div>
        </div>
    </template>

    <template x-if="filteredPartners.length === 0">
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-purple-400 to-indigo-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                <i class="fas fa-handshake"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No B2B Partners Found</h3>
            <p class="text-gray-500 text-sm mb-4">Start by adding your first business partner.</p>
            <a href="{{ route('b2b.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-medium">
                <i class="fas fa-plus mr-2"></i>
                Add B2B Partner
            </a>
        </div>
    </template>
</div>