<!DOCTYPE html>
<html lang="en" class="bg-gradient-to-br from-blue-50 to-purple-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hospitality Manager')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="{{ asset('css/globe-loader.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        [x-cloak] { display: none !important; }
        body { background: linear-gradient(135deg, #dbeafe 0%, #f3e8ff 100%); }
        @keyframes truck-motion {
            0% { transform: translateY(0px); }
            50% { transform: translateY(3px); }
            100% { transform: translateY(0px); }
        }
        @keyframes road-animation {
            0% { transform: translateX(0px); }
            100% { transform: translateX(-350px); }
        }
        .truck-body { animation: truck-motion 1s linear infinite; }
        .road-line { animation: road-animation 1.4s linear infinite; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50" x-data="{ sidebarOpen: false }">
    <!-- Top Bar -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-white bg-opacity-80 backdrop-blur-md shadow-lg">
        <div class="flex items-center justify-between px-4 py-3">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-gray-800 transition-all duration-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center space-x-2">
                @if(auth()->user()->is_admin)
                    <span class="bg-red-100 text-red-800 px-2 py-1 text-xs rounded-full">ADMIN</span>
                @endif
                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div x-show="sidebarOpen" 
         x-cloak
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black bg-opacity-50 z-40">
    </div>

    <!-- Sidebar -->
    <div x-show="sidebarOpen" 
         x-cloak
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="-translate-x-full" 
         x-transition:enter-end="translate-x-0"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="-translate-x-full"
         class="fixed left-0 top-0 h-full w-72 bg-white shadow-lg z-50 transform">
        
        <div class="p-6">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-bold text-gray-800">Menu</h2>
                <button @click="sidebarOpen = false" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-100 text-gray-700 transition-colors {{ request()->routeIs('dashboard') ? 'bg-purple-100 text-purple-700' : '' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('properties.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-100 text-gray-700 transition-colors {{ request()->routeIs('properties.*') ? 'bg-purple-100 text-purple-700' : '' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Properties
                    </a>
                </li>
                <li>
                    <a href="{{ route('bookings.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-100 text-gray-700 transition-colors {{ request()->routeIs('bookings.*') ? 'bg-emerald-100 text-emerald-700' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Bookings
                    </a>
                </li>
                <li>
                    <a href="{{ route('customers.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-teal-100 text-gray-700 transition-colors {{ request()->routeIs('customers.*') ? 'bg-teal-100 text-teal-700' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Customers
                    </a>
                </li>
                <li>
                    <a href="{{ route('b2b.dashboard') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-100 text-gray-700 transition-colors {{ request()->routeIs('b2b.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        B2B Partners
                    </a>
                </li>
                <li>
                    <a href="{{ route('resources.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-orange-100 text-gray-700 transition-colors {{ request()->routeIs('resources.*') ? 'bg-orange-100 text-orange-700' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Resources
                    </a>
                </li>
                <li>
                    <a href="{{ route('pricing.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-100 text-gray-700 transition-colors {{ request()->routeIs('pricing.*') ? 'bg-purple-100 text-purple-700' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pricing
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.analytics') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-indigo-100 text-gray-700 transition-colors {{ request()->routeIs('reports.*') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Reports
                    </a>
                </li>
                @if(auth()->user()->is_admin)
                <li>
                    <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-red-100 text-gray-700 transition-colors {{ request()->routeIs('admin.*') ? 'bg-red-100 text-red-700' : '' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Admin Panel
                    </a>
                </li>
                @endif
                <li class="pt-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 text-gray-700 transition-colors w-full text-left">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-16 pb-20 px-4">
        @yield('content')
    </div>

    <!-- Bottom Navigation -->
    <div class="fixed bottom-4 left-4 right-4 z-40">
        <div class="flex items-center justify-between bg-white bg-opacity-80 backdrop-blur-md rounded-full px-6 py-3 shadow-lg max-w-md mx-auto transition-all duration-300 hover:shadow-xl hover:bg-opacity-90">
            <a href="{{ route('dashboard') }}" 
               class="text-gray-600 hover:text-purple-600 mx-2 transition-transform duration-200 ease-in-out hover:scale-110 focus:outline-none focus:ring-2 focus:ring-purple-500 rounded-full {{ request()->routeIs('dashboard') ? 'text-purple-600' : '' }}">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </a>
            <a href="{{ route('bookings.index') }}" 
               class="text-gray-600 hover:text-emerald-600 mx-2 transition-all duration-200 ease-in-out hover:scale-110 focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded-full {{ request()->routeIs('bookings.*') ? 'text-emerald-600' : '' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </a>
            <a href="{{ route('b2b.dashboard') }}" 
               class="text-gray-600 hover:text-blue-600 mx-2 transition-all duration-200 ease-in-out hover:rotate-12 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full {{ request()->routeIs('b2b.*') ? 'text-blue-600' : '' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </a>
            <a href="{{ route('properties.index') }}" 
               class="text-gray-600 hover:text-purple-600 mx-2 transition-all duration-200 ease-in-out hover:scale-110 focus:outline-none focus:ring-2 focus:ring-purple-500 rounded-full {{ request()->routeIs('properties.*') ? 'text-purple-600' : '' }}">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </a>
            @if(auth()->user()->is_admin)
            <a href="{{ route('admin.dashboard') }}" 
               class="text-gray-600 hover:text-red-600 mx-2 transition-transform duration-200 ease-in-out hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-full {{ request()->routeIs('admin.*') ? 'text-red-600' : '' }}">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.9 1 3 1.9 3 3V21C3 22.1 3.9 23 5 23H19C20.1 23 21 22.1 21 21V9M19 9H14V4H19V9Z"/>
                </svg>
            </a>
            @endif
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="text-gray-600 hover:text-gray-800 mx-2 transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-500 rounded-full p-1">
                    <svg class="h-6 w-6" stroke="currentColor" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
                    </svg>
                </button>
                <div x-show="open" x-cloak @click.away="open = false" x-transition 
                     class="absolute bottom-12 right-0 bg-white rounded-xl shadow-lg border border-gray-200 py-2 w-48">
                    <a href="{{ route('customers.index') }}" @click="open = false" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Customers
                    </a>
                    <a href="{{ route('resources.index') }}" @click="open = false" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Resources
                    </a>
                    <a href="{{ route('pricing.index') }}" @click="open = false" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pricing
                    </a>
                    <a href="{{ route('reports.analytics') }}" @click="open = false" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Reports
                    </a>
                    <a href="{{ route('properties.create') }}" @click="open = false" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Property
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Globe Loader -->
    <x-globe-loader />
    
    @livewireScripts
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Globe loader functions
        window.showGlobeLoader = function() {
            document.getElementById('globe-loader').style.display = 'flex';
        };
        
        window.hideGlobeLoader = function() {
            document.getElementById('globe-loader').style.display = 'none';
        };
        
        // Show loader on Livewire requests
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updating', () => {
                showGlobeLoader();
            });
            
            Livewire.hook('morph.updated', () => {
                hideGlobeLoader();
            });
            
            Livewire.hook('request', () => {
                showGlobeLoader();
            });
            
            Livewire.hook('response', () => {
                hideGlobeLoader();
            });
        });
        
        // Show loader on page navigation
        window.addEventListener('beforeunload', function() {
            showGlobeLoader();
        });
        
        // Hide loader when page loads
        window.addEventListener('load', function() {
            hideGlobeLoader();
        });
    </script>
</body>
</html>