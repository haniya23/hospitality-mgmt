@extends('layouts.app')

@section('title', 'Check-ins')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">🏨 Guest Check-ins</h1>
                        <p class="text-sm text-gray-600 mt-1">Manage guest check-in records</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Total Check-ins</div>
                            <div class="text-2xl font-bold text-green-600">{{ $checkIns->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Check-ins List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Check-ins</h2>
            </div>
            
            @if($checkIns->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($checkIns as $checkIn)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-sign-in-alt text-green-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $checkIn->guest_name }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ ucfirst($checkIn->status) }}
                                            </span>
                                        </div>
                                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-building mr-1"></i>{{ $checkIn->reservation->accommodation->property->name }}</span>
                                            <span><i class="fas fa-bed mr-1"></i>{{ $checkIn->reservation->accommodation->display_name }}</span>
                                            <span><i class="fas fa-calendar mr-1"></i>{{ $checkIn->check_in_time->format('M d, Y g:i A') }}</span>
                                        </div>
                                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-user mr-1"></i>{{ $checkIn->guest_contact }}</span>
                                            @if($checkIn->guest_email)
                                            <span><i class="fas fa-envelope mr-1"></i>{{ $checkIn->guest_email }}</span>
                                            @endif
                                            <span><i class="fas fa-user-tie mr-1"></i>Staff: {{ $checkIn->staff->name ?? 'Unknown' }}</span>
                                        </div>
                                        @if($checkIn->special_requests)
                                        <div class="mt-2">
                                            <span class="text-sm text-gray-600"><i class="fas fa-sticky-note mr-1"></i>{{ Str::limit($checkIn->special_requests, 100) }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">Booking Ref</div>
                                    <div class="font-mono text-sm font-semibold text-blue-600">{{ $checkIn->reservation->confirmation_number }}</div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('checkin.details', $checkIn->uuid) }}" 
                                       class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        View Details
                                    </a>
                                    @if($checkIn->reservation->status === 'checked_in')
                                    <a href="{{ route('checkout.show', $checkIn->reservation->uuid) }}" 
                                       class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors">
                                        Check-out
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $checkIns->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-sign-in-alt text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No check-ins yet</h3>
                    <p class="text-gray-500 mb-6">Guest check-ins will appear here once they start checking in.</p>
                    <a href="{{ route('bookings.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        View Bookings
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
