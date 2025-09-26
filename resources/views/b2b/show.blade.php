@extends('layouts.app')

@section('title', 'B2B Partner Details')

@section('header')
    @include('partials.b2b.header')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Partner Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $b2b->partner_name }}</h1>
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        {{ $b2b->status === 'active' ? 'bg-green-100 text-green-800' : 
                           ($b2b->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($b2b->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($b2b->status) }}
                    </span>
                </div>
                <p class="text-gray-600">{{ $b2b->partner_type }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('b2b.edit', $b2b) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">
                    Edit Partner
                </a>
                <form action="{{ route('b2b.destroy', $b2b) }}" method="POST" class="inline" 
                      onsubmit="return confirm('Are you sure you want to delete this partner? This will also delete the reserved customer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Delete Partner
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Partner Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Contact Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
            <div class="space-y-3">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-user text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Contact Person</p>
                        <p class="font-medium text-gray-900">{{ $b2b->contactUser->name }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-phone text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-medium text-gray-900">{{ $b2b->phone }}</p>
                    </div>
                </div>
                @if($b2b->email)
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-envelope text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium text-gray-900">{{ $b2b->email }}</p>
                    </div>
                </div>
                @endif
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-calendar text-gray-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Created</p>
                        <p class="font-medium text-gray-900">{{ $b2b->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Commission Settings</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">Commission Rate</span>
                    <span class="font-semibold text-gray-900">{{ $b2b->commission_rate }}%</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">Default Discount</span>
                    <span class="font-semibold text-gray-900">{{ $b2b->default_discount_pct }}%</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">Total Bookings</span>
                    <span class="font-semibold text-gray-900">{{ $b2b->reservations_count }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Reserved Customer Information -->
    @if($b2b->reservedCustomer)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reserved Customer</h3>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-green-800">Automatically Created Reserved Customer</h4>
                    <div class="mt-2 text-sm text-green-700">
                        <p><strong>Name:</strong> {{ $b2b->reservedCustomer->name }}</p>
                        <p><strong>Email:</strong> {{ $b2b->reservedCustomer->email }}</p>
                        <p><strong>Mobile:</strong> {{ $b2b->reservedCustomer->mobile_number }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Bookings -->
    @if($b2b->reservations->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Bookings</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($b2b->reservations->take(5) as $reservation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $reservation->guest->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $reservation->check_in_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $reservation->check_out_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ₹{{ number_format($reservation->total_amount) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                   ($reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($b2b->reservations->count() > 5)
        <div class="mt-4 text-center">
            <a href="{{ route('bookings.index') }}?b2b_partner_id={{ $b2b->uuid }}" 
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                View All Bookings ({{ $b2b->reservations->count() }})
            </a>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
