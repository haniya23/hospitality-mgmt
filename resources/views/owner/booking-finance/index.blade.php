@extends('layouts.app')

@section('title', 'Booking Finances')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="bookingFinanceApp()">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1
                        class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Booking Finances</h1>
                    <p class="text-gray-600">All-in-one financial tracking for bookings</p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('owner.booking-finance.export', request()->query()) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Total Bookings -->
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-blue-100 text-xs sm:text-sm font-medium">Total Bookings</p>
                    <div class="bg-white/20 rounded-full p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold">{{ number_format($summary['total_bookings']) }}</p>
                <p class="text-blue-100 text-xs mt-2">{{ $summary['paid_count'] }} paid, {{ $summary['pending_count'] }}
                    pending</p>
            </div>

            <!-- Total Amount -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-green-100 text-xs sm:text-sm font-medium">Total Amount</p>
                    <div class="bg-white/20 rounded-full p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($summary['total_amount'], 0) }}</p>
            </div>

            <!-- Total Received -->
            <div class="bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-teal-100 text-xs sm:text-sm font-medium">Total Received</p>
                    <div class="bg-white/20 rounded-full p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($summary['total_received'], 0) }}</p>
                <p class="text-teal-100 text-xs mt-2">₹{{ number_format($summary['today_collections'], 0) }} today</p>
            </div>

            <!-- Total Pending -->
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-amber-100 text-xs sm:text-sm font-medium">Outstanding</p>
                    <div class="bg-white/20 rounded-full p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($summary['total_pending'], 0) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('owner.booking-finance.index') }}" class="flex flex-wrap gap-4">
                <!-- Property Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                    <select name="property_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Properties</option>
                        @foreach ($properties as $property)
                            <option value="{{ $property->id }}"
                                {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Status Filter -->
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                    <select name="payment_status"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid
                        </option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial
                        </option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                <!-- Booking Status Filter -->
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Booking Status</label>
                    <select name="booking_status"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('booking_status') == 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="confirmed" {{ request('booking_status') == 'confirmed' ? 'selected' : '' }}>
                            Confirmed</option>
                        <option value="checked_in" {{ request('booking_status') == 'checked_in' ? 'selected' : '' }}>
                            Checked In</option>
                        <option value="checked_out" {{ request('booking_status') == 'checked_out' ? 'selected' : '' }}>
                            Checked Out</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Filter Button -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-medium shadow-sm hover:from-indigo-600 hover:to-purple-700">
                        Apply Filter
                    </button>
                    <a href="{{ route('owner.booking-finance.index') }}"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Finances Table -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-8">
            @if ($finances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Finance #</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Guest / Booking</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Property</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Received</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Pending</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($finances as $finance)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-4 py-4">
                                        <p class="font-mono text-sm font-bold text-indigo-600">
                                            {{ $finance->finance_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $finance->booking_date->format('M d, Y') }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-gray-900">
                                            {{ $finance->reservation?->guest?->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $finance->reservation?->confirmation_number ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="font-medium text-gray-900">{{ $finance->property?->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $finance->accommodation?->custom_name ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <p class="font-bold text-gray-900">
                                            ₹{{ number_format($finance->final_amount, 0) }}</p>
                                        @if ($finance->additional_charges > 0)
                                            <p class="text-xs text-amber-600">+₹{{ number_format($finance->additional_charges, 0) }} charges</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <p class="font-bold text-green-600">
                                            ₹{{ number_format($finance->advance_received, 0) }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <p
                                            class="font-bold {{ $finance->balance_pending > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                            ₹{{ number_format($finance->balance_pending, 0) }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                            @if ($finance->payment_status === 'paid') bg-green-100 text-green-800
                                            @elseif($finance->payment_status === 'partial') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $finance->payment_status_label }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">{{ $finance->booking_status_label }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            @if ($finance->balance_pending > 0)
                                                <button @click="openPaymentModal('{{ $finance->uuid }}', {{ $finance->balance_pending }})"
                                                    class="px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 shadow-sm">
                                                    Record Payment
                                                </button>
                                            @endif
                                            <a href="{{ route('owner.booking-finance.show', $finance) }}"
                                                class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200">
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $finances->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">No Booking Finances</h4>
                    <p class="text-gray-600">Finance records will appear here once bookings are created.</p>
                </div>
            @endif
        </div>

        <!-- Payment Modal -->
        <div x-show="showPaymentModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900/50 transition-opacity" @click="showPaymentModal = false"></div>
                <div
                    class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Record Payment</h3>
                        <form @submit.prevent="submitPayment">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₹</span>
                                    <input type="number" x-model="paymentAmount" step="0.01" min="0.01"
                                        :max="maxPayment"
                                        class="w-full pl-8 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Enter amount" required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Max: ₹<span x-text="maxPayment.toLocaleString()"></span></p>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                                <textarea x-model="paymentNotes" rows="2"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Payment notes..."></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="showPaymentModal = false"
                                    class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200">
                                    Cancel
                                </button>
                                <button type="submit" :disabled="isSubmitting"
                                    class="flex-1 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg font-medium shadow-sm hover:from-green-600 hover:to-emerald-700 disabled:opacity-50">
                                    <span x-show="!isSubmitting">Record Payment</span>
                                    <span x-show="isSubmitting">Processing...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function bookingFinanceApp() {
                return {
                    showPaymentModal: false,
                    selectedFinanceId: null,
                    maxPayment: 0,
                    paymentAmount: 0,
                    paymentNotes: '',
                    isSubmitting: false,

                    openPaymentModal(financeId, balance) {
                        this.selectedFinanceId = financeId;
                        this.maxPayment = balance;
                        this.paymentAmount = balance;
                        this.paymentNotes = '';
                        this.showPaymentModal = true;
                    },

                    async submitPayment() {
                        this.isSubmitting = true;
                        try {
                            const response = await fetch(`/owner/booking-finance/${this.selectedFinanceId}/payment`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    amount: this.paymentAmount,
                                    notes: this.paymentNotes,
                                }),
                            });
                            const data = await response.json();
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Failed to record payment');
                            }
                        } catch (error) {
                            alert('An error occurred. Please try again.');
                        } finally {
                            this.isSubmitting = false;
                        }
                    },
                };
            }
        </script>
    @endpush
@endsection
