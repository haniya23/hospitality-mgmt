@extends('layouts.app')

@section('title', 'Property Pending Approval - Hospitality Manager')
@section('page-title', 'Property Pending Approval')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <!-- Icon and Message -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-yellow-100 to-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Property Needs Approval</h1>
                <p class="text-gray-600 mb-6">Your property "{{ $property->name }}" is currently <span class="font-semibold text-yellow-600">{{ ucfirst($property->status) }}</span> and requires admin approval before you can make any updates.</p>
            </div>

            <!-- Property Info Card -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 mb-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $property->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $property->category->name ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($property->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($property->status === 'draft') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($property->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium">{{ $property->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('dashboard') }}" 
                   class="block w-full bg-green-600 text-white text-center py-3 px-4 rounded-xl font-medium hover:bg-green-700 transition-colors">
                    Back to Dashboard
                </a>
                
                <a href="{{ route('properties.index') }}" 
                   class="block w-full border border-gray-300 text-gray-700 text-center py-3 px-4 rounded-xl font-medium hover:bg-gray-50 transition-colors">
                    View All Properties
                </a>
            </div>

            <!-- Help Text -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    Once approved by an admin, you'll be able to edit and manage all property details.
                </p>
            </div>
        </div>
    </div>
@endsection