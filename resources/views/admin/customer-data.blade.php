@extends('layouts.mobile')

@section('title', 'Customer Data - Admin')
@section('page-title', 'Customer Data')

@section('content')

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-xl font-bold text-gray-900">Customer Data</h2>
            <p class="text-sm text-gray-600">{{ $customers->total() }} total customers</p>
        </div>

        <!-- Customers List -->
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            @if($customers->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p>No customers found.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($customers as $customer)
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $customer->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $customer->mobile_number }}</p>
                                        @if($customer->email)
                                            <p class="text-sm text-gray-600">{{ $customer->email }}</p>
                                        @endif
                                        @if($customer->address)
                                            <p class="text-sm text-gray-500">{{ $customer->address }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                            {{ $customer->reservations_count }} bookings
                                        </span>
                                    </div>
                                </div>
                                
                                @if($customer->reservations->isNotEmpty())
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <h5 class="text-sm font-medium text-gray-900 mb-2">Recent Bookings:</h5>
                                        <div class="space-y-1">
                                            @foreach($customer->reservations->take(3) as $reservation)
                                                <div class="flex justify-between text-xs text-gray-600">
                                                    <span>{{ $reservation->property->name ?? 'Property' }}</span>
                                                    <span>{{ $reservation->check_in_date->format('M d, Y') }}</span>
                                                </div>
                                            @endforeach
                                            @if($customer->reservations->count() > 3)
                                                <p class="text-xs text-gray-500">+ {{ $customer->reservations->count() - 3 }} more bookings</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <p class="text-xs text-gray-500">
                                    Customer since: {{ $customer->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="flex justify-center">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection