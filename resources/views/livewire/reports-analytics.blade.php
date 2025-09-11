<div class="space-y-6">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Reports & Analytics</h2>
            <p class="text-gray-600">Business insights and performance metrics</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <select wire:model.live="dateRange" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 3 months</option>
                <option value="365">Last year</option>
            </select>
            
            <select wire:model.live="property_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="all">All Properties</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                @endforeach
            </select>
            
            <button wire:click="exportReport('csv')" 
                    class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200">
                Export CSV
            </button>
        </div>
    </div>

    <!-- Report Type Tabs -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            <button wire:click="switchReport('overview')" 
                    class="flex-shrink-0 px-6 py-4 text-center font-medium transition-colors
                    {{ $reportType === 'overview' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-500' : 'text-gray-600 hover:text-gray-900' }}">
                Overview
            </button>
            <button wire:click="switchReport('bookings')" 
                    class="flex-shrink-0 px-6 py-4 text-center font-medium transition-colors
                    {{ $reportType === 'bookings' ? 'bg-emerald-50 text-emerald-600 border-b-2 border-emerald-500' : 'text-gray-600 hover:text-gray-900' }}">
                Bookings
            </button>
            <button wire:click="switchReport('commissions')" 
                    class="flex-shrink-0 px-6 py-4 text-center font-medium transition-colors
                    {{ $reportType === 'commissions' ? 'bg-purple-50 text-purple-600 border-b-2 border-purple-500' : 'text-gray-600 hover:text-gray-900' }}">
                Commissions
            </button>
            <button wire:click="switchReport('customers')" 
                    class="flex-shrink-0 px-6 py-4 text-center font-medium transition-colors
                    {{ $reportType === 'customers' ? 'bg-orange-50 text-orange-600 border-b-2 border-orange-500' : 'text-gray-600 hover:text-gray-900' }}">
                Customers
            </button>
        </div>

        <!-- Report Content -->
        <div class="p-6">
            @if($reportType === 'overview')
                <!-- Overview Dashboard -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-4 text-white">
                        <div class="text-2xl font-bold">{{ $bookingStats['total_bookings'] }}</div>
                        <div class="text-sm opacity-90">Total Bookings</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white">
                        <div class="text-2xl font-bold">₹{{ number_format($bookingStats['total_revenue']) }}</div>
                        <div class="text-sm opacity-90">Total Revenue</div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-4 text-white">
                        <div class="text-2xl font-bold">{{ $bookingStats['b2b_bookings'] }}</div>
                        <div class="text-sm opacity-90">B2B Bookings</div>
                    </div>
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-4 text-white">
                        <div class="text-2xl font-bold">₹{{ number_format($bookingStats['avg_booking_value']) }}</div>
                        <div class="text-sm opacity-90">Avg Booking</div>
                    </div>
                </div>

                <!-- Property Performance -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900 mb-4">Property Performance</h3>
                    <div class="space-y-3">
                        @foreach($propertyPerformance as $property)
                            <div class="bg-white p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $property['name'] }}</h4>
                                        <div class="text-sm text-gray-600">
                                            {{ $property['total_bookings'] }} bookings • ₹{{ number_format($property['total_revenue']) }} revenue
                                        </div>
                                    </div>
                                    <div class="text-right">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status Breakdown -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Booking Status</h3>
                        <div class="space-y-3">
                            @foreach($bookingStats['status_breakdown'] as $status => $count)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 capitalize">{{ $status }}</span>
                                    <span class="font-medium">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Revenue Breakdown -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Revenue Sources</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Direct Bookings</span>
                                <span class="font-medium">₹{{ number_format($bookingStats['total_revenue'] - $bookingStats['b2b_revenue']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">B2B Bookings</span>
                                <span class="font-medium">₹{{ number_format($bookingStats['b2b_revenue']) }}</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between items-center font-semibold">
                                <span class="text-gray-900">Total Revenue</span>
                                <span>₹{{ number_format($bookingStats['total_revenue']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($reportType === 'commissions')
                <!-- Commission Analytics -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Commission Summary -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Commission Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Total Commissions</span>
                                <span class="font-medium">₹{{ number_format($commissionStats['total_commissions']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-700">Paid</span>
                                <span class="font-medium text-green-600">₹{{ number_format($commissionStats['paid_commissions']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-orange-700">Pending</span>
                                <span class="font-medium text-orange-600">₹{{ number_format($commissionStats['pending_commissions']) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Partner Breakdown -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Top Partners</h3>
                        <div class="space-y-3">
                            @foreach($commissionStats['partner_breakdown']->take(5) as $partner => $data)
                                <div class="bg-white p-3 rounded-lg">
                                    <div class="font-medium text-gray-900">{{ $partner }}</div>
                                    <div class="text-sm text-gray-600">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Summary -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Customer Insights</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Total Customers</span>
                                <span class="font-medium">{{ $customerStats['total_customers'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-700">New Customers</span>
                                <span class="font-medium">{{ $customerStats['new_customers'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-blue-700">Repeat Customers</span>
                                <span class="font-medium">{{ $customerStats['repeat_customers'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-purple-700">Avg Loyalty Points</span>
                                <span class="font-medium">{{ number_format($customerStats['avg_loyalty_points']) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Top Customers -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Top Customers</h3>
                        <div class="space-y-3">
                            @foreach($customerStats['top_customers'] as $customer)
                                <div class="bg-white p-3 rounded-lg">
                                    <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-sm text-gray-600">
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