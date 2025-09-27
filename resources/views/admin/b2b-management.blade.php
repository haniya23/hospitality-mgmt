@extends('layouts.app')

@section('title', 'B2B Management - Admin')
@section('page-title', 'B2B Management')

@section('content')

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-xl font-bold text-gray-900">B2B Partner Management</h2>
            <p class="text-sm text-gray-600">{{ $b2bPartners->total() }} total B2B partners</p>
        </div>

        <!-- B2B Partners List -->
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            @if($b2bPartners->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V8m8 0V6a2 2 0 00-2-2H10a2 2 0 00-2 2v2"></path>
                    </svg>
                    <p>No B2B partners found.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($b2bPartners as $partner)
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $partner->company_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $partner->business_type }}</p>
                                        @if($partner->contactUser)
                                            <p class="text-sm text-gray-600">
                                                Contact: {{ $partner->contactUser->name }} • {{ $partner->contactUser->mobile_number }}
                                            </p>
                                        @endif
                                        @if($partner->email)
                                            <p class="text-sm text-gray-600">{{ $partner->email }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($partner->status === 'active') bg-green-100 text-green-800
                                            @elseif($partner->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($partner->status) }}
                                        </span>
                                        <div class="mt-1">
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                {{ $partner->requests_count }} requests
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($partner->description)
                                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $partner->description }}</p>
                                @endif
                                
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    @if($partner->commission_rate)
                                        <div>
                                            <span class="text-gray-500">Commission Rate:</span>
                                            <span class="font-medium">{{ $partner->commission_rate }}%</span>
                                        </div>
                                    @endif
                                    @if($partner->website)
                                        <div>
                                            <span class="text-gray-500">Website:</span>
                                            <a href="{{ $partner->website }}" target="_blank" class="text-blue-600 hover:underline">
                                                {{ $partner->website }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($partner->requests->isNotEmpty())
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <h5 class="text-sm font-medium text-gray-900 mb-2">Recent Requests:</h5>
                                        <div class="space-y-1">
                                            @foreach($partner->requests->take(3) as $request)
                                                <div class="flex justify-between text-xs text-gray-600">
                                                    <span>{{ $request->property->name ?? 'Property Request' }}</span>
                                                    <span class="px-1 py-0.5 rounded text-xs
                                                        @if($request->status === 'approved') bg-green-100 text-green-800
                                                        @elseif($request->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst($request->status) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                            @if($partner->requests->count() > 3)
                                                <p class="text-xs text-gray-500">+ {{ $partner->requests->count() - 3 }} more requests</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <p class="text-xs text-gray-500">
                                    Partner since: {{ $partner->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($b2bPartners->hasPages())
            <div class="flex justify-center">
                {{ $b2bPartners->links() }}
            </div>
        @endif
    </div>
@endsection