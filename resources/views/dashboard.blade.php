@extends('layouts.mobile')

@section('title', 'Dashboard - Hospitality Manager')
@section('page-title', 'Dashboard')

@section('content')
    @if(session('success'))
        <div class="bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 px-4 py-3 rounded-xl mb-6 backdrop-blur-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
        <!-- Welcome Section -->
        <div x-show="loaded" x-transition:enter="transition ease-out duration-700" 
             x-transition:enter-start="opacity-0 transform translate-y-8" 
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="relative overflow-hidden bg-gradient-to-br from-slate-800/40 to-slate-900/60 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6 shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-2">Welcome back!</h2>
                <p class="text-slate-300">{{ auth()->user()->name }}</p>
                <div class="absolute top-4 right-4 w-16 h-16 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-full blur-xl"></div>
            </div>
        </div>

        @if($properties->isEmpty())
            <!-- Get Started Card -->
            <div x-show="loaded" x-transition:enter="transition ease-out duration-700 delay-200" 
                 x-transition:enter-start="opacity-0 transform translate-y-8" 
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="relative overflow-hidden bg-gradient-to-br from-indigo-600/20 to-purple-600/20 backdrop-blur-xl border border-indigo-500/30 rounded-2xl p-6 shadow-2xl hover:shadow-indigo-500/25 transition-all duration-500 group">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/5 to-purple-600/5 group-hover:from-indigo-600/10 group-hover:to-purple-600/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <h3 class="text-xl font-semibold text-white mb-3">Get Started</h3>
                    <p class="text-slate-300 mb-4">Create your first property to begin managing your hospitality business.</p>
                    <a href="{{ route('properties.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-indigo-500/25">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create First Property
                    </a>
                </div>
            </div>
        @else
            <!-- Quick Actions Grid -->
            <div x-show="loaded" x-transition:enter="transition ease-out duration-700 delay-300" 
                 x-transition:enter-start="opacity-0 transform translate-y-8" 
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                
                <!-- Add Property Card -->
                <a href="{{ route('properties.create') }}" 
                   class="group relative overflow-hidden bg-gradient-to-br from-emerald-500/20 to-teal-600/20 backdrop-blur-xl border border-emerald-400/30 rounded-2xl p-4 hover:shadow-2xl hover:shadow-emerald-500/25 transform hover:scale-105 transition-all duration-500">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-teal-500/10 group-hover:from-emerald-500/20 group-hover:to-teal-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-emerald-500/50 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-white">Add Property</p>
                    </div>
                </a>

                <!-- Bookings Card -->
                <a href="{{ route('bookings.index') }}" 
                   class="group relative overflow-hidden bg-gradient-to-br from-blue-500/20 to-cyan-600/20 backdrop-blur-xl border border-blue-400/30 rounded-2xl p-4 hover:shadow-2xl hover:shadow-blue-500/25 transform hover:scale-105 transition-all duration-500">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-cyan-500/10 group-hover:from-blue-500/20 group-hover:to-cyan-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-blue-500/50 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-white">Bookings</p>
                    </div>
                </a>

                <!-- B2B Partners Card -->
                <a href="{{ route('b2b.dashboard') }}" 
                   class="group relative overflow-hidden bg-gradient-to-br from-purple-500/20 to-pink-600/20 backdrop-blur-xl border border-purple-400/30 rounded-2xl p-4 hover:shadow-2xl hover:shadow-purple-500/25 transform hover:scale-105 transition-all duration-500">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-pink-500/10 group-hover:from-purple-500/20 group-hover:to-pink-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-purple-500/50 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-white">B2B Partners</p>
                    </div>
                </a>

                <!-- Pricing Card -->
                <a href="{{ route('pricing.calendar') }}" 
                   class="group relative overflow-hidden bg-gradient-to-br from-amber-500/20 to-orange-600/20 backdrop-blur-xl border border-amber-400/30 rounded-2xl p-4 hover:shadow-2xl hover:shadow-amber-500/25 transform hover:scale-105 transition-all duration-500">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 to-orange-500/10 group-hover:from-amber-500/20 group-hover:to-orange-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-amber-500/50 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-white">Pricing</p>
                    </div>
                </a>

                <!-- Reports Card -->
                <a href="{{ route('reports.analytics') }}" 
                   class="group relative overflow-hidden bg-gradient-to-br from-indigo-500/20 to-blue-600/20 backdrop-blur-xl border border-indigo-400/30 rounded-2xl p-4 hover:shadow-2xl hover:shadow-indigo-500/25 transform hover:scale-105 transition-all duration-500">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-blue-500/10 group-hover:from-indigo-500/20 group-hover:to-blue-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-indigo-500/50 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-white">Reports</p>
                    </div>
                </a>

                <!-- Properties Card -->
                <a href="{{ route('properties.index') }}" 
                   class="group relative overflow-hidden bg-gradient-to-br from-rose-500/20 to-red-600/20 backdrop-blur-xl border border-rose-400/30 rounded-2xl p-4 hover:shadow-2xl hover:shadow-rose-500/25 transform hover:scale-105 transition-all duration-500">
                    <div class="absolute inset-0 bg-gradient-to-r from-rose-500/10 to-red-500/10 group-hover:from-rose-500/20 group-hover:to-red-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-rose-500/50 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-white">Properties</p>
                    </div>
                </a>
            </div>

            <!-- Properties List -->
            <div x-show="loaded" x-transition:enter="transition ease-out duration-700 delay-500" 
                 x-transition:enter-start="opacity-0 transform translate-y-8" 
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="relative overflow-hidden bg-gradient-to-br from-slate-800/40 to-slate-900/60 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-600/5 to-slate-700/5"></div>
                <div class="relative z-10">
                    <div class="px-6 py-5 border-b border-slate-700/50">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            Your Properties
                        </h3>
                    </div>
                    <div class="p-2">
                        @foreach($properties->take(3) as $index => $property)
                            <div x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false"
                                 class="mx-4 my-3 p-4 bg-gradient-to-r from-slate-700/30 to-slate-800/30 backdrop-blur-sm border border-slate-600/30 rounded-xl hover:border-slate-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-slate-900/50">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-white mb-1">{{ $property->name }}</h4>
                                        <p class="text-sm text-slate-400">{{ $property->category->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                            @if($property->status === 'pending') bg-amber-500/20 text-amber-300 border border-amber-500/30
                                            @elseif($property->status === 'active') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                            @else bg-red-500/20 text-red-300 border border-red-500/30 @endif">
                                            {{ ucfirst($property->status) }}
                                        </span>
                                        <svg x-show="hover" x-transition class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($properties->count() > 3)
                        <div class="px-6 py-4 border-t border-slate-700/50">
                            <a href="{{ route('properties.index') }}" 
                               class="inline-flex items-center text-sm text-blue-400 hover:text-blue-300 transition-colors group">
                                View all {{ $properties->count() }} properties
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection