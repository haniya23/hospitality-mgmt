@extends('layouts.mobile')

@section('title', 'Properties - Hospitality Manager')
@section('page-title', 'Properties')

@section('content')
    <div class="space-y-6">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Your Properties</h2>
            <a href="{{ route('properties.create') }}" 
               class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                + Add New
            </a>
        </div>

        @if($properties->isEmpty())
            <!-- Empty State -->
            <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-8 text-center">
                <svg class="h-16 w-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h6m-6 4h6m-6 4h6"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Properties Yet</h3>
                <p class="text-gray-600 mb-4">Create your first property to get started with managing your hospitality business.</p>
                <a href="{{ route('properties.create') }}" 
                   class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-full font-medium hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                    Create First Property
                </a>
            </div>
        @else
            <!-- Properties Grid -->
            <div class="space-y-4">
                @foreach($properties as $property)
                    <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
                        @php
                            $mainImage = $property->photos()->where('is_main', true)->first();
                        @endphp
                        @if($mainImage)
                            <div class="relative h-48 w-full">
                                <img src="{{ asset($mainImage->file_path) }}" 
                                     alt="{{ $property->name }}" 
                                     class="w-full h-full object-cover"
                                >
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $property->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $property->category->name ?? 'N/A' }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('properties.edit', $property) }}" 
                                       class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-full transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($property->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($property->status === 'active') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($property->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($property->description)
                                <p class="text-sm text-gray-700 mb-4 bg-gray-50 p-3 rounded-lg">{{ $property->description }}</p>
                            @endif
                            
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Created {{ $property->created_at->format('M d, Y') }}</span>
                                @if($property->approved_at)
                                    <span>Approved {{ $property->approved_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                            
                            @if($property->status === 'active')
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-3 gap-2 text-center">
                                        <button class="bg-blue-50 text-blue-600 py-2 rounded-lg text-xs font-medium">
                                            Rooms
                                        </button>
                                        <button class="bg-green-50 text-green-600 py-2 rounded-lg text-xs font-medium">
                                            Bookings
                                        </button>
                                        <button class="bg-purple-50 text-purple-600 py-2 rounded-lg text-xs font-medium">
                                            Settings
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection