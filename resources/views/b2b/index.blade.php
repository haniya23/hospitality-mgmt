<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>B2B Partners - Hospitality Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glassmorphism { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18); }
        .partner-card { background: white; transition: all 0.3s ease; }
        .partner-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .status-active { background: #d1fae5; color: #059669; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-inactive { background: #fee2e2; color: #dc2626; }
        .tier-gold { background: #fef3c7; color: #d97706; }
        .tier-silver { background: #f3f4f6; color: #6b7280; }
        .tier-bronze { background: #fef2f2; color: #dc2626; }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.sidebar')
    
    <div x-data="b2bManager()" x-init="init()" class="lg:ml-72">
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
                            <i class="fas fa-handshake text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">B2B Partners</h1>
                            <p class="text-sm opacity-90">Manage business partnerships</p>
                        </div>
                    </div>
                    <button @click="showPartnerModal = true" class="glassmorphism rounded-xl px-4 py-2">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="font-medium">Add</span>
                    </button>
                </div>

                <!-- Filter Tabs -->
                <div class="flex space-x-2 mb-4">
                    <button @click="activeFilter = 'all'" 
                            :class="activeFilter === 'all' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        All
                    </button>
                    <button @click="activeFilter = 'active'" 
                            :class="activeFilter === 'active' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Active
                    </button>
                    <button @click="activeFilter = 'pending'" 
                            :class="activeFilter === 'pending' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Pending
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="glassmorphism rounded-2xl p-4">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.total"></div>
                            <div class="text-xs opacity-75">Partners</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.active"></div>
                            <div class="text-xs opacity-75">Active</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">₹<span x-text="formatNumber(stats.totalCommission)"></span></div>
                            <div class="text-xs opacity-75">Commission</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="px-4 py-6 pb-32 space-y-4">
            <!-- Partners List -->
            <template x-for="partner in filteredPartners" :key="partner.id">
                <div class="partner-card rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800" x-text="partner.name"></h3>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs px-2 py-1 rounded-full font-medium"
                                          :class="'status-' + partner.status"
                                          x-text="partner.status.charAt(0).toUpperCase() + partner.status.slice(1)"></span>
                                    <span class="text-xs px-2 py-1 rounded-full font-medium"
                                          :class="'tier-' + partner.tier"
                                          x-text="partner.tier.toUpperCase()"></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-800" x-text="partner.bookings"></div>
                            <div class="text-xs text-gray-500">bookings</div>
                        </div>
                    </div>

                    <!-- Partner Details -->
                    <div class="bg-gray-50 rounded-xl p-3 mb-3">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <i class="fas fa-user text-gray-500 mr-2"></i>
                                <span x-text="partner.contactPerson"></span>
                            </div>
                            <div>
                                <i class="fas fa-phone text-gray-500 mr-2"></i>
                                <span x-text="partner.mobile"></span>
                            </div>
                            <div>
                                <i class="fas fa-envelope text-gray-500 mr-2"></i>
                                <span x-text="partner.email"></span>
                            </div>
                            <div>
                                <i class="fas fa-percentage text-gray-500 mr-2"></i>
                                <span x-text="partner.commissionRate + '% commission'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="grid grid-cols-3 gap-4 mb-3">
                        <div class="text-center p-2 bg-blue-50 rounded-lg">
                            <div class="text-lg font-bold text-blue-600">₹<span x-text="formatNumber(partner.totalRevenue)"></span></div>
                            <div class="text-xs text-blue-600">Revenue</div>
                        </div>
                        <div class="text-center p-2 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-600">₹<span x-text="formatNumber(partner.commission)"></span></div>
                            <div class="text-xs text-green-600">Commission</div>
                        </div>
                        <div class="text-center p-2 bg-purple-50 rounded-lg">
                            <div class="text-lg font-bold text-purple-600" x-text="partner.conversionRate + '%'"></div>
                            <div class="text-xs text-purple-600">Conversion</div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="mb-3">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Bookings</h4>
                        <div class="space-y-1">
                            <template x-for="booking in partner.recentBookings.slice(0, 2)" :key="booking.id">
                                <div class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 rounded-lg p-2">
                                    <span x-text="booking.guest + ' - ' + booking.property"></span>
                                    <span x-text="'₹' + formatNumber(booking.amount)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <button class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-blue-600 transition">
                            <i class="fas fa-eye mr-1"></i>
                            View Details
                        </button>
                        <button class="bg-green-100 text-green-600 py-2 px-4 rounded-xl font-medium text-sm hover:bg-green-200 transition">
                            <i class="fas fa-phone"></i>
                        </button>
                        <button class="bg-purple-100 text-purple-600 py-2 px-4 rounded-xl font-medium text-sm hover:bg-purple-200 transition">
                            <i class="fas fa-chart-line"></i>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="filteredPartners.length === 0">
                <div class="text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-indigo-400 to-purple-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Partners Found</h3>
                    <p class="text-gray-500 text-sm mb-4">Start building your partner network.</p>
                    <button @click="showPartnerModal = true" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Add Partner
                    </button>
                </div>
            </template>
        </div>

    </div>
    
    @include('partials.bottom-bar')

    <script>
        function b2bManager() {
            return {
                partners: [
                    {
                        id: 1,
                        name: 'Travel Express',
                        contactPerson: 'Rajesh Kumar',
                        mobile: '+91 98765 43210',
                        email: 'rajesh@travelexpress.com',
                        status: 'active',
                        tier: 'gold',
                        bookings: 45,
                        commissionRate: 15,
                        totalRevenue: 450000,
                        commission: 67500,
                        conversionRate: 78,
                        recentBookings: [
                            { id: 1, guest: 'John Doe', property: 'Ocean View Resort', amount: 12000 },
                            { id: 2, guest: 'Jane Smith', property: 'Mountain Lodge', amount: 8500 }
                        ]
                    },
                    {
                        id: 2,
                        name: 'Holiday Makers',
                        contactPerson: 'Priya Sharma',
                        mobile: '+91 87654 32109',
                        email: 'priya@holidaymakers.com',
                        status: 'active',
                        tier: 'silver',
                        bookings: 28,
                        commissionRate: 12,
                        totalRevenue: 280000,
                        commission: 33600,
                        conversionRate: 65,
                        recentBookings: [
                            { id: 3, guest: 'Mike Johnson', property: 'City Center Hotel', amount: 6500 }
                        ]
                    },
                    {
                        id: 3,
                        name: 'Adventure Tours',
                        contactPerson: 'Amit Patel',
                        mobile: '+91 76543 21098',
                        email: 'amit@adventuretours.com',
                        status: 'pending',
                        tier: 'bronze',
                        bookings: 12,
                        commissionRate: 10,
                        totalRevenue: 120000,
                        commission: 12000,
                        conversionRate: 45,
                        recentBookings: [
                            { id: 4, guest: 'Sarah Wilson', property: 'Mountain Lodge', amount: 9500 }
                        ]
                    }
                ],
                filteredPartners: [],
                activeFilter: 'all',
                showPartnerModal: false,

                get stats() {
                    return {
                        total: this.partners.length,
                        active: this.partners.filter(p => p.status === 'active').length,
                        totalCommission: this.partners.reduce((sum, p) => sum + p.commission, 0)
                    };
                },

                get filteredPartners() {
                    if (this.activeFilter === 'all') return this.partners;
                    return this.partners.filter(p => p.status === this.activeFilter);
                },

                init() {
                    this.filteredPartners = this.partners;
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('en-IN').format(num);
                }
            }
        }
    </script>
</body>
</html>