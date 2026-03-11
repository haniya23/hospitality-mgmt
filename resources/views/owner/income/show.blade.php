@extends('layouts.app')

@section('title', 'Income Details')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <a href="{{ route('owner.income.index') }}"
                class="text-blue-600 hover:text-blue-800 flex items-center gap-2 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Income List
            </a>
        </div>

        <!-- Main Card -->
        <div
            class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h1
                        class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                        Income Details</h1>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('owner.income.edit', $income) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 font-semibold shadow-md">Edit</a>
                    <form method="POST" action="{{ route('owner.income.destroy', $income) }}"
                        onsubmit="return confirm('Delete this income record?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl hover:from-red-600 hover:to-rose-700 font-semibold shadow-md">Delete</button>
                    </form>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        Property
                    </p>
                    <p class="font-bold text-gray-900">{{ $income->property->name }}</p>
                </div>
                @if($income->accommodation)
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            Accommodation
                        </p>
                        <p class="font-bold text-gray-900">{{ $income->accommodation->display_name }}</p>
                    </div>
                @endif
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        Type
                    </p>
                    <span
                        class="px-3 py-1 text-sm font-bold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($income->income_type) }}</span>
                </div>
                <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-200">
                    <p class="text-sm text-green-600 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        Amount
                    </p>
                    <p class="text-2xl font-bold text-green-600">₹{{ number_format($income->amount, 2) }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Payment Status
                    </p>
                    @if($income->payment_status === 'paid')
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-green-100 text-green-800">Paid</span>
                    @elseif($income->payment_status === 'partial')
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-yellow-100 text-yellow-800">Partial
                            (₹{{ number_format($income->paid_amount, 2) }} paid)</span>
                    @else
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-red-100 text-red-800">Unpaid</span>
                    @endif
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Transaction Date
                    </p>
                    <p class="font-bold text-gray-900">{{ $income->transaction_date->format('F d, Y') }}</p>
                </div>
                @if($income->reference_number)
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            Reference Number
                        </p>
                        <p class="font-bold text-gray-900">{{ $income->reference_number }}</p>
                    </div>
                @endif
                @if($income->b2bPartner)
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            B2B Partner
                        </p>
                        <p class="font-bold text-gray-900">{{ $income->b2bPartner->partner_name }}</p>
                    </div>
                @endif
            </div>

            @if($income->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Notes
                    </p>
                    <p class="text-gray-700 bg-gray-50 p-4 rounded-xl">{{ $income->notes }}</p>
                </div>
            @endif

            @if($income->adjustments->count() > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Adjustment History
                    </h3>
                    <div class="space-y-3">
                        @foreach($income->adjustments as $adj)
                            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-4 rounded-xl border border-purple-200">
                                <div class="flex justify-between">
                                    <span class="text-sm font-bold text-purple-800">{{ ucfirst($adj->adjustment_type) }}</span>
                                    <span class="text-sm text-gray-500">{{ $adj->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $adj->reason }}</p>
                                <p class="text-sm text-gray-500">By: {{ $adj->adjustedBy->name ?? 'System' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection