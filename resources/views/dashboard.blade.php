<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Hospitality Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-gradient {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }
        .card-gradient-2 {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        .card-gradient-3 {
            background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%);
        }
        .card-gradient-4 {
            background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .property-card {
            transition: all 0.3s ease;
        }
        .property-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.sidebar')
    
    <div x-data="dashboardData()" x-init="init()" class="lg:ml-72">
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
                            <i class="fas fa-building text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold">Hi, Manager 👋</h1>
                            <p class="text-sm opacity-90">Welcome back to your dashboard</p>
                        </div>
                    </div>
                    <div class="relative">
                        <button class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-bell text-white"></i>
                        </button>
                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-white" x-text="notifications"></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <button @click="navigateToBookings()" class="glassmorphism rounded-2xl p-4 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                                <i class="fas fa-calendar-plus text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Bookings</div>
                                <div class="text-sm opacity-75">Manage reservations</div>
                            </div>
                        </div>
                    </button>
                    <button @click="navigateToProperties()" class="glassmorphism rounded-2xl p-4 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                                <i class="fas fa-home text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Properties</div>
                                <div class="text-sm opacity-75">View & manage</div>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Stats Overview -->
                <div class="glassmorphism rounded-2xl p-4">
                    <h3 class="font-semibold mb-3">Today's Overview</h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold" x-text="todayStats.checkIns"></div>
                            <div class="text-xs opacity-75">Check-ins</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="todayStats.checkOuts"></div>
                            <div class="text-xs opacity-75">Check-outs</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="todayStats.newBookings"></div>
                            <div class="text-xs opacity-75">New bookings</div>
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
                    <div class="text-2xl font-bold text-gray-800" x-text="'₹' + formatNumber(revenue.today)"></div>
                    <div class="text-sm text-gray-600">Today's Revenue</div>
                </div>

                <div class="card-gradient-2 rounded-2xl p-4 shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-medium">+8%</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-800" x-text="'₹' + formatNumber(revenue.month)"></div>
                    <div class="text-sm text-gray-600">This Month</div>
                </div>
            </div>

            <!-- Properties Section -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Your Properties</h3>
                        <button @click="navigateToProperties()" class="text-blue-600 font-medium text-sm">View all</button>
                    </div>
                </div>
                
                <div class="p-4 space-y-4">
                    <template x-for="property in properties.slice(0, 3)" :key="property.id">
                        <div class="property-card bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                                     :style="'background: linear-gradient(135deg, ' + property.color + ')'">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800" x-text="property.name"></h4>
                                    <p class="text-sm text-gray-500" x-text="property.category"></p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="text-xs px-2 py-1 rounded-full font-medium"
                                              :class="property.status === 'active' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'"
                                              x-text="property.status.charAt(0).toUpperCase() + property.status.slice(1)"></span>
                                        <span class="text-xs text-gray-500" x-text="property.rooms + ' rooms'"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-800" x-text="property.occupancy + '%'"></div>
                                    <div class="text-xs text-gray-500">Occupancy</div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="properties.length === 0">
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl flex items-center justify-center text-white text-2xl">
                                <i class="fas fa-plus"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Create Your First Property</h4>
                            <p class="text-gray-500 text-sm mb-4">Start managing your hospitality business by adding a property.</p>
                            <button @click="navigateToCreateProperty()" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl font-medium">
                                <i class="fas fa-plus mr-2"></i>
                                Add Property
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Recent Activity</h3>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <template x-for="activity in recentActivity" :key="activity.id">
                        <div class="p-4 flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                 :class="activity.type === 'booking' ? 'bg-blue-100 text-blue-600' : 
                                        activity.type === 'checkin' ? 'bg-green-100 text-green-600' :
                                        'bg-orange-100 text-orange-600'">
                                <i :class="activity.type === 'booking' ? 'fas fa-calendar-plus' : 
                                          activity.type === 'checkin' ? 'fas fa-sign-in-alt' :
                                          'fas fa-sign-out-alt'"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800" x-text="activity.message"></p>
                                <p class="text-xs text-gray-500" x-text="activity.time"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 gap-4">
                <div class="card-gradient-3 rounded-2xl p-4 shadow-lg">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center mb-3">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800" x-text="stats.totalGuests"></div>
                    <div class="text-sm text-gray-600">Total Guests</div>
                </div>

                <div class="card-gradient-4 rounded-2xl p-4 shadow-lg">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center mb-3">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800" x-text="stats.avgRating"></div>
                    <div class="text-sm text-gray-600">Avg Rating</div>
                </div>
            </div>
        </div>

    </div>
    
    @include('partials.bottom-bar')

    <script>
        function dashboardData() {
            return {
                notifications: 3,
                todayStats: {
                    checkIns: 12,
                    checkOuts: 8,
                    newBookings: 5
                },
                revenue: {
                    today: 45000,
                    month: 890000
                },
                stats: {
                    totalGuests: 1240,
                    avgRating: 4.8
                },
                properties: [
                    {
                        id: 1,
                        name: "Ocean View Resort",
                        category: "Resort",
                        status: "active",
                        rooms: 24,
                        occupancy: 85,
                        color: "#667eea, #764ba2"
                    },
                    {
                        id: 2,
                        name: "Mountain Lodge",
                        category: "Lodge",
                        status: "active",
                        rooms: 12,
                        occupancy: 92,
                        color: "#f093fb, #f5576c"
                    },
                    {
                        id: 3,
                        name: "City Center Hotel",
                        category: "Hotel",
                        status: "pending",
                        rooms: 36,
                        occupancy: 67,
                        color: "#4facfe, #00f2fe"
                    }
                ],
                recentActivity: [
                    {
                        id: 1,
                        type: 'booking',
                        message: 'New booking from John Doe',
                        time: '2 minutes ago'
                    },
                    {
                        id: 2,
                        type: 'checkin',
                        message: 'Guest checked in to Room 205',
                        time: '15 minutes ago'
                    },
                    {
                        id: 3,
                        type: 'checkout',
                        message: 'Guest checked out from Room 102',
                        time: '1 hour ago'
                    },
                    {
                        id: 4,
                        type: 'booking',
                        message: 'Booking confirmed for Amanda Smith',
                        time: '2 hours ago'
                    }
                ],

                init() {
                    // Initialize dashboard data
                    console.log('Dashboard initialized');
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('en-IN').format(num);
                },

                navigateToBookings() {
                    // Replace with actual navigation
                    console.log('Navigate to bookings');
                },

                navigateToProperties() {
                    // Replace with actual navigation
                    console.log('Navigate to properties');
                },

                navigateToCreateProperty() {
                    // Replace with actual navigation
                    console.log('Navigate to create property');
                }
            }
        }
    </script>
</body>
</html>