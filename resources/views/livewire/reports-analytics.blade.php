<div class="space-y-4 sm:space-y-6" x-cloak x-data="{ currentTab: '{{ $reportType }}' }" 
     @report-changed.window="currentTab = $event.detail">
    <!-- Header Controls -->
    <div class="flex flex-col gap-4">
        <div class="text-center sm:text-left">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Reports & Analytics</h2>
            <p class="text-sm sm:text-base text-gray-600">Business insights and performance metrics</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <select wire:model.live="dateRange" class="px-3 py-2 border border-gray-300 rounded-lg text-sm flex-1 sm:flex-none">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 3 months</option>
                <option value="365">Last year</option>
            </select>
            
            <select wire:model.live="property_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm flex-1 sm:flex-none">
                <option value="all">All Properties</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                @endforeach
            </select>
            
            <button wire:click="exportReport('csv')" 
                    class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium text-sm">
                Export CSV
            </button>
        </div>
    </div>

    <!-- Report Type Tabs -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto scrollbar-hide">
            <button wire:click="switchReport('overview')" 
                    class="flex-shrink-0 px-4 sm:px-6 py-3 sm:py-4 text-center font-medium transition-all duration-200 text-sm sm:text-base whitespace-nowrap
                    {{ $reportType === 'overview' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}" 
                    x-cloak>
                Overview
            </button>
            <button wire:click="switchReport('bookings')" 
                    class="flex-shrink-0 px-4 sm:px-6 py-3 sm:py-4 text-center font-medium transition-all duration-200 text-sm sm:text-base whitespace-nowrap
                    {{ $reportType === 'bookings' ? 'bg-emerald-50 text-emerald-600 border-b-2 border-emerald-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}" 
                    x-cloak>
                Bookings
            </button>
            <button wire:click="switchReport('commissions')" 
                    class="flex-shrink-0 px-4 sm:px-6 py-3 sm:py-4 text-center font-medium transition-all duration-200 text-sm sm:text-base whitespace-nowrap
                    {{ $reportType === 'commissions' ? 'bg-purple-50 text-purple-600 border-b-2 border-purple-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}" 
                    x-cloak>
                Commissions
            </button>
            <button wire:click="switchReport('customers')" 
                    class="flex-shrink-0 px-4 sm:px-6 py-3 sm:py-4 text-center font-medium transition-all duration-200 text-sm sm:text-base whitespace-nowrap
                    {{ $reportType === 'customers' ? 'bg-orange-50 text-orange-600 border-b-2 border-orange-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}" 
                    x-cloak>
                Customers
            </button>
        </div>

        <!-- Report Content -->
        <div class="p-4 sm:p-6" x-cloak x-show="true" x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 transform translate-y-2" 
             x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($reportType === 'overview')
                <!-- Overview Dashboard -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-4 text-white shadow-lg">
                        <div class="text-xl sm:text-2xl font-bold">{{ $bookingStats['total_bookings'] }}</div>
                        <div class="text-xs sm:text-sm opacity-90">Total Bookings</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white shadow-lg">
                        <div class="text-xl sm:text-2xl font-bold">₹{{ number_format($bookingStats['total_revenue']) }}</div>
                        <div class="text-xs sm:text-sm opacity-90">Total Revenue</div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-4 text-white shadow-lg">
                        <div class="text-xl sm:text-2xl font-bold">{{ $bookingStats['b2b_bookings'] }}</div>
                        <div class="text-xs sm:text-sm opacity-90">B2B Bookings</div>
                    </div>
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-4 text-white shadow-lg">
                        <div class="text-xl sm:text-2xl font-bold">₹{{ number_format($bookingStats['avg_booking_value']) }}</div>
                        <div class="text-xs sm:text-sm opacity-90">Avg Booking</div>
                    </div>
                </div>

                <!-- Property Performance -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900 mb-4 text-sm sm:text-base">Property Performance</h3>
                    <div class="space-y-3">
                        @foreach($propertyPerformance as $property)
                            <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 text-sm sm:text-base">{{ $property['name'] }}</h4>
                                        <div class="text-xs sm:text-sm text-gray-600">
                                            {{ $property['total_bookings'] }} bookings • ₹{{ number_format($property['total_revenue']) }} revenue
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($property['avg_occupancy'], 1) }}%</div>
                                        <div class="text-xs text-gray-500">Occupancy</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            @elseif($reportType === 'bookings')
                <!-- Booking Analytics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Status Breakdown -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4 text-sm sm:text-base">Booking Status</h3>
                        <div class="space-y-3">
                            @foreach($bookingStats['status_breakdown'] as $status => $count)
                                <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                    <span class="text-gray-700 capitalize text-sm sm:text-base">{{ $status }}</span>
                                    <span class="font-medium text-sm sm:text-base">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Revenue Breakdown -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4 text-sm sm:text-base">Revenue Sources</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                <span class="text-gray-700 text-sm sm:text-base">Direct Bookings</span>
                                <span class="font-medium text-sm sm:text-base">₹{{ number_format($bookingStats['total_revenue'] - $bookingStats['b2b_revenue']) }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                <span class="text-gray-700 text-sm sm:text-base">B2B Bookings</span>
                                <span class="font-medium text-sm sm:text-base">₹{{ number_format($bookingStats['b2b_revenue']) }}</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between items-center font-semibold bg-white p-2 rounded-lg">
                                <span class="text-gray-900 text-sm sm:text-base">Total Revenue</span>
                                <span class="text-sm sm:text-base">₹{{ number_format($bookingStats['total_revenue']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($reportType === 'commissions')
                <!-- Commission Analytics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Commission Summary -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4 text-sm sm:text-base">Commission Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                <span class="text-gray-700 text-sm sm:text-base">Total Commissions</span>
                                <span class="font-medium text-sm sm:text-base">₹{{ number_format($commissionStats['total_commissions']) }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                <span class="text-green-700 text-sm sm:text-base">Paid</span>
                                <span class="font-medium text-green-600 text-sm sm:text-base">₹{{ number_format($commissionStats['paid_commissions']) }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                <span class="text-orange-700 text-sm sm:text-base">Pending</span>
                                <span class="font-medium text-orange-600 text-sm sm:text-base">₹{{ number_format($commissionStats['pending_commissions']) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Partner Breakdown -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4 text-sm sm:text-base">Top Partners</h3>
                        <div class="space-y-3">
                            @foreach($commissionStats['partner_breakdown']->take(5) as $partner => $data)
                                <div class="bg-white p-3 rounded-lg shadow-sm">
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">{{ $partner }}</div>
                                    <div class="text-xs sm:text-sm text-gray-600">
                                        Total: ₹{{ number_format($data['total']) }} • 
                                        Paid: ₹{{ number_format($data['paid']) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            @elseif($reportType === 'customers')
                <!-- Customer Analytics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Customer Summary -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4 text-sm sm:text-base">Customer Insights</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                <span class="text-gray-700 text-sm sm:text-base">Total Customers</span>
                                <span class="font-medium text-sm sm:text-base">{{ $customerStats['total_customers'] }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                <span class="text-green-700 text-sm sm:text-base">New Customers</span>
                                <span class="font-medium text-sm sm:text-base">{{ $customerStats['new_customers'] }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                <span class="text-blue-700 text-sm sm:text-base">Repeat Customers</span>
                                <span class="font-medium text-sm sm:text-base">{{ $customerStats['repeat_customers'] }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white p-2 rounded-lg">
                                <span class="text-purple-700 text-sm sm:text-base">Avg Loyalty Points</span>
                                <span class="font-medium text-sm sm:text-base">{{ number_format($customerStats['avg_loyalty_points']) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Top Customers -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4 text-sm sm:text-base">Top Customers</h3>
                        <div class="space-y-3">
                            @foreach($customerStats['top_customers'] as $customer)
                                <div class="bg-white p-3 rounded-lg shadow-sm">
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">{{ $customer->name }}</div>
                                    <div class="text-xs sm:text-sm text-gray-600">
                                        {{ $customer->reservations_count }} bookings • 
                                        {{ $customer->loyalty_points }} points
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>