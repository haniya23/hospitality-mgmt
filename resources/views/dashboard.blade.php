@extends('layouts.mobile')

@section('title', 'Dashboard - Hospitality Manager')
@section('page-title', 'Dashboard')

@section('content')
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-6 shadow-lg">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome back!</h2>
            <p class="text-gray-600">{{ auth()->user()->name }}</p>
        </div>

        @if($properties->isEmpty())
            <!-- Get Started Card -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-6 text-white">
                <h3 class="text-xl font-bold mb-2">Get Started</h3>
                <p class="mb-4 opacity-90">Create your first property to begin managing your hospitality business.</p>
                <a href="{{ route('properties.create') }}" 
                   class="bg-white bg-opacity-20 backdrop-blur-md text-white px-6 py-3 rounded-full font-medium hover:bg-opacity-30 transition-all duration-200">
                    Create First Property
                </a>
            </div>
        @else
            <!-- Quick Actions -->
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('properties.create') }}" 
                   class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-200">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Add Property</p>
                    </div>
                </a>
                <a href="{{ route('properties.index') }}" 
                   class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-200">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900">View All</p>
                    </div>
                </a>
            </div>

            <!-- Properties List -->
            <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Your Properties</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($properties->take(3) as $property)
                        <div class="px-6 py-4">
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $property->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $property->category->name ?? 'N/A' }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium rounded-full
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
                    <div class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('properties.index') }}" class="text-sm text-purple-600 font-medium">
                            View all {{ $properties->count() }} properties →
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection