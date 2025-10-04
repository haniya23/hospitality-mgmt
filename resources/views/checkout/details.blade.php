@extends('layouts.app')

@section('title', 'Check-out Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">🚪 Check-out Details</h1>
                        <p class="text-sm text-gray-600 mt-1">Complete check-out information</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Check-out ID</div>
                        <div class="font-mono text-lg font-semibold text-blue-600">{{ $checkOut->uuid }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guest Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Guest Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Guest Details</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Name</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->guest_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Contact</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->reservation->guest->mobile_number }}</dd>
                            </div>
                            @if($checkOut->room_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Room Number</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->room_number }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Stay Summary</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Property</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->reservation->accommodation->property->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Room Type</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->reservation->accommodation->display_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Booking Reference</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $checkOut->reservation->confirmation_number }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stay Timeline -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Stay Timeline</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Check-in</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Check-in Time</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->checkIn->check_in_time->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Expected Check-out</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->checkIn->expected_check_out_date->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Check-out</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Check-out Time</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->check_out_time->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Duration</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->checkIn->check_in_time->diffInDays($checkOut->check_out_time) }} days</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Used -->
        @if($checkOut->services_used && count($checkOut->services_used) > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Services Used</h2>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-2">
                    @foreach($checkOut->services_used as $service)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-concierge-bell mr-2"></i>
                        {{ ucfirst(str_replace('_', ' ', $service)) }}
                    </span>
                    @endforeach
                </div>
                @if($checkOut->service_notes)
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Service Notes</h3>
                    <p class="text-sm text-gray-900">{{ $checkOut->service_notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Final Settlement -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Final Settlement</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Payment Details</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Final Bill</dt>
                                <dd class="text-sm text-gray-900 font-semibold">₹{{ number_format($checkOut->final_bill, 2) }}</dd>
                            </div>
                            @if($checkOut->late_checkout_charges > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Late Check-out Charges</dt>
                                <dd class="text-sm text-gray-900">₹{{ number_format($checkOut->late_checkout_charges, 2) }}</dd>
                            </div>
                            @endif
                            @if($checkOut->deposit_refund > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Deposit Refund</dt>
                                <dd class="text-sm text-gray-900">₹{{ number_format($checkOut->deposit_refund, 2) }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Net Amount</dt>
                                <dd class="text-sm text-gray-900 font-semibold text-lg">₹{{ number_format($checkOut->net_amount, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Payment Status</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Status</dt>
                                <dd class="text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($checkOut->payment_status === 'completed') bg-green-100 text-green-800
                                        @elseif($checkOut->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($checkOut->payment_status === 'partial') bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($checkOut->payment_status) }}
                                    </span>
                                </dd>
                            </div>
                            @if($checkOut->payment_notes)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Payment Notes</dt>
                                <dd class="text-sm text-gray-900">{{ $checkOut->payment_notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guest Feedback -->
        @if($checkOut->rating || $checkOut->feedback_comments)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Guest Feedback</h2>
            </div>
            <div class="p-6">
                @if($checkOut->rating)
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Rating</h3>
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-6 h-6 {{ $i <= $checkOut->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                        <span class="ml-2 text-sm text-gray-600">{{ $checkOut->rating }}/5 stars</span>
                    </div>
                </div>
                @endif
                
                @if($checkOut->feedback_comments)
                <div>
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Comments</h3>
                    <p class="text-sm text-gray-900">{{ $checkOut->feedback_comments }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Room Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Room Status</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 {{ $checkOut->room_marked_clean ? 'bg-green-100' : 'bg-yellow-100' }} rounded-full flex items-center justify-center">
                                <i class="fas {{ $checkOut->room_marked_clean ? 'fa-check' : 'fa-clock' }} {{ $checkOut->room_marked_clean ? 'text-green-600' : 'text-yellow-600' }}"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                @if($checkOut->room_marked_clean)
                                    Room Cleaned & Ready
                                @else
                                    Room Needs Cleaning
                                @endif
                            </h3>
                            <p class="text-sm text-gray-600">
                                @if($checkOut->room_marked_clean)
                                    Room has been marked as clean and ready for the next guest
                                @else
                                    Room cleaning is pending
                                @endif
                            </p>
                        </div>
                    </div>
                    @if(!$checkOut->room_marked_clean)
                    <form action="{{ route('checkout.mark-clean', $checkOut->uuid) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Mark Room Clean
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Staff Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Staff Information</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-blue-600"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $checkOut->staff->name ?? 'Unknown Staff' }}</h3>
                        <p class="text-sm text-gray-600">Processed check-out on {{ $checkOut->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('checkout.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Back to Check-outs
            </a>
            <a href="{{ route('bookings.show', $checkOut->reservation->uuid) }}" 
               class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                View Booking Details
            </a>
        </div>
    </div>
</div>
@endsection
