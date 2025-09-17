<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Properties - Hospitality Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glassmorphism { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18); }
        .property-card { background: white; transition: all 0.3s ease; }
        .property-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .status-active { background: #d1fae5; color: #059669; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-inactive { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.sidebar')
    
    <div x-data="propertyManager()" x-init="init()" class="lg:ml-72">
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
                            <h1 class="text-xl font-bold">Properties</h1>
                            <p class="text-sm opacity-90">Manage your properties</p>
                        </div>
                    </div>
                    <a href="{{ route('properties.create') }}" class="glassmorphism rounded-xl px-4 py-2">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="font-medium">Add</span>
                    </a>
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
                            <div class="text-xs opacity-75">Total</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.active"></div>
                            <div class="text-xs opacity-75">Active</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.rooms"></div>
                            <div class="text-xs opacity-75">Total Rooms</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="px-4 py-6 pb-32 space-y-4">
            <!-- Properties List -->
            <template x-for="property in filteredProperties" :key="property.id">
                <div class="property-card rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800" x-text="property.name"></h3>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs px-2 py-1 rounded-full font-medium"
                                          :class="'status-' + property.status"
                                          x-text="property.status.charAt(0).toUpperCase() + property.status.slice(1)"></span>
                                    <span class="text-xs text-gray-500" x-text="property.category"></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-800" x-text="property.accommodations_count + ' rooms'"></div>
                            <div class="text-xs text-gray-500">85% occupied</div>
                        </div>
                    </div>

                    <!-- Property Details -->
                    <div class="bg-gray-50 rounded-xl p-3 mb-3">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-map-marker-alt text-gray-500 text-sm"></i>
                            <span class="text-sm text-gray-600" x-text="property.location?.city + ', ' + property.location?.state"></span>
                        </div>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span><i class="fas fa-bed mr-1"></i><span x-text="property.accommodations_count"></span> Rooms</span>
                            <span><i class="fas fa-star mr-1"></i>4.5 Rating</span>
                            <span><i class="fas fa-calendar mr-1"></i>12 Bookings</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <a :href="'/properties/' + property.id + '/edit'" class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-blue-600 transition text-center">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                        <button class="bg-green-100 text-green-600 py-2 px-4 rounded-xl font-medium text-sm hover:bg-green-200 transition">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="bg-red-100 text-red-600 py-2 px-4 rounded-xl font-medium text-sm hover:bg-red-200 transition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="filteredProperties.length === 0">
                <div class="text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-blue-400 to-purple-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Properties Found</h3>
                    <p class="text-gray-500 text-sm mb-4">Start by adding your first property.</p>
                    <a href="{{ route('properties.create') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Add Property
                    </a>
                </div>
            </template>
        </div>

    </div>
    
    @include('partials.bottom-bar')

    <script>
        function propertyManager() {
            return {
                properties: @json($properties ?? []),
                activeFilter: 'all',

                get filteredProperties() {
                    if (this.activeFilter === 'all') return this.properties;
                    return this.properties.filter(p => p.status === this.activeFilter);
                },

                get stats() {
                    return {
                        total: this.properties.length,
                        active: this.properties.filter(p => p.status === 'active').length,
                        rooms: this.properties.reduce((sum, p) => sum + (p.accommodations_count || 0), 0)
                    };
                },

                init() {
                    console.log('Properties loaded:', this.properties.length);
                }
            }
        }
    </script>
</body>
</html>