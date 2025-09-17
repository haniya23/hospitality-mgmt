<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reports & Analytics - Hospitality Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-gradient { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
        .card-gradient-2 { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .card-gradient-3 { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); }
        .card-gradient-4 { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); }
        .glassmorphism { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18); }
        .report-card { background: white; transition: all 0.3s ease; }
        .report-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.sidebar')
    
    <div x-data="reportsManager()" x-init="init()" class="lg:ml-72">
        <!-- Header -->
        <header class="gradient-bg text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
            <div class="relative px-4 py-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <button @click="$dispatch('toggle-sidebar')" class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition-all lg:hidden">
                            <i class="fas fa-bars text-white"></i>
                        </button>
                        <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-chart-bar text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">Analytics</h1>
                            <p class="text-sm opacity-90">Business insights & reports</p>
                        </div>
                    </div>
                    <button @click="generateReport()" class="glassmorphism rounded-xl px-4 py-2">
                        <i class="fas fa-download mr-2"></i>
                        <span class="font-medium">Export</span>
                    </button>
                </div>

                <!-- Period Selector -->
                <div class="flex space-x-2 mb-4">
                    <button @click="selectedPeriod = 'today'" 
                            :class="selectedPeriod === 'today' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Today
                    </button>
                    <button @click="selectedPeriod = 'week'" 
                            :class="selectedPeriod === 'week' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Week
                    </button>
                    <button @click="selectedPeriod = 'month'" 
                            :class="selectedPeriod === 'month' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Month
                    </button>
                </div>

                <!-- Key Metrics -->
                <div class="glassmorphism rounded-2xl p-4">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold">₹<span x-text="formatNumber(metrics.revenue)"></span></div>
                            <div class="text-xs opacity-75">Revenue</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="metrics.bookings"></div>
                            <div class="text-xs opacity-75">Bookings</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="metrics.occupancy + '%'"></div>
                            <div class="text-xs opacity-75">Occupancy</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="px-4 py-6 pb-32 space-y-6">
            <!-- Revenue Cards -->
            <div class="grid grid-cols-2 gap-4">
                <div class="card-gradient rounded-2xl p-4 shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                            <i class="fas fa-rupee-sign text-orange-600"></i>
                        </div>
                        <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full font-medium">+12%</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">₹<span x-text="formatNumber(analytics.totalRevenue)"></span></div>
                    <div class="text-sm text-gray-600">Total Revenue</div>
                </div>

                <div class="card-gradient-2 rounded-2xl p-4 shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-medium">+8%</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">₹<span x-text="formatNumber(analytics.avgBookingValue)"></span></div>
                    <div class="text-sm text-gray-600">Avg Booking</div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="report-card rounded-2xl p-4 shadow-sm">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Performance Overview</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                                <i class="fas fa-bed text-green-600 text-sm"></i>
                            </div>
                            <span class="text-gray-700">Room Occupancy</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-24 h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-green-500 rounded-full" :style="'width: ' + analytics.occupancyRate + '%'"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-800" x-text="analytics.occupancyRate + '%'"></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-star text-blue-600 text-sm"></i>
                            </div>
                            <span class="text-gray-700">Guest Satisfaction</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-24 h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-blue-500 rounded-full" :style="'width: ' + (analytics.guestRating * 20) + '%'"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-800" x-text="analytics.guestRating + '/5'"></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-redo text-purple-600 text-sm"></i>
                            </div>
                            <span class="text-gray-700">Repeat Guests</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-24 h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-purple-500 rounded-full" :style="'width: ' + analytics.repeatGuestRate + '%'"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-800" x-text="analytics.repeatGuestRate + '%'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Properties -->
            <div class="report-card rounded-2xl p-4 shadow-sm">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Top Performing Properties</h3>
                <div class="space-y-3">
                    <template x-for="property in topProperties" :key="property.id">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                    <i class="fas fa-building text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800" x-text="property.name"></h4>
                                    <p class="text-sm text-gray-500" x-text="property.bookings + ' bookings'"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-800">₹<span x-text="formatNumber(property.revenue)"></span></div>
                                <div class="text-xs text-green-600" x-text="'+' + property.growth + '%'"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div class="card-gradient-3 rounded-2xl p-4 shadow-lg">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center mb-3">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800" x-text="analytics.totalGuests"></div>
                    <div class="text-sm text-gray-600">Total Guests</div>
                </div>

                <div class="card-gradient-4 rounded-2xl p-4 shadow-lg">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center mb-3">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800" x-text="analytics.completedBookings"></div>
                    <div class="text-sm text-gray-600">Completed</div>
                </div>
            </div>
        </div>

    </div>
    
    @include('partials.bottom-bar')

    <script>
        function reportsManager() {
            return {
                selectedPeriod: 'month',
                metrics: {
                    revenue: 125000,
                    bookings: 45,
                    occupancy: 78
                },
                analytics: {
                    totalRevenue: 890000,
                    avgBookingValue: 4500,
                    occupancyRate: 78,
                    guestRating: 4.6,
                    repeatGuestRate: 35,
                    totalGuests: 1240,
                    completedBookings: 156
                },
                topProperties: [
                    { id: 1, name: 'Ocean View Resort', bookings: 28, revenue: 420000, growth: 15 },
                    { id: 2, name: 'Mountain Lodge', bookings: 22, revenue: 330000, growth: 12 },
                    { id: 3, name: 'City Center Hotel', bookings: 18, revenue: 270000, growth: 8 }
                ],

                init() {
                    console.log('Analytics initialized');
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('en-IN').format(num);
                },

                generateReport() {
                    console.log('Generating report for period:', this.selectedPeriod);
                    // Add report generation logic
                }
            }
        }
    </script>
</body>
</html>