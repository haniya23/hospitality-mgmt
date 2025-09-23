<!DOCTYPE html>
<html lang="en" class="bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900">
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
    <!-- Desktop Sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:w-72 lg:bg-white lg:p-5 lg:shadow-md lg:shadow-purple-200/50 lg:flex lg:flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                    <span class="text-white text-lg font-bold">H</span>
                </div>
                <span class="text-xl font-bold text-gray-900">Hospitality</span>
            </div>
        </div>

        <!-- Navigation -->
        <ul class="w-full flex flex-col gap-2 flex-1">
            <li class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                <a href="{{ route('dashboard') }}" class="p-16-semibold flex size-full gap-4 p-4 group font-semibold rounded-full bg-cover hover:bg-purple-100 hover:shadow-inner {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all ease-linear">
                    <svg stroke="#000000" class="icon glyph size-6 {{ request()->routeIs('dashboard') ? 'fill-white stroke-white' : 'group-hover:fill-purple-600 group-hover:stroke-purple-600' }}" viewBox="0 0 24 24" fill="#000000">
                        <path d="M14,10V22H4a2,2,0,0,1-2-2V10Z"></path>
                        <path d="M22,10V20a2,2,0,0,1-2,2H16V10Z"></path>
                        <path d="M22,4V8H2V4A2,2,0,0,1,4,2H20A2,2,0,0,1,22,4Z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>

            <li class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                <a href="{{ route('properties.index') }}" class="p-16-semibold flex size-full gap-4 p-4 group font-semibold rounded-full bg-cover hover:bg-purple-100 hover:shadow-inner {{ request()->routeIs('properties.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="size-6">
                        <path class="{{ request()->routeIs('properties.*') ? 'fill-white' : 'group-hover:fill-purple-600' }}" fill="#000" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Properties
                </a>
            </li>

            <li class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                <a href="{{ route('bookings.index') }}" class="p-16-semibold flex size-full gap-4 p-4 group font-semibold rounded-full bg-cover hover:bg-purple-100 hover:shadow-inner {{ request()->routeIs('bookings.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="size-6">
                        <path class="{{ request()->routeIs('bookings.*') ? 'fill-white' : 'group-hover:fill-purple-600' }}" fill="#000" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Bookings
                </a>
            </li>

            <li class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                <a href="{{ route('customers.index') }}" class="p-16-semibold flex size-full gap-4 p-4 group font-semibold rounded-full bg-cover hover:bg-purple-100 hover:shadow-inner {{ request()->routeIs('customers.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="size-6">
                        <path class="{{ request()->routeIs('customers.*') ? 'fill-white' : 'group-hover:fill-purple-600' }}" fill="#000" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Customers
                </a>
            </li>

            <li class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                <a href="{{ route('b2b.dashboard') }}" class="p-16-semibold flex size-full gap-4 p-4 group font-semibold rounded-full bg-cover hover:bg-purple-100 hover:shadow-inner {{ request()->routeIs('b2b.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="size-6">
                        <path class="{{ request()->routeIs('b2b.*') ? 'fill-white' : 'group-hover:fill-purple-600' }}" fill="#000" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    B2B Partners
                </a>
            </li>

            <li class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                <a href="{{ route('pricing.index') }}" class="p-16-semibold flex size-full gap-4 p-4 group font-semibold rounded-full bg-cover hover:bg-purple-100 hover:shadow-inner {{ request()->routeIs('pricing.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="size-6">
                        <path class="{{ request()->routeIs('pricing.*') ? 'fill-white' : 'group-hover:fill-purple-600' }}" fill="#000" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Pricing
                </a>
            </li>

            <li class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                <a href="{{ route('reports.analytics') }}" class="p-16-semibold flex size-full gap-4 p-4 group font-semibold rounded-full bg-cover hover:bg-purple-100 hover:shadow-inner {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="size-6">
                        <path class="{{ request()->routeIs('reports.*') ? 'fill-white' : 'group-hover:fill-purple-600' }}" fill="#000" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Reports
                </a>
            </li>



            <li class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap mt-auto">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="p-16-semibold flex size-full gap-4 p-4 group font-semibold rounded-full bg-cover hover:bg-purple-100 hover:shadow-inner focus:bg-gradient-to-r from-purple-400 to-purple-600 focus:text-white text-gray-700 transition-all ease-linear">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="size-6">
                            <path class="group-focus:fill-white" fill="#000000" d="M17.2929 14.2929C16.9024 14.6834 16.9024 15.3166 17.2929 15.7071C17.6834 16.0976 18.3166 16.0976 18.7071 15.7071L21.6201 12.7941C21.6351 12.7791 21.6497 12.7637 21.6637 12.748C21.87 12.5648 22 12.2976 22 12C22 11.7024 21.87 11.4352 21.6637 11.252C21.6497 11.2363 21.6351 11.2209 21.6201 11.2059L18.7071 8.29289C18.3166 7.90237 17.6834 7.90237 17.2929 8.29289C16.9024 8.68342 16.9024 9.31658 17.2929 9.70711L18.5858 11H13C12.4477 11 12 11.4477 12 12C12 12.5523 12.4477 13 13 13H18.5858L17.2929 14.2929Z"></path>
                            <path class="group-focus:fill-white" fill="#000" d="M5 2C3.34315 2 2 3.34315 2 5V19C2 20.6569 3.34315 22 5 22H14.5C15.8807 22 17 20.8807 17 19.5V16.7326C16.8519 16.647 16.7125 16.5409 16.5858 16.4142C15.9314 15.7598 15.8253 14.7649 16.2674 14H13C11.8954 14 11 13.1046 11 12C11 10.8954 11.8954 10 13 10H16.2674C15.8253 9.23514 15.9314 8.24015 16.5858 7.58579C16.7125 7.4591 16.8519 7.35296 17 7.26738V4.5C17 3.11929 15.8807 2 14.5 2H5Z"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
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
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/25 hover:backdrop-blur-md text-primary transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-white/30 backdrop-blur-md border border-white/40 shadow-lg' : '' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('properties.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/25 hover:backdrop-blur-md text-primary transition-all duration-300 {{ request()->routeIs('properties.*') ? 'bg-white/30 backdrop-blur-md border border-white/40 shadow-lg' : '' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Properties
                    </a>
                </li>
                <li>
                    <a href="{{ route('bookings.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/25 hover:backdrop-blur-md text-primary transition-all duration-300 {{ request()->routeIs('bookings.*') ? 'bg-white/30 backdrop-blur-md border border-white/40 shadow-lg' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Bookings
                    </a>
                </li>
                <li>
                    <a href="{{ route('customers.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/25 hover:backdrop-blur-md text-primary transition-all duration-300 {{ request()->routeIs('customers.*') ? 'bg-white/30 backdrop-blur-md border border-white/40 shadow-lg' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Customers
                    </a>
                </li>
                <li>
                    <a href="{{ route('b2b.dashboard') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/25 hover:backdrop-blur-md text-primary transition-all duration-300 {{ request()->routeIs('b2b.*') ? 'bg-white/30 backdrop-blur-md border border-white/40 shadow-lg' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        B2B Partners
                    </a>
                </li>

                <li>
                    <a href="{{ route('pricing.index') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/25 hover:backdrop-blur-md text-primary transition-all duration-300 {{ request()->routeIs('pricing.*') ? 'bg-white/30 backdrop-blur-md border border-white/40 shadow-lg' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pricing
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.analytics') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/25 hover:backdrop-blur-md text-primary transition-all duration-300 {{ request()->routeIs('reports.*') ? 'bg-white/30 backdrop-blur-md border border-white/40 shadow-lg' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Reports
                    </a>
                </li>

                <li class="pt-4 border-t border-glass-border">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/25 hover:backdrop-blur-md text-primary transition-all duration-300 w-full text-left">
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
    <div class="container-mobile lg:ml-72">
        <!-- Mobile Header -->
        <div class="lg:hidden bg-gradient-to-r from-purple-600 to-blue-600 text-white p-4 mb-6 rounded-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition-all">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold">@yield('page-title', 'Dashboard')</h1>

                    </div>
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
            </div>
        </div>
        
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
                           class="flex items-center px-4 py-2 body-text hover:bg-white/25 hover:backdrop-blur-md transition-all duration-300">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Customers
                        </a>

                        <a href="{{ route('pricing.index') }}" @click="open = false" 
                           class="flex items-center px-4 py-2 body-text hover:bg-white/25 hover:backdrop-blur-md transition-all duration-300">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pricing
                        </a>
                        <a href="{{ route('reports.analytics') }}" @click="open = false" 
                           class="flex items-center px-4 py-2 body-text hover:bg-white/25 hover:backdrop-blur-md transition-all duration-300">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Reports
                        </a>

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