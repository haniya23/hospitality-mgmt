@extends('layouts.app')

@section('title', 'View Accommodation')

@section('header')
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $accommodation->custom_name }}</h1>
            <p class="text-gray-600">{{ $accommodation->property->name }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('accommodations.edit', $accommodation) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('accommodations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Accommodations
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Basic Information -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Property</label>
                <div class="text-lg font-semibold text-gray-900">{{ $accommodation->property->name }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Accommodation Name</label>
                <div class="text-lg font-semibold text-gray-900">{{ $accommodation->custom_name }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                <div class="text-lg font-semibold text-gray-900">{{ $accommodation->predefinedType->name ?? 'Custom' }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Base Price</label>
                <div class="text-lg font-semibold text-gray-900">₹{{ number_format($accommodation->base_price, 2) }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Max Occupancy</label>
                <div class="text-lg font-semibold text-gray-900">{{ $accommodation->max_occupancy }} guests</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Size</label>
                <div class="text-lg font-semibold text-gray-900">{{ $accommodation->size ? $accommodation->size . ' sq ft' : 'Not specified' }}</div>
            </div>
        </div>
        
        @if($accommodation->description)
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
            <div class="text-gray-700 bg-gray-50 rounded-lg p-4">{{ $accommodation->description }}</div>
        </div>
        @endif
    </div>

    <!-- Amenities -->
    @if($accommodation->amenities->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Amenities</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($accommodation->amenities as $amenity)
                <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-lg">
                    <i class="{{ $amenity->icon }} text-blue-600"></i>
                    <span class="text-sm font-medium text-gray-700">{{ $amenity->name }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Photos -->
    @if($accommodation->photos->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Photos</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($accommodation->photos as $photo)
                <div class="relative group">
                    <img src="{{ Storage::url($photo->file_path) }}" alt="Accommodation photo" 
                         class="w-full h-48 object-cover rounded-lg">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity rounded-lg flex items-center justify-center">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" class="text-white hover:text-red-400 transition-colors">
                                <i class="fas fa-trash text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4">
        <a href="{{ route('accommodations.index') }}" 
           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Back to List
        </a>
        <a href="{{ route('accommodations.edit', $accommodation) }}" 
           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Edit Accommodation
        </a>
    </div>
</div>
@endsection
