<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff Dashboard') - Hospitality Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .soft-header-gradient {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        .soft-glass-card {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .modern-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        /* Mobile Sidebar Styles */
        .mobile-sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .mobile-sidebar.open {
            transform: translateX(0);
        }
        
        /* Bottom Navbar Styles */
        .bottom-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen" x-data="staffApp()">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 lg:hidden z-40" @click="sidebarOpen = false"></div>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar fixed top-0 left-0 h-full w-80 bg-white shadow-2xl z-50 lg:hidden" 
         :class="sidebarOpen ? 'open' : ''"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
        
        <div class="p-6">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Staff Portal</h2>
                        <p class="text-sm text-gray-600">{{ Auth::user()->name }}</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="space-y-2">
                <a href="{{ route('staff.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('staff.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <a href="{{ route('staff.tasks') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('staff.tasks') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-tasks w-5 mr-3"></i>
                    <span class="font-medium">Tasks</span>
                </a>
                
                <a href="{{ route('staff.checklists') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('staff.checklists') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-clipboard-check w-5 mr-3"></i>
                    <span class="font-medium">Checklists</span>
                </a>
                
                <a href="{{ route('staff.notifications') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('staff.notifications') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-bell w-5 mr-3"></i>
                    <span class="font-medium">Notifications</span>
                    <span x-show="stats.unreadNotifications > 0" 
                          class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1" 
                          x-text="stats.unreadNotifications"></span>
                </a>
                
                <a href="{{ route('staff.activity') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('staff.activity') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-history w-5 mr-3"></i>
                    <span class="font-medium">Activity Log</span>
                </a>
            </nav>

            <!-- Quick Stats -->
            <div class="mt-8 p-4 bg-gray-50 rounded-xl">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Today's Summary</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center">
                        <div class="text-lg font-bold text-blue-600" x-text="stats.todaysTasks"></div>
                        <div class="text-xs text-gray-600">Tasks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-red-600" x-text="stats.overdueTasks"></div>
                        <div class="text-xs text-gray-600">Overdue</div>
                    </div>
                </div>
            </div>

            <!-- Logout -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <form method="POST" action="{{ route('staff.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                        <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Staff Header -->
    <header class="soft-header-gradient text-slate-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-white bg-opacity-10"></div>
        <div class="relative px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-white/20 transition-colors">
                        <i class="fas fa-bars text-slate-700"></i>
                    </button>
                    
                    <div class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                        <i class="fas fa-user-tie text-teal-600"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-slate-900">Staff Portal</h1>
                        <p class="text-sm text-slate-700">{{ Auth::user()->name }} - {{ Auth::user()->getActiveStaffAssignments()->first()->role->name ?? 'Staff' }}</p>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('staff.dashboard') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center {{ request()->routeIs('staff.dashboard') ? 'bg-opacity-80' : '' }}">
                        <i class="fas fa-tachometer-alt text-blue-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Dashboard</span>
                    </a>
                    <a href="{{ route('staff.tasks') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center {{ request()->routeIs('staff.tasks') ? 'bg-opacity-80' : '' }}">
                        <i class="fas fa-tasks text-green-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Tasks</span>
                    </a>
                    <a href="{{ route('staff.checklists') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center {{ request()->routeIs('staff.checklists') ? 'bg-opacity-80' : '' }}">
                        <i class="fas fa-clipboard-check text-purple-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Checklists</span>
                    </a>
                    <a href="{{ route('staff.notifications') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center {{ request()->routeIs('staff.notifications') ? 'bg-opacity-80' : '' }}">
                        <i class="fas fa-bell text-orange-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Notifications</span>
                        <span x-show="stats.unreadNotifications > 0" 
                              class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-1" 
                              x-text="stats.unreadNotifications"></span>
                    </a>
                </nav>

                <!-- User Menu -->
                <div class="flex items-center space-x-3" x-data="{ open: false }">
                    <!-- Quick Stats -->
                    <div class="hidden sm:flex items-center space-x-4 text-sm">
                        <div class="text-center">
                            <div class="font-bold text-slate-900" x-text="stats.todaysTasks"></div>
                            <div class="text-xs text-slate-600">Today's Tasks</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-slate-900" x-text="stats.overdueTasks"></div>
                            <div class="text-xs text-slate-600">Overdue</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-slate-900" x-text="stats.unreadNotifications"></div>
                            <div class="text-xs text-slate-600">Notifications</div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button @click="open = !open" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                            <i class="fas fa-user-circle text-slate-600 mr-2"></i>
                            <span class="font-medium text-slate-800">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-slate-600 ml-2"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                            <div class="py-1">
                                <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                    <div class="font-medium">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::user()->mobile_number }}</div>
                                </div>
                                <a href="{{ route('staff.activity') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-history mr-2"></i>Activity Log
                                </a>
                                <form method="POST" action="{{ route('staff.logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-20 lg:pb-6">
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar -->
    <div class="fixed bottom-0 left-0 right-0 z-20 lg:hidden">
        <!-- Floating Navigation Container -->
        <div class="mx-4 mb-4">
            <div class="bottom-navbar rounded-3xl shadow-2xl border border-gray-200/50 px-2 py-3">
                <div class="flex items-center justify-around">
                    <!-- Dashboard -->
                    <div class="group relative">
                        <a href="{{ route('staff.dashboard') }}" class="flex flex-col items-center justify-center p-2 rounded-2xl transition-all duration-200 {{ request()->routeIs('staff.dashboard') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <i class="fas fa-tachometer-alt text-lg mb-1"></i>
                            <span class="text-xs font-medium">Dashboard</span>
                        </a>
                    </div>

                    <!-- Tasks -->
                    <div class="group relative">
                        <a href="{{ route('staff.tasks') }}" class="flex flex-col items-center justify-center p-2 rounded-2xl transition-all duration-200 {{ request()->routeIs('staff.tasks') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <i class="fas fa-tasks text-lg mb-1"></i>
                            <span class="text-xs font-medium">Tasks</span>
                        </a>
                    </div>

                    <!-- More Menu (Centered) -->
                    <div class="group relative">
                        <button @click="showMoreMenu = !showMoreMenu" class="flex flex-col items-center justify-center p-2 rounded-2xl transition-all duration-200 text-gray-600 hover:text-blue-600 hover:bg-blue-50">
                            <i class="fas fa-bars text-lg mb-1"></i>
                            <span class="text-xs font-medium">More</span>
                        </button>

                        <!-- Drop-up Menu -->
                        <div x-show="showMoreMenu" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform translate-y-4"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform translate-y-4"
                             @click.away="showMoreMenu = false"
                             class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-56 bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-200/50 py-2 z-50">
                            
                            <!-- Tasks Section -->
                            <div class="px-3 py-2">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tasks</h4>
                                <div class="space-y-1">
                                    <a href="{{ route('staff.tasks') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.tasks') ? 'bg-blue-50 text-blue-600' : '' }}">
                                        <i class="fas fa-tasks w-4 mr-3"></i>
                                        <span class="text-sm">All Tasks</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Checklists Section -->
                            <div class="px-3 py-2">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Checklists</h4>
                                <div class="space-y-1">
                                    <a href="{{ route('staff.checklists') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.checklists') ? 'bg-blue-50 text-blue-600' : '' }}">
                                        <i class="fas fa-clipboard-check w-4 mr-3"></i>
                                        <span class="text-sm">Cleaning Checklists</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Notifications Section -->
                            <div class="px-3 py-2">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Communication</h4>
                                <div class="space-y-1">
                                    <a href="{{ route('staff.notifications') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.notifications') ? 'bg-blue-50 text-blue-600' : '' }}">
                                        <i class="fas fa-bell w-4 mr-3"></i>
                                        <span class="text-sm">Notifications</span>
                                        <span x-show="stats.unreadNotifications > 0" 
                                              class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1" 
                                              x-text="stats.unreadNotifications"></span>
                                    </a>
                                    <a href="{{ route('staff.activity') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.activity') ? 'bg-blue-50 text-blue-600' : '' }}">
                                        <i class="fas fa-history w-4 mr-3"></i>
                                        <span class="text-sm">Activity Log</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="group relative">
                        <a href="{{ route('staff.notifications') }}" class="flex flex-col items-center justify-center p-2 rounded-2xl transition-all duration-200 {{ request()->routeIs('staff.notifications') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <i class="fas fa-bell text-lg mb-1"></i>
                            <span class="text-xs font-medium">Notifications</span>
                            <span x-show="stats.unreadNotifications > 0" 
                                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" 
                                  x-text="stats.unreadNotifications"></span>
                        </a>
                    </div>

                    <!-- Profile -->
                    <div class="group relative">
                        <button @click="showProfileMenu = !showProfileMenu" class="flex flex-col items-center justify-center p-2 rounded-2xl transition-all duration-200 text-gray-600 hover:text-blue-600 hover:bg-blue-50">
                            <i class="fas fa-user-circle text-lg mb-1"></i>
                            <span class="text-xs font-medium">Profile</span>
                        </button>

                        <!-- Profile Drop-up Menu -->
                        <div x-show="showProfileMenu" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform translate-y-4"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform translate-y-4"
                             @click.away="showProfileMenu = false"
                             class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-200/50 py-2 z-50">
                            
                            <div class="px-3 py-2">
                                <div class="text-sm text-gray-700 border-b pb-2 mb-2">
                                    <div class="font-medium">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::user()->mobile_number }}</div>
                                </div>
                                <a href="{{ route('staff.activity') }}" @click="showProfileMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg">
                                    <i class="fas fa-history w-4 mr-3"></i>
                                    <span class="text-sm">Activity Log</span>
                                </a>
                                <form method="POST" action="{{ route('staff.logout') }}" @click="showProfileMenu = false">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center px-3 py-2 text-red-700 hover:bg-red-50 transition-colors rounded-lg">
                                        <i class="fas fa-sign-out-alt w-4 mr-3"></i>
                                        <span class="text-sm">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="text-center text-sm text-gray-500">
                <i class="fas fa-shield-alt mr-1"></i>
                Staff Portal - Secure Access Only
            </div>
        </div>
    </footer>

    <script>
        // Global staff dashboard data
        function staffApp() {
            return {
                sidebarOpen: false,
                showMoreMenu: false,
                showProfileMenu: false,
                stats: {
                    todaysTasks: {{ Auth::user()->getTodaysTasks()->count() }},
                    overdueTasks: {{ Auth::user()->getOverdueTasks()->count() }},
                    unreadNotifications: {{ Auth::user()->getUnreadNotificationsCount() }}
                }
            }
        }
    </script>
</body>
</html>