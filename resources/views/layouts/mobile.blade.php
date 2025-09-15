<!DOCTYPE html>
<html lang="en" class="bg-gradient-to-br from-green-900 to-emerald-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hospitality Manager')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="{{ asset('css/globe-loader.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/glass-olive-theme.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        [x-cloak] { display: none !important; }
        .truck-body { animation: truck-motion 1s linear infinite; }
        .road-line { animation: road-animation 1.4s linear infinite; }
    </style>
</head>
<body class="theme-bg" x-data="{ sidebarOpen: false }">
    <!-- Top Bar -->
    <div class="nav-top">
        <div class="flex-nav">
            <button @click="sidebarOpen = !sidebarOpen" class="text-primary hover:text-accent transition-all duration-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="heading-2">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center space-x-2">
                @if(auth()->user()->is_admin)
                    <span class="status-inactive">ADMIN</span>
                @endif
                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
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
         class="fixed left-0 top-0 h-full w-72 nav-sidebar z-50 transform">
        
        <div class="p-6">
            <div class="flex items-center justify-between mb-8">
                <h2 class="heading-2">Menu</h2>
                <button @click="sidebarOpen = false" class="btn-icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors {{ request()->routeIs('dashboard') ? 'bg-olive text-white' : '' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('properties.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors {{ request()->routeIs('properties.*') ? 'bg-olive text-white' : '' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Properties
                    </a>
                </li>
                <li>
                    <a href="{{ route('bookings.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors {{ request()->routeIs('bookings.*') ? 'bg-olive text-white' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Bookings
                    </a>
                </li>
                <li>
                    <a href="{{ route('customers.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors {{ request()->routeIs('customers.*') ? 'bg-olive text-white' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Customers
                    </a>
                </li>
                <li>
                    <a href="{{ route('b2b.dashboard') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors {{ request()->routeIs('b2b.*') ? 'bg-olive text-white' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        B2B Partners
                    </a>
                </li>
                <li>
                    <a href="{{ route('resources.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors {{ request()->routeIs('resources.*') ? 'bg-olive text-white' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Resources
                    </a>
                </li>
                <li>
                    <a href="{{ route('pricing.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors {{ request()->routeIs('pricing.*') ? 'bg-olive text-white' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pricing
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.analytics') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors {{ request()->routeIs('reports.*') ? 'bg-olive text-white' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Reports
                    </a>
                </li>
                @if(auth()->user()->is_admin)
                <li>
                    <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors {{ request()->routeIs('admin.*') ? 'bg-olive text-white' : '' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Admin Panel
                    </a>
                </li>
                @endif
                <li class="pt-4 border-t border-glass-border">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-white hover:bg-opacity-20 text-primary transition-colors w-full text-left">
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
    <div class="container-mobile">
        @yield('content')
    </div>

    <!-- Bottom Navigation -->
    <div class="nav-bottom">
        <div class="flex justify-center items-center px-2">
            <div class="flex items-center justify-between w-full max-w-sm">
                <a href="{{ route('dashboard') }}" class="flex-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </a>
                <a href="{{ route('bookings.index') }}" class="flex-nav-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </a>
                <a href="{{ route('b2b.dashboard') }}" class="flex-nav-item {{ request()->routeIs('b2b.*') ? 'active' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </a>
                <a href="{{ route('properties.index') }}" class="flex-nav-item {{ request()->routeIs('properties.*') ? 'active' : '' }}">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </a>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex-nav-item">
                        <svg class="h-5 w-5" stroke="currentColor" viewBox="0 0 24 24" fill="none">
                            <path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false" x-transition 
                         class="absolute bottom-full mb-2 right-0 glass-card py-2 w-48">
                        <a href="{{ route('customers.index') }}" @click="open = false" 
                           class="flex items-center px-4 py-2 body-text hover:bg-white hover:bg-opacity-20">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Customers
                        </a>
                        <a href="{{ route('resources.index') }}" @click="open = false" 
                           class="flex items-center px-4 py-2 body-text hover:bg-white hover:bg-opacity-20">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Resources
                        </a>
                        <a href="{{ route('pricing.index') }}" @click="open = false" 
                           class="flex items-center px-4 py-2 body-text hover:bg-white hover:bg-opacity-20">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pricing
                        </a>
                        <a href="{{ route('reports.analytics') }}" @click="open = false" 
                           class="flex items-center px-4 py-2 body-text hover:bg-white hover:bg-opacity-20">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Reports
                        </a>
                        @if(auth()->user()->is_admin)
                        <div class="divider"></div>
                        <a href="{{ route('admin.dashboard') }}" @click="open = false" 
                           class="flex items-center px-4 py-2 body-text hover:bg-white hover:bg-opacity-20">
                            <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.9 1 3 1.9 3 3V21C3 22.1 3.9 23 5 23H19C20.1 23 21 22.1 21 21V9M19 9H14V4H19V9Z"/>
                            </svg>
                            Admin Panel
                        </a>
                        @endif
                    </div>
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