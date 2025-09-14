@extends('layouts.mobile')

@section('title', 'Admin Dashboard - Hospitality Manager')
@section('page-title', 'Admin Panel')

@section('content')

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- Admin Stats -->
        <div class="bg-gradient-to-r from-red-500 to-pink-600 rounded-2xl p-6 text-white">
            <h2 class="text-xl font-bold mb-2">Admin Dashboard</h2>
            <p class="opacity-90">{{ $pendingProperties->count() }} properties pending approval</p>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pending Approvals</h3>
            </div>
            
            @if($pendingProperties->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>No pending properties for approval.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($pendingProperties as $property)
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $property->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $property->category->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $property->owner->name }} • {{ $property->owner->mobile_number }}</p>
                                </div>
                                
                                @if($property->description)
                                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $property->description }}</p>
                                @endif
                                
                                <p class="text-xs text-gray-500">{{ $property->created_at->format('M d, Y H:i') }}</p>
                                
                                <div class="flex space-x-3">
                                    <form method="POST" action="{{ route('admin.properties.approve', $property) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-2 rounded-xl text-sm font-medium hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.properties.reject', $property) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white py-2 rounded-xl text-sm font-medium hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                                onclick="return confirm('Are you sure you want to reject this property?')">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection