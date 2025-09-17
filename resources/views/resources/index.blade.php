<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Resources - Hospitality Manager</title>
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
        .glassmorphism {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.sidebar')
    
    <div x-data="resourceManager()" x-init="init()" class="lg:ml-72">
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
                            <i class="fas fa-boxes text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold">Resource Management 📦</h1>
                            <p class="text-sm opacity-90">Manage inventory & supplies</p>
                        </div>
                    </div>
                    <button @click="showResourceModal = true" class="glassmorphism rounded-xl px-4 py-2">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="font-medium">Add</span>
                    </button>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <button @click="activeFilter = 'low'; filterResources()" class="glassmorphism rounded-2xl p-4 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Low Stock</div>
                                <div class="text-sm opacity-75" x-text="stats.lowStock + ' items'"></div>
                            </div>
                        </div>
                    </button>
                    <button @click="activeFilter = 'out'; filterResources()" class="glassmorphism rounded-2xl p-4 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                                <i class="fas fa-times-circle text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Out of Stock</div>
                                <div class="text-sm opacity-75" x-text="stats.outOfStock + ' items'"></div>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Stats Overview -->
                <div class="glassmorphism rounded-2xl p-4">
                    <h3 class="font-semibold mb-3">Inventory Overview</h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.total"></div>
                            <div class="text-xs opacity-75">Total Items</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="stats.categories"></div>
                            <div class="text-xs opacity-75">Categories</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" x-text="'₹' + formatNumber(stats.totalValue)"></div>
                            <div class="text-xs opacity-75">Total Value</div>
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
                    <button @click="activeFilter = 'all'; filterResources()" 
                            :class="activeFilter === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        All
                    </button>
                    <button @click="activeFilter = 'housekeeping'; filterResources()" 
                            :class="activeFilter === 'housekeeping' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Housekeeping
                    </button>
                    <button @click="activeFilter = 'maintenance'; filterResources()" 
                            :class="activeFilter === 'maintenance' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-600'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Maintenance
                    </button>
                    <button @click="activeFilter = 'low'; filterResources()" 
                            :class="activeFilter === 'low' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition">
                        Low Stock
                    </button>
                </div>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" @input="filterResources()" 
                           placeholder="Search resources..." 
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Resource Stats Cards -->
            <div class="grid grid-cols-2 gap-4">
                <div class="card-gradient rounded-2xl p-4 shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                            <i class="fas fa-boxes text-orange-600"></i>
                        </div>
                        <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full font-medium">+12</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-800" x-text="stats.total"></div>
                    <div class="text-sm text-gray-600">Total Resources</div>
                </div>

                <div class="card-gradient-2 rounded-2xl p-4 shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                            <i class="fas fa-rupee-sign text-blue-600"></i>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-medium">+8%</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-800" x-text="'₹' + formatNumber(stats.avgValue)"></div>
                    <div class="text-sm text-gray-600">Avg Item Value</div>
                </div>
            </div>

            <!-- Resources List -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Resources (<span x-text="filteredResources.length"></span>)</h3>
                        <button @click="showResourceModal = true" class="text-blue-600 font-medium text-sm">
                            <i class="fas fa-plus mr-1"></i>Add New
                        </button>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <template x-for="resource in filteredResources" :key="resource.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg"
                                     :style="'background: linear-gradient(135deg, ' + resource.color + ')'">
                                    <i :class="resource.icon"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h4 class="font-semibold text-gray-800" x-text="resource.name"></h4>
                                        <span class="text-xs px-2 py-1 rounded-full font-medium"
                                              :class="resource.stock > resource.minStock ? 'bg-green-100 text-green-600' : 
                                                     resource.stock > 0 ? 'bg-yellow-100 text-yellow-600' : 
                                                     'bg-red-100 text-red-600'"
                                              x-text="resource.stock > resource.minStock ? 'IN STOCK' : 
                                                     resource.stock > 0 ? 'LOW STOCK' : 'OUT OF STOCK'"></span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <div class="flex items-center space-x-4">
                                            <span><i class="fas fa-tag text-gray-400 mr-1"></i><span x-text="resource.category"></span></span>
                                            <span><i class="fas fa-rupee-sign text-gray-400 mr-1"></i>₹<span x-text="resource.unitPrice"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-800" x-text="resource.stock"></div>
                                    <div class="text-xs text-gray-500" x-text="resource.unit + 's'"></div>
                                    <div class="flex space-x-1 mt-2">
                                        <button @click="updateStock(resource.id, 'add')" class="p-2 bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition-colors">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                        <button @click="updateStock(resource.id, 'remove')" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Empty State -->
            <template x-if="filteredResources.length === 0">
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-green-400 to-blue-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Resources Found</h3>
                    <p class="text-gray-500 text-sm mb-4">Start managing your inventory by adding resources.</p>
                    <button @click="showResourceModal = true" class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-6 py-3 rounded-xl font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Add Resource
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Resource Modal -->
        <div x-show="showResourceModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Add New Resource</h3>
                        <button @click="showResourceModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form @submit.prevent="addResource()" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resource Name</label>
                            <input type="text" x-model="newResource.name" required
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select x-model="newResource.category" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="housekeeping">Housekeeping</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="food">Food & Beverage</option>
                                <option value="office">Office Supplies</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                                <input type="number" x-model="newResource.stock" min="0" required
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Min Stock</label>
                                <input type="number" x-model="newResource.minStock" min="0" required
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                                <input type="text" x-model="newResource.unit" placeholder="piece, kg, liter" required
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price (₹)</label>
                                <input type="number" x-model="newResource.unitPrice" min="0" step="0.01" required
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="showResourceModal = false" 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                                Add Resource
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    
    @include('partials.bottom-bar')

    <script>
        function resourceManager() {
            return {
                resources: [
                    {
                        id: 1,
                        name: 'Toilet Paper',
                        category: 'housekeeping',
                        stock: 50,
                        minStock: 20,
                        unit: 'roll',
                        unitPrice: 25,
                        color: '#667eea, #764ba2',
                        icon: 'fas fa-toilet-paper'
                    },
                    {
                        id: 2,
                        name: 'Cleaning Detergent',
                        category: 'housekeeping',
                        stock: 8,
                        minStock: 10,
                        unit: 'bottle',
                        unitPrice: 150,
                        color: '#f093fb, #f5576c',
                        icon: 'fas fa-spray-can'
                    },
                    {
                        id: 3,
                        name: 'Light Bulbs',
                        category: 'maintenance',
                        stock: 0,
                        minStock: 5,
                        unit: 'piece',
                        unitPrice: 80,
                        color: '#4facfe, #00f2fe',
                        icon: 'fas fa-lightbulb'
                    }
                ],
                filteredResources: [],
                activeFilter: 'all',
                searchQuery: '',
                showResourceModal: false,
                newResource: {
                    name: '',
                    category: 'housekeeping',
                    stock: 0,
                    minStock: 5,
                    unit: 'piece',
                    unitPrice: 0
                },

                get stats() {
                    const total = this.resources.length;
                    const categories = [...new Set(this.resources.map(r => r.category))].length;
                    const lowStock = this.resources.filter(r => r.stock <= r.minStock && r.stock > 0).length;
                    const outOfStock = this.resources.filter(r => r.stock === 0).length;
                    const totalValue = this.resources.reduce((sum, r) => sum + (r.stock * r.unitPrice), 0);
                    const avgValue = total > 0 ? Math.round(totalValue / total) : 0;
                    
                    return {
                        total,
                        categories,
                        lowStock,
                        outOfStock,
                        totalValue,
                        avgValue
                    };
                },

                init() {
                    this.filteredResources = this.resources;
                },

                filterResources() {
                    let filtered = this.resources;

                    if (this.searchQuery) {
                        filtered = filtered.filter(r => 
                            r.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            r.category.toLowerCase().includes(this.searchQuery.toLowerCase())
                        );
                    }

                    if (this.activeFilter !== 'all') {
                        if (this.activeFilter === 'low') {
                            filtered = filtered.filter(r => r.stock <= r.minStock && r.stock > 0);
                        } else if (this.activeFilter === 'out') {
                            filtered = filtered.filter(r => r.stock === 0);
                        } else {
                            filtered = filtered.filter(r => r.category === this.activeFilter);
                        }
                    }

                    this.filteredResources = filtered;
                },

                updateStock(resourceId, action) {
                    const resource = this.resources.find(r => r.id === resourceId);
                    if (resource) {
                        if (action === 'add') {
                            resource.stock += 1;
                        } else if (action === 'remove' && resource.stock > 0) {
                            resource.stock -= 1;
                        }
                        this.filterResources();
                    }
                },

                addResource() {
                    if (!this.newResource.name || !this.newResource.unit) {
                        alert('Please fill in required fields');
                        return;
                    }

                    const colors = [
                        '#667eea, #764ba2',
                        '#f093fb, #f5576c',
                        '#4facfe, #00f2fe',
                        '#fa709a, #fee140',
                        '#a8edea, #fed6e3'
                    ];

                    const icons = {
                        housekeeping: 'fas fa-broom',
                        maintenance: 'fas fa-tools',
                        food: 'fas fa-utensils',
                        office: 'fas fa-paperclip'
                    };

                    const newId = Math.max(...this.resources.map(r => r.id)) + 1;
                    const resource = {
                        id: newId,
                        name: this.newResource.name,
                        category: this.newResource.category,
                        stock: parseInt(this.newResource.stock),
                        minStock: parseInt(this.newResource.minStock),
                        unit: this.newResource.unit,
                        unitPrice: parseFloat(this.newResource.unitPrice),
                        color: colors[Math.floor(Math.random() * colors.length)],
                        icon: icons[this.newResource.category] || 'fas fa-box'
                    };

                    this.resources.push(resource);
                    this.filterResources();
                    this.resetForm();
                    this.showResourceModal = false;
                },

                resetForm() {
                    this.newResource = {
                        name: '',
                        category: 'housekeeping',
                        stock: 0,
                        minStock: 5,
                        unit: 'piece',
                        unitPrice: 0
                    };
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('en-IN').format(num);
                }
            }
        }
    </script>
</body>
</html>