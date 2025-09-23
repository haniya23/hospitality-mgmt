<!-- Desktop Table View -->
<div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bookings</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <template x-for="customer in filteredCustomers" :key="customer.id">
                    <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 transition-all">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center shadow-lg">
                                        <span class="text-white font-bold text-lg" x-text="customer.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900" x-text="customer.name"></div>
                                    <div class="text-sm text-gray-500" x-text="customer.id_number"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900" x-text="customer.email"></div>
                            <div class="text-sm text-gray-500" x-text="customer.mobile_number"></div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800" x-text="customer.reservations_count || 0"></span>
                        </td>
                        <td class="px-6 py-4">
                            <a :href="`/customers/${customer.id}/edit`" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-indigo-600 hover:to-purple-700 transition-all">Edit</a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Card View -->
<div class="md:hidden space-y-4">
    <template x-for="customer in filteredCustomers" :key="customer.id">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all">
            <div class="flex items-center space-x-4 mb-3">
                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-lg" x-text="customer.name.charAt(0).toUpperCase()"></span>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900" x-text="customer.name"></h3>
                    <p class="text-sm text-gray-500" x-text="customer.id_number"></p>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800" x-text="customer.reservations_count || 0"></span>
            </div>
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm">
                    <i class="fas fa-envelope text-gray-400 w-4 mr-2"></i>
                    <span class="text-gray-600" x-text="customer.email"></span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-phone text-gray-400 w-4 mr-2"></i>
                    <span class="text-gray-600" x-text="customer.mobile_number"></span>
                </div>
            </div>
            <a :href="`/customers/${customer.id}/edit`" class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:from-indigo-600 hover:to-purple-700 transition-all text-center block">
                <i class="fas fa-edit mr-2"></i>Edit Customer
            </a>
        </div>
    </template>
</div>