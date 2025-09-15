@extends('layouts.mobile')

@section('title', 'Dashboard - Hospitality Manager')
@section('page-title', 'Dashboard')

@section('content')
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4 sm:space-y-6">
        <!-- Welcome Section -->
        <div class="bg-slate-800 bg-opacity-90 backdrop-blur-md rounded-2xl p-4 sm:p-6 shadow-xl border border-slate-700">
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-2">Welcome back!</h2>
            <p class="text-sm sm:text-base text-slate-300">{{ auth()->user()->name }}</p>
        </div>

        @if($properties->isEmpty())
            <!-- Get Started Card -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-4 sm:p-6 text-white shadow-xl">
                <h3 class="text-lg sm:text-xl font-bold mb-2">Get Started</h3>
                <p class="mb-4 opacity-90 text-sm sm:text-base">Create your first property to begin managing your hospitality business.</p>
                <a href="{{ route('properties.create') }}" 
                   class="inline-block bg-white bg-opacity-20 backdrop-blur-md text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium hover:bg-opacity-30 transition-all duration-200 text-sm sm:text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Create First Property
                </a>
            </div>
        @else
            <!-- Quick Actions -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                <a href="{{ route('properties.create') }}" 
                   class="bg-slate-800 bg-opacity-90 backdrop-blur-md rounded-2xl p-3 sm:p-4 shadow-xl hover:shadow-2xl transition-all duration-200 transform hover:-translate-y-0.5 border border-slate-700">
                    <div class="text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-white">Add Property</p>
                    </div>
                </a>
                <a href="{{ route('bookings.index') }}" 
                   class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-3 sm:p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    <div class="text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-900">Bookings</p>
                    </div>
                </a>
                <a href="{{ route('b2b.dashboard') }}" 
                   class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-3 sm:p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    <div class="text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-900">B2B Partners</p>
                    </div>
                </a>
                <a href="{{ route('pricing.calendar') }}" 
                   class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-3 sm:p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    <div class="text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-900">Pricing</p>
                    </div>
                </a>
                <a href="{{ route('reports.analytics') }}" 
                   class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-3 sm:p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    <div class="text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-900">Reports</p>
                    </div>
                </a>
                <a href="{{ route('properties.index') }}" 
                   class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-3 sm:p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    <div class="text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-900">Properties</p>
                    </div>
                </a>
            </div>

            <!-- Properties List -->
            <div class="bg-slate-800 bg-opacity-90 backdrop-blur-md rounded-2xl shadow-xl overflow-hidden border border-slate-700">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-700">
                    <h3 class="text-base sm:text-lg font-semibold text-white">Your Properties</h3>
                </div>
                <div class="divide-y divide-slate-700">
                    @foreach($properties->take(3) as $property)
                        <div class="px-4 sm:px-6 py-3 sm:py-4">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-white text-sm sm:text-base">{{ $property->name }}</h4>
                                    <p class="text-xs sm:text-sm text-slate-400">{{ $property->category->name ?? 'N/A' }}</p>
                                </div>
                                <span class="px-2 sm:px-3 py-1 text-xs font-medium rounded-full self-start sm:self-center
                                    @if($property->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($property->status === 'active') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($properties->count() > 3)
                    <div class="px-4 sm:px-6 py-3 bg-slate-700">
                        <a href="{{ route('properties.index') }}" class="text-xs sm:text-sm text-blue-400 font-medium hover:text-blue-300 transition-colors">
                            View all {{ $properties->count() }} properties →
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection