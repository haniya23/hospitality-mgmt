@extends('layouts.app')

@section('title', 'Expense Details')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <a href="{{ route('owner.expense.index') }}"
                class="text-blue-600 hover:text-blue-800 flex items-center gap-2 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Expenses
            </a>
        </div>

        <!-- Main Card -->
        <div
            class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg border border-gray-200 p-4 sm:p-6 transition-shadow duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1
                            class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-transparent">
                            {{ $expense->title }}</h1>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('owner.expense.edit', $expense) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 font-semibold shadow-md">Edit</a>
                    <form method="POST" action="{{ route('owner.expense.destroy', $expense) }}"
                        onsubmit="return confirm('Delete?')">
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
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        Property
                    </p>
                    <p class="font-bold text-gray-900">{{ $expense->property->name }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        Category
                    </p>
                    <span class="px-3 py-1 text-sm font-bold rounded-full"
                        style="background-color: {{ $expense->category->color ?? '#6B7280' }}20; color: {{ $expense->category->color ?? '#6B7280' }}">
                        {{ $expense->category->name ?? 'Uncategorized' }}
                    </span>
                </div>
                <div class="p-4 bg-gradient-to-br from-red-50 to-rose-50 rounded-xl border border-red-200">
                    <p class="text-sm text-red-600 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        Amount
                    </p>
                    <p class="text-2xl font-bold text-red-600">₹{{ number_format($expense->amount, 2) }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Status
                    </p>
                    @if($expense->payment_status === 'paid')
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-green-100 text-green-800">Paid</span>
                    @else
                        <span
                            class="px-3 py-1 text-sm font-bold rounded-full bg-red-100 text-red-800">{{ ucfirst($expense->payment_status) }}</span>
                    @endif
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Date
                    </p>
                    <p class="font-bold text-gray-900">{{ $expense->transaction_date->format('F d, Y') }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        Payment Method
                    </p>
                    <p class="font-bold text-gray-900">{{ ucfirst($expense->payment_method) }}</p>
                </div>
                @if($expense->vendor_name)
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Vendor
                        </p>
                        <p class="font-bold text-gray-900">{{ $expense->vendor_name }}</p>
                    </div>
                @endif
                @if($expense->receipt_number)
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            Receipt #
                        </p>
                        <p class="font-bold text-gray-900">{{ $expense->receipt_number }}</p>
                    </div>
                @endif
            </div>

            @if($expense->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Notes
                    </p>
                    <p class="text-gray-700 bg-gray-50 p-4 rounded-xl">{{ $expense->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection