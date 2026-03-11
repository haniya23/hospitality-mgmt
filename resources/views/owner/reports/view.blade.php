@extends('layouts.app')

@section('title', 'Report Details')

@section('content')
    <div class="max-w-5xl mx-auto py-6">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ $report->report_type === 'weekly' ? route('owner.reports.weekly') : route('owner.reports.monthly') }}"
                class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Reports
            </a>
            <div class="flex gap-2">
                <a href="{{ route('owner.reports.pdf', $report) }}"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('owner.reports.excel', $report) }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Excel
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $report->report_title }}</h1>
                    <p class="text-gray-600">{{ $report->report_number }}</p>
                </div>
                <div class="text-right">
                    @if($report->status === 'locked')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">Locked</span>
                    @elseif($report->status === 'approved')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">Approved</span>
                    @else
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                    @endif
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-green-50 rounded-xl p-4">
                    <p class="text-sm text-green-600">Total Income</p>
                    <p class="text-2xl font-bold text-green-700">₹{{ number_format($report->total_income, 2) }}</p>
                </div>
                <div class="bg-red-50 rounded-xl p-4">
                    <p class="text-sm text-red-600">Total Expenses</p>
                    <p class="text-2xl font-bold text-red-700">₹{{ number_format($report->total_expenses, 2) }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-sm text-blue-600">Net Profit</p>
                    <p class="text-2xl font-bold {{ $report->net_profit >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                        ₹{{ number_format($report->net_profit, 2) }}</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-4">
                    <p class="text-sm text-amber-600">Outstanding</p>
                    <p class="text-2xl font-bold text-amber-700">₹{{ number_format($report->outstanding_receivables, 2) }}
                    </p>
                </div>
            </div>

            <!-- Breakdown Tables -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Income Breakdown</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($report->items->where('item_type', 'income') as $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ ucfirst($item->category) }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-green-600">
                                        ₹{{ number_format($item->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-gray-500">{{ $item->transaction_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">No income data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Expense Breakdown</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($report->items->where('item_type', 'expense') as $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $item->category }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-red-600">₹{{ number_format($item->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-right text-gray-500">{{ $item->transaction_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">No expense data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            @if(!$report->is_locked)
                <div class="mt-8 pt-6 border-t flex justify-end gap-4">
                    @if(!$report->is_approved)
                        <form method="POST" action="{{ route('owner.reports.approve', $report) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Approve
                                Report</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('owner.reports.lock', $report) }}"
                            onsubmit="return confirm('Lock this report? This cannot be undone.')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Lock
                                Report</button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <div class="text-sm text-gray-500 text-center">
            Generated on {{ $report->created_at->format('F d, Y \a\t H:i') }}
            @if($report->generatedBy) by {{ $report->generatedBy->name }} @endif
        </div>
    </div>
@endsection