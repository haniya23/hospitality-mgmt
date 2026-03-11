@extends('layouts.app')

@section('title', 'Finance Details - ' . $bookingFinance->finance_number)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="financeDetailApp()">
        <!-- Back Button & Header -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('owner.booking-finance.index') }}"
                class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $bookingFinance->finance_number }}</h1>
                <p class="text-gray-600">Finance Details</p>
            </div>
        </div>

        <!-- Main Info Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
            <!-- Status Header -->
            <div
                class="px-6 py-4 {{ $bookingFinance->payment_status === 'paid' ? 'bg-gradient-to-r from-green-500 to-emerald-600' : ($bookingFinance->payment_status === 'partial' ? 'bg-gradient-to-r from-yellow-500 to-amber-600' : 'bg-gradient-to-r from-red-500 to-rose-600') }}">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <p class="text-sm font-medium opacity-90">Payment Status</p>
                        <p class="text-2xl font-bold">{{ $bookingFinance->payment_status_label }}</p>
                    </div>
                    <div class="text-right text-white">
                        <p class="text-sm font-medium opacity-90">Booking Status</p>
                        <p class="text-lg font-semibold">{{ $bookingFinance->booking_status_label }}</p>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Guest & Booking -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Booking Details</h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Guest Name</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $bookingFinance->reservation?->guest?->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Confirmation Number</p>
                                    <p class="font-mono font-semibold text-gray-900">
                                        {{ $bookingFinance->reservation?->confirmation_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Property / Room</p>
                                    <p class="font-semibold text-gray-900">{{ $bookingFinance->property?->name ?? 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $bookingFinance->accommodation?->custom_name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Stay Period</h3>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Check-in</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $bookingFinance->check_in_date->format('M d') }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $bookingFinance->check_in_date->format('Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Check-out</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $bookingFinance->check_out_date->format('M d') }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $bookingFinance->check_out_date->format('Y') }}</p>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 mt-4 pt-4 text-center">
                                <p class="text-xs text-gray-500">Duration</p>
                                <p class="text-lg font-bold text-indigo-600">
                                    {{ $bookingFinance->check_in_date->diffInDays($bookingFinance->check_out_date) }}
                                    Nights</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Breakdown -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Financial Breakdown</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Base Amount</span>
                        <span class="font-semibold text-gray-900">₹{{ number_format($bookingFinance->total_amount, 2) }}</span>
                    </div>
                    @if ($bookingFinance->additional_charges > 0)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Additional Charges</span>
                            <span class="font-semibold text-amber-600">+₹{{ number_format($bookingFinance->additional_charges, 2) }}</span>
                        </div>
                    @endif
                    @if ($bookingFinance->refund_amount > 0)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Refunds</span>
                            <span class="font-semibold text-red-600">-₹{{ number_format($bookingFinance->refund_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center py-3 bg-gray-50 rounded-lg px-4 -mx-4">
                        <span class="font-bold text-gray-900">Final Amount</span>
                        <span class="text-xl font-bold text-gray-900">₹{{ number_format($bookingFinance->final_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Amount Received</span>
                        <span class="font-semibold text-green-600">₹{{ number_format($bookingFinance->advance_received, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 {{ $bookingFinance->balance_pending > 0 ? 'bg-red-50' : 'bg-green-50' }} rounded-lg px-4 -mx-4">
                        <span class="font-bold {{ $bookingFinance->balance_pending > 0 ? 'text-red-900' : 'text-green-900' }}">Balance Pending</span>
                        <span class="text-xl font-bold {{ $bookingFinance->balance_pending > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₹{{ number_format($bookingFinance->balance_pending, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if ($bookingFinance->notes)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Notes & History</h3>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <pre class="text-sm text-gray-700 whitespace-pre-wrap font-sans">{{ $bookingFinance->notes }}</pre>
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-3">
                @if ($bookingFinance->balance_pending > 0)
                    <button @click="showPaymentModal = true"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold shadow-md hover:from-green-600 hover:to-emerald-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Record Payment
                    </button>
                @endif
                <button @click="showChargeModal = true"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-semibold shadow-md hover:from-amber-600 hover:to-orange-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Charge
                </button>
                @if ($bookingFinance->advance_received > 0)
                    <button @click="showRefundModal = true"
                        class="inline-flex items-center px-4 py-2 border-2 border-red-500 text-red-700 rounded-xl font-semibold hover:bg-red-50">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Record Refund
                    </button>
                @endif
            </div>
        </div>

        <!-- Payment Modal -->
        <template x-if="showPaymentModal">
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/50" @click="showPaymentModal = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Record Payment</h3>
                        <form @submit.prevent="submitPayment">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (max: ₹{{ number_format($bookingFinance->balance_pending, 2) }})</label>
                                <input type="number" x-model="paymentAmount" step="0.01" min="0.01" max="{{ $bookingFinance->balance_pending }}"
                                    class="w-full rounded-lg border-gray-300" required>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea x-model="paymentNotes" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="showPaymentModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium">Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- Charge Modal -->
        <template x-if="showChargeModal">
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/50" @click="showChargeModal = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Additional Charge</h3>
                        <form @submit.prevent="submitCharge">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                <input type="number" x-model="chargeAmount" step="0.01" min="0.01"
                                    class="w-full rounded-lg border-gray-300" required>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <input type="text" x-model="chargeReason" class="w-full rounded-lg border-gray-300" placeholder="e.g., Late checkout, Room service">
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="showChargeModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                                <button type="submit" class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg font-medium">Add Charge</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- Refund Modal -->
        <template x-if="showRefundModal">
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/50" @click="showRefundModal = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Record Refund</h3>
                        <form @submit.prevent="submitRefund">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (max: ₹{{ number_format($bookingFinance->advance_received, 2) }})</label>
                                <input type="number" x-model="refundAmount" step="0.01" min="0.01" max="{{ $bookingFinance->advance_received }}"
                                    class="w-full rounded-lg border-gray-300" required>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <input type="text" x-model="refundReason" class="w-full rounded-lg border-gray-300" placeholder="e.g., Early checkout, Cancellation">
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="showRefundModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium">Record Refund</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script>
            function financeDetailApp() {
                return {
                    showPaymentModal: false,
                    showChargeModal: false,
                    showRefundModal: false,
                    paymentAmount: {{ $bookingFinance->balance_pending }},
                    paymentNotes: '',
                    chargeAmount: 0,
                    chargeReason: '',
                    refundAmount: 0,
                    refundReason: '',

                    async submitPayment() {
                        const response = await fetch(`{{ route('owner.booking-finance.payment', $bookingFinance) }}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ amount: this.paymentAmount, notes: this.paymentNotes }),
                        });
                        const data = await response.json();
                        if (data.success) window.location.reload();
                        else alert(data.message);
                    },

                    async submitCharge() {
                        const response = await fetch(`{{ route('owner.booking-finance.charge', $bookingFinance) }}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ amount: this.chargeAmount, reason: this.chargeReason }),
                        });
                        const data = await response.json();
                        if (data.success) window.location.reload();
                        else alert(data.message);
                    },

                    async submitRefund() {
                        const response = await fetch(`{{ route('owner.booking-finance.refund', $bookingFinance) }}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ amount: this.refundAmount, reason: this.refundReason }),
                        });
                        const data = await response.json();
                        if (data.success) window.location.reload();
                        else alert(data.message);
                    },
                };
            }
        </script>
    @endpush
@endsection
