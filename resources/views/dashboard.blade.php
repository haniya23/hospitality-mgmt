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
        <div class="card-info">
            <h2 class="heading-1">Welcome back!</h2>
            <p class="text-secondary">{{ auth()->user()->name }}</p>
        </div>

        @if($properties->isEmpty())
            <!-- Get Started Card -->
            <div class="card-action">
                <h3 class="heading-2">Get Started</h3>
                <p class="body-text spacer-sm">Create your first property to begin managing your hospitality business.</p>
                <a href="{{ route('properties.create') }}" class="btn-secondary">
                    Create First Property
                </a>
            </div>
        @else
            <!-- Quick Actions -->
            <div class="grid-actions">
                <a href="{{ route('properties.create') }}" class="glass-card">
                    <div class="text-center p-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <p class="small-text text-primary">Add Property</p>
                    </div>
                </a>
                <a href="{{ route('bookings.index') }}" class="glass-card">
                    <div class="text-center p-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="small-text text-primary">Bookings</p>
                    </div>
                </a>
                <a href="{{ route('b2b.dashboard') }}" class="glass-card">
                    <div class="text-center p-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="small-text text-primary">B2B Partners</p>
                    </div>
                </a>
                <a href="{{ route('pricing.calendar') }}" class="glass-card">
                    <div class="text-center p-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="small-text text-primary">Pricing</p>
                    </div>
                </a>
                <a href="{{ route('reports.analytics') }}" class="glass-card">
                    <div class="text-center p-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="small-text text-primary">Reports</p>
                    </div>
                </a>
                <a href="{{ route('properties.index') }}" class="glass-card">
                    <div class="text-center p-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                            </svg>
                        </div>
                        <p class="small-text text-primary">Properties</p>
                    </div>
                </a>
            </div>

            <!-- Properties List -->
            <div class="glass-card">
                <div class="px-6 py-4">
                    <h3 class="heading-3 spacer-sm">Your Properties</h3>
                </div>
                <div class="divider"></div>
                <div>
                    @foreach($properties->take(3) as $property)
                        <div class="card-list">
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <h4 class="text-primary">{{ $property->name }}</h4>
                                    <p class="small-text text-accent">{{ $property->category->name ?? 'N/A' }}</p>
                                </div>
                                <span class="@if($property->status === 'pending') status-pending
                                    @elseif($property->status === 'active') status-active
                                    @else status-inactive @endif">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($properties->count() > 3)
                    <div class="divider"></div>
                    <div class="px-6 py-3">
                        <a href="{{ route('properties.index') }}" class="small-text text-accent hover:text-primary transition-colors">
                            View all {{ $properties->count() }} properties →
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection