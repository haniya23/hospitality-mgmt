<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customers - Hospitality Manager</title>
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
        .glassmorphism {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .customer-card {
            background: white;
            transition: all 0.3s ease;
        }
        .customer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .tier-vip { background: #fef3c7; color: #d97706; }
        .tier-premium { background: #e0e7ff; color: #6366f1; }
        .tier-regular { background: #f3f4f6; color: #6b7280; }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.sidebar')
    
    <div x-data="customerManager()" x-init="init()" class="lg:ml-72">
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
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold">Customer Management 👥</h1>
                            <p class="text-sm opacity-90">Manage your guest relationships</p>
                        </div>
                    </div>
                    <button @click="showCustomerModal = true" class="glassmorphism rounded-xl px-4 py-2">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="font-medium">Add</span>
                    </button>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <button @click="activeFilter = 'vip'; filterCustomers()" class="glassmorphism rounded-2xl p-4 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                                <i class="fas fa-crown text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">VIP Customers</div>
                                <div class="text-sm opacity-75" x-text="stats.vip + ' customers'"></div>
                            </div>
                        </div>
                    </button>
                    <button @click="activeFilter = 'recent'; filterCustomers()" class="glassmorphism rounded-2xl p-4 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">New Customers</div>
                                <div class="text-sm opacity-75" x-text="stats.newThisMonth + ' this month'"></div>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Stats Overview -->
                <div class="glassmorphism rounded-2xl p-4">
                    <h3 class="font-semibold mb-3">Customer Overview</h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.total"></div>
                            <div class="text-xs opacity-75">Total</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.active"></div>
                            <div class="text-xs opacity-75">Active</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="'₹' + formatNumber(stats.totalRevenue)"></div>
                            <div class="text-xs opacity-75">Total Revenue</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="px-4 py-6 pb-32 space-y-6">
            <!-- Filter & Search -->
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <div class="flex flex-wrap gap-2 mb-4">
                    <button @click="activeFilter = 'all'; filterCustomers()" 
                            :class="activeFilter === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        All
                    </button>
                    <button @click="activeFilter = 'vip'; filterCustomers()" 
                            :class="activeFilter === 'vip' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-600'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        VIP
                    </button>
                    <button @click="activeFilter = 'premium'; filterCustomers()" 
                            :class="activeFilter === 'premium' ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-600'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Premium
                    </button>
                    <button @click="activeFilter = 'recent'; filterCustomers()" 
                            :class="activeFilter === 'recent' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Recent
                    </button>
                </div>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" @input="filterCustomers()" 
                           placeholder="Search customers by name or mobile..." 
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Customer Stats Cards -->
            <div class="grid grid-cols-2 gap-4">
                <div class="card-gradient rounded-2xl p-4 shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                            <i class="fas fa-users text-orange-600"></i>
                        </div>
                        <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full font-medium">+5</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-800" x-text="stats.total"></div>
                    <div class="text-sm text-gray-600">Total Customers</div>
                </div>

                <div class="card-gradient-2 rounded-2xl p-4 shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                            <i class="fas fa-rupee-sign text-blue-600"></i>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-medium">+12%</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-800" x-text="'₹' + formatNumber(stats.avgSpending)"></div>
                    <div class="text-sm text-gray-600">Avg Spending</div>
                </div>
            </div>

            <!-- Customers List -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Customers (<span x-text="filteredCustomers.length"></span>)</h3>
                        <button @click="showCustomerModal = true" class="text-blue-600 font-medium text-sm">
                            <i class="fas fa-plus mr-1"></i>Add New
                        </button>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <template x-for="customer in filteredCustomers" :key="customer.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg"
                                     :style="'background: linear-gradient(135deg, ' + customer.color + ')'">
                                    <span x-text="customer.name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h4 class="font-semibold text-gray-800" x-text="customer.name"></h4>
                                        <span class="text-xs px-2 py-1 rounded-full font-medium"
                                              :class="customer.tier === 'vip' ? 'bg-yellow-100 text-yellow-600' : 
                                                     customer.tier === 'premium' ? 'bg-purple-100 text-purple-600' : 
                                                     'bg-gray-100 text-gray-600'"
                                              x-text="customer.tier.toUpperCase()"></span>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div class="flex items-center space-x-4">
                                            <span><i class="fas fa-phone text-gray-400 mr-1"></i><span x-text="customer.mobile"></span></span>
                                            <span x-show="customer.email"><i class="fas fa-envelope text-gray-400 mr-1"></i><span x-text="customer.email"></span></span>
                                        </div>
                                        <div class="flex items-center space-x-4 text-xs">
                                            <span><i class="fas fa-calendar text-gray-400 mr-1"></i>Last visit: <span x-text="formatDate(customer.lastVisit)"></span></span>
                                            <span><i class="fas fa-star text-yellow-400 mr-1"></i><span x-text="customer.rating"></span>/5</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-800">₹<span x-text="formatNumber(customer.totalSpent)"></span></div>
                                    <div class="text-xs text-gray-500" x-text="customer.totalBookings + ' bookings'"></div>
                                    <div class="flex space-x-1 mt-2">
                                        <button class="p-2 bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition-colors">
                                            <i class="fas fa-phone text-xs"></i>
                                        </button>
                                        <button class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors">
                                            <i class="fas fa-calendar-plus text-xs"></i>
                                        </button>
                                        <button class="p-2 bg-purple-100 text-purple-600 rounded-full hover:bg-purple-200 transition-colors">
                                            <i class="fas fa-envelope text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Empty State -->
            <template x-if="filteredCustomers.length === 0">
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-pink-400 to-purple-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Customers Found</h3>
                    <p class="text-gray-500 text-sm mb-4">Start building your customer base by adding your first customer.</p>
                    <button @click="showCustomerModal = true" class="bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Customer
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Customer Modal -->
        <div x-show="showCustomerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Add New Customer</h3>
                        <button @click="showCustomerModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form @submit.prevent="addCustomer()" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" x-model="newCustomer.name" required
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                            <input type="tel" x-model="newCustomer.mobile" required
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                            <input type="email" x-model="newCustomer.email"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer Tier</label>
                            <select x-model="newCustomer.tier" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="regular">Regular</option>
                                <option value="premium">Premium</option>
                                <option value="vip">VIP</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="showCustomerModal = false" 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                                Add Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    
    @include('partials.bottom-bar')

    <script>
        function customerManager() {
            return {
                customers: [
                    {
                        id: 1,
                        name: 'John Doe',
                        mobile: '+91 98765 43210',
                        email: 'john@example.com',
                        tier: 'vip',
                        totalBookings: 12,
                        totalSpent: 145000,
                        lastVisit: '2024-01-15',
                        rating: 4.8,
                        color: '#667eea, #764ba2',
                        recentBookings: [
                            { id: 1, property: 'Ocean View Resort', date: '2024-01-15' },
                            { id: 2, property: 'Mountain Lodge', date: '2023-12-20' }
                        ]
                    },
                    {
                        id: 2,
                        name: 'Jane Smith',
                        mobile: '+91 87654 32109',
                        email: 'jane@example.com',
                        tier: 'premium',
                        totalBookings: 8,
                        totalSpent: 95000,
                        lastVisit: '2024-01-10',
                        rating: 4.6,
                        color: '#f093fb, #f5576c',
                        recentBookings: [
                            { id: 3, property: 'City Center Hotel', date: '2024-01-10' }
                        ]
                    },
                    {
                        id: 3,
                        name: 'Mike Johnson',
                        mobile: '+91 76543 21098',
                        email: null,
                        tier: 'regular',
                        totalBookings: 3,
                        totalSpent: 25000,
                        lastVisit: '2023-12-05',
                        rating: 4.2,
                        color: '#4facfe, #00f2fe',
                        recentBookings: [
                            { id: 4, property: 'Mountain Lodge', date: '2023-12-05' }
                        ]
                    },
                    {
                        id: 4,
                        name: 'Sarah Wilson',
                        mobile: '+91 65432 10987',
                        email: 'sarah@example.com',
                        tier: 'vip',
                        totalBookings: 15,
                        totalSpent: 180000,
                        lastVisit: '2024-01-20',
                        rating: 4.9,
                        color: '#fa709a, #fee140',
                        recentBookings: [
                            { id: 5, property: 'Beach Resort', date: '2024-01-20' }
                        ]
                    },
                    {
                        id: 5,
                        name: 'David Brown',
                        mobile: '+91 54321 09876',
                        email: 'david@example.com',
                        tier: 'premium',
                        totalBookings: 6,
                        totalSpent: 75000,
                        lastVisit: '2024-01-08',
                        rating: 4.5,
                        color: '#a8edea, #fed6e3',
                        recentBookings: [
                            { id: 6, property: 'Mountain Lodge', date: '2024-01-08' }
                        ]
                    }
                ],
                filteredCustomers: [],
                activeFilter: 'all',
                searchQuery: '',
                showCustomerModal: false,
                newCustomer: {
                    name: '',
                    mobile: '',
                    email: '',
                    tier: 'regular'
                },

                get stats() {
                    const total = this.customers.length;
                    const vip = this.customers.filter(c => c.tier === 'vip').length;
                    const premium = this.customers.filter(c => c.tier === 'premium').length;
                    const active = this.customers.filter(c => new Date(c.lastVisit) > new Date(Date.now() - 30*24*60*60*1000)).length;
                    const newThisMonth = this.customers.filter(c => new Date(c.lastVisit) > new Date('2024-01-01')).length;
                    const totalRevenue = this.customers.reduce((sum, c) => sum + c.totalSpent, 0);
                    const avgSpending = total > 0 ? Math.round(totalRevenue / total) : 0;
                    
                    return {
                        total,
                        vip,
                        premium,
                        active,
                        newThisMonth,
                        totalRevenue,
                        avgSpending
                    };
                },

                init() {
                    this.filteredCustomers = this.customers;
                },

                filterCustomers() {
                    let filtered = this.customers;

                    // Apply search filter
                    if (this.searchQuery) {
                        filtered = filtered.filter(c => 
                            c.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            c.mobile.includes(this.searchQuery) ||
                            (c.email && c.email.toLowerCase().includes(this.searchQuery.toLowerCase()))
                        );
                    }

                    // Apply category filter
                    if (this.activeFilter === 'vip') {
                        filtered = filtered.filter(c => c.tier === 'vip');
                    } else if (this.activeFilter === 'premium') {
                        filtered = filtered.filter(c => c.tier === 'premium');
                    } else if (this.activeFilter === 'recent') {
                        filtered = filtered.filter(c => new Date(c.lastVisit) > new Date('2024-01-01'));
                    }

                    this.filteredCustomers = filtered;
                },

                addCustomer() {
                    if (!this.newCustomer.name || !this.newCustomer.mobile) {
                        alert('Please fill in required fields');
                        return;
                    }

                    const colors = [
                        '#667eea, #764ba2',
                        '#f093fb, #f5576c',
                        '#4facfe, #00f2fe',
                        '#fa709a, #fee140',
                        '#a8edea, #fed6e3',
                        '#d299c2, #fef9d7'
                    ];

                    const newId = Math.max(...this.customers.map(c => c.id)) + 1;
                    const customer = {
                        id: newId,
                        name: this.newCustomer.name,
                        mobile: this.newCustomer.mobile,
                        email: this.newCustomer.email || null,
                        tier: this.newCustomer.tier,
                        totalBookings: 0,
                        totalSpent: 0,
                        lastVisit: new Date().toISOString().split('T')[0],
                        rating: 5.0,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        recentBookings: []
                    };

                    this.customers.push(customer);
                    this.filterCustomers();
                    this.resetForm();
                    this.showCustomerModal = false;
                },

                resetForm() {
                    this.newCustomer = {
                        name: '',
                        mobile: '',
                        email: '',
                        tier: 'regular'
                    };
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('en-IN').format(num);
                },

                formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString('en-GB', { 
                        day: 'numeric', 
                        month: 'short', 
                        year: 'numeric' 
                    });
                }
            }
        }
    </script>
</body>
</html>