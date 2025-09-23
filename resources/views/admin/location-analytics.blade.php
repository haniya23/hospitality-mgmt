@extends('layouts.mobile')

@section('title', 'Location Analytics - Admin')
@section('page-title', 'Location Analytics')

@section('content')

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-xl font-bold text-gray-900">Property Location Analytics</h2>
            <p class="text-sm text-gray-600">Property distribution and approval rates by location</p>
        </div>

        <!-- Location Stats -->
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Location Statistics</h3>
            </div>
            
            @if($locationStats->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p>No location data available.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($locationStats as $stat)
                        <div class="px-6 py-4">
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $stat['location'] }}</h4>
                                    <div class="flex items-center space-x-4 mt-1">
                                        <span class="text-sm text-gray-600">
                                            {{ $stat['property_count'] }} {{ Str::plural('property', $stat['property_count']) }}
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            {{ $stat['approval_rate'] }}% approval rate
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <!-- Property Count Badge -->
                                    <span class="px-3 py-1 text-sm font-medium rounded-full
                                        @if($stat['property_count'] >= 10) bg-green-100 text-green-800
                                        @elseif($stat['property_count'] >= 5) bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $stat['property_count'] }}
                                    </span>
                                    
                                    <!-- Approval Rate Badge -->
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($stat['approval_rate'] >= 80) bg-green-100 text-green-800
                                            @elseif($stat['approval_rate'] >= 60) bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $stat['approval_rate'] }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Visual Progress Bar for Approval Rate -->
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Approval Rate</span>
                                    <span>{{ $stat['approval_rate'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full
                                        @if($stat['approval_rate'] >= 80) bg-green-500
                                        @elseif($stat['approval_rate'] >= 60) bg-yellow-500
                                        @else bg-red-500
                                        @endif"
                                        style="width: {{ $stat['approval_rate'] }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-4 text-white">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $locationStats->sum('property_count') }}</div>
                    <div class="text-sm opacity-90">Total Properties</div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-4 text-white">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $locationStats->count() }}</div>
                    <div class="text-sm opacity-90">Active Locations</div>
                </div>
            </div>
        </div>

        <!-- Top Performing Locations -->
        @if($locationStats->isNotEmpty())
            <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Top Performing Locations</h3>
                </div>
                
                <div class="px-6 py-4">
                    <div class="space-y-3">
                        @foreach($locationStats->sortByDesc('approval_rate')->take(5) as $index => $stat)
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                    @if($index === 0) bg-yellow-100 text-yellow-800
                                    @elseif($index === 1) bg-gray-100 text-gray-800
                                    @elseif($index === 2) bg-orange-100 text-orange-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $stat['location'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $stat['approval_rate'] }}% approval rate</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-medium text-gray-900">{{ $stat['property_count'] }}</span>
                                    <p class="text-xs text-gray-500">properties</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection