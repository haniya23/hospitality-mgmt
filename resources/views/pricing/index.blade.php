<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pricing - Hospitality Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glassmorphism { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18); }
        .pricing-card { background: white; transition: all 0.3s ease; }
        .pricing-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.sidebar')
    
    <div x-data="pricingManager()" x-init="init()" class="lg:ml-72">
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
                            <i class="fas fa-rupee-sign text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">Pricing</h1>
                            <p class="text-sm opacity-90">Manage room rates & rules</p>
                        </div>
                    </div>
                    <button @click="showPricingModal = true" class="glassmorphism rounded-xl px-4 py-2">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="font-medium">Rule</span>
                    </button>
                </div>

                <!-- Filter Tabs -->
                <div class="flex space-x-2 mb-4">
                    <button @click="activeView = 'rules'" 
                            :class="activeView === 'rules' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Rules
                    </button>
                    <button @click="activeView = 'calendar'" 
                            :class="activeView === 'calendar' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Calendar
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="glassmorphism rounded-2xl p-4">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.totalRules"></div>
                            <div class="text-xs opacity-75">Active Rules</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">₹<span x-text="stats.avgRate"></span></div>
                            <div class="text-xs opacity-75">Avg Rate</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.properties"></div>
                            <div class="text-xs opacity-75">Properties</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="px-4 py-6 pb-32 space-y-4">
            <!-- Property Filter -->
            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <select x-model="selectedProperty" @change="loadPricingRules()" 
                        class="w-full bg-transparent border-none text-gray-800 font-medium focus:ring-0">
                    <option value="">All Properties</option>
                    <template x-for="property in properties" :key="property.id">
                        <option :value="property.id" x-text="property.name"></option>
                    </template>
                </select>
            </div>

            <!-- Rules View -->
            <div x-show="activeView === 'rules'">
                <template x-for="rule in pricingRules" :key="rule.id">
                    <div class="pricing-card rounded-2xl p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-green-500 to-blue-500 flex items-center justify-center text-white font-bold">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800" x-text="rule.name"></h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full font-medium">Active</span>
                                        <span class="text-xs text-gray-500" x-text="rule.rule_type"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-800">₹<span x-text="rule.base_price"></span></div>
                                <div class="text-xs text-gray-500">per night</div>
                            </div>
                        </div>

                        <!-- Rule Details -->
                        <div class="bg-gray-50 rounded-xl p-3 mb-3">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Property:</span>
                                    <span class="font-medium ml-1" x-text="rule.property?.name"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Room:</span>
                                    <span class="font-medium ml-1" x-text="rule.accommodation?.display_name"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Valid From:</span>
                                    <span class="font-medium ml-1" x-text="formatDate(rule.valid_from)"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Valid To:</span>
                                    <span class="font-medium ml-1" x-text="formatDate(rule.valid_to)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-blue-600 transition">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </button>
                            <button class="bg-red-100 text-red-600 py-2 px-4 rounded-xl font-medium text-sm hover:bg-red-200 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Calendar View -->
            <div x-show="activeView === 'calendar'" class="bg-white rounded-2xl p-4 shadow-sm">
                <div class="text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Calendar View</h3>
                    <p class="text-gray-500 text-sm">Coming soon - Visual pricing calendar</p>
                </div>
            </div>

            <!-- Empty State -->
            <template x-if="pricingRules.length === 0 && activeView === 'rules'">
                <div class="text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-green-400 to-blue-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Pricing Rules</h3>
                    <p class="text-gray-500 text-sm mb-4">Create your first pricing rule.</p>
                    <button @click="showPricingModal = true" class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-6 py-3 rounded-xl font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Add Rule
                    </button>
                </div>
            </template>
        </div>

    </div>
    
    @include('partials.bottom-bar')

    <script>
        function pricingManager() {
            return {
                properties: [],
                pricingRules: [],
                selectedProperty: '',
                activeView: 'rules',
                showPricingModal: false,

                get stats() {
                    return {
                        totalRules: this.pricingRules.length,
                        avgRate: this.pricingRules.length > 0 ? Math.round(this.pricingRules.reduce((sum, r) => sum + r.base_price, 0) / this.pricingRules.length) : 0,
                        properties: this.properties.length
                    };
                },

                async init() {
                    await this.loadProperties();
                    await this.loadPricingRules();
                },

                async loadProperties() {
                    // Mock data
                    this.properties = [
                        { id: 1, name: 'Ocean View Resort' },
                        { id: 2, name: 'Mountain Lodge' }
                    ];
                },

                async loadPricingRules() {
                    // Mock data
                    this.pricingRules = [
                        {
                            id: 1,
                            name: 'Weekend Rate',
                            rule_type: 'Weekend',
                            base_price: 5000,
                            valid_from: '2024-01-01',
                            valid_to: '2024-12-31',
                            property: { name: 'Ocean View Resort' },
                            accommodation: { display_name: 'Deluxe Room' }
                        },
                        {
                            id: 2,
                            name: 'Peak Season',
                            rule_type: 'Seasonal',
                            base_price: 7500,
                            valid_from: '2024-12-15',
                            valid_to: '2024-01-15',
                            property: { name: 'Mountain Lodge' },
                            accommodation: { display_name: 'Suite' }
                        }
                    ];
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