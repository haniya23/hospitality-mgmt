@extends('layouts.staff')

@section('title', 'Upcoming Bookings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Upcoming Bookings</h1>
            <p class="text-gray-600">Manage upcoming check-ins and check-outs</p>
        </div>
        <div class="flex space-x-3">
            @if(Auth::user()->hasPermission('create_bookings'))
            <a href="{{ route('staff.bookings.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Create Booking
            </a>
            @endif
            <div class="text-sm text-gray-500">
                <i class="fas fa-calendar-alt mr-1"></i>
                Next 30 days
            </div>
        </div>
    </div>

    <!-- Today's Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Check-ins -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Today's Check-ins</h3>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                    {{ $todaysCheckIns->count() }}
                </span>
            </div>
            
            @if($todaysCheckIns->count() > 0)
                <div class="space-y-3">
                    @foreach($todaysCheckIns as $booking)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-sign-in-alt text-blue-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">
                                                @if($booking->isB2bBooking())
                                                    {{ $booking->b2bPartner->getOrCreateReservedCustomer()->name }}
                                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full ml-2">B2B</span>
                                                @else
                                                    {{ $booking->guest->name }}
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-600">{{ $booking->accommodation->property->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $booking->accommodation->display_name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $booking->confirmation_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->adults }} adults, {{ $booking->children }} children</p>
                                    <a href="{{ route('staff.bookings.show', $booking) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-2">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar-check text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500">No check-ins scheduled for today</p>
                </div>
            @endif
        </div>

        <!-- Today's Check-outs -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Today's Check-outs</h3>
                <span class="bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                    {{ $todaysCheckOuts->count() }}
                </span>
            </div>
            
            @if($todaysCheckOuts->count() > 0)
                <div class="space-y-3">
                    @foreach($todaysCheckOuts as $booking)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-sign-out-alt text-red-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">
                                                @if($booking->isB2bBooking())
                                                    {{ $booking->b2bPartner->getOrCreateReservedCustomer()->name }}
                                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full ml-2">B2B</span>
                                                @else
                                                    {{ $booking->guest->name }}
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-600">{{ $booking->accommodation->property->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $booking->accommodation->display_name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $booking->confirmation_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->adults }} adults, {{ $booking->children }} children</p>
                                    <a href="{{ route('staff.bookings.show', $booking) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-2">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500">No check-outs scheduled for today</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Upcoming Bookings Calendar View -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Upcoming Bookings (Next 30 Days)</h3>
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                    Regular Booking
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                    B2B Booking
                </div>
            </div>
        </div>

        @if($bookingsByDate->count() > 0)
            <div class="space-y-4">
                @foreach($bookingsByDate as $date => $bookings)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($date)->format('l, M d, Y') }}
                            </h4>
                            <span class="bg-gray-100 text-gray-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                                {{ $bookings->count() }} {{ Str::plural('booking', $bookings->count()) }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($bookings as $booking)
                                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <div class="w-2 h-2 rounded-full {{ $booking->isB2bBooking() ? 'bg-purple-500' : 'bg-blue-500' }}"></div>
                                                <h5 class="font-medium text-gray-900 text-sm">
                                                    @if($booking->isB2bBooking())
                                                        {{ $booking->b2bPartner->getOrCreateReservedCustomer()->name }}
                                                    @else
                                                        {{ $booking->guest->name }}
                                                    @endif
                                                </h5>
                                            </div>
                                            <p class="text-xs text-gray-600 mb-1">{{ $booking->accommodation->property->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $booking->accommodation->display_name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d') }} - 
                                                {{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-medium text-gray-900">{{ $booking->confirmation_number }}</p>
                                            @if($booking->isB2bBooking())
                                                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">B2B</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-3 flex justify-end">
                                        <a href="{{ route('staff.bookings.show', $booking) }}" 
                                           class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No upcoming bookings</h3>
                <p class="text-gray-500">There are no bookings scheduled for the next 30 days.</p>
            </div>
        @endif
    </div>
</div>
@endsection
