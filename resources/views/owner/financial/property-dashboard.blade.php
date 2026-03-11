@extends('layouts.app')

@section('title', $property->name . ' - Financial Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('owner.financial.dashboard', ['period' => $period]) }}"
                class="text-blue-600 hover:text-blue-800 flex items-center gap-2 font-semibold mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Dashboard
            </a>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1
                            class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            {{ $property->name }}</h1>
                        <p class="text-gray-600">{{ $dates['label'] }}</p>
                    </div>
                </div>

                <!-- Period Toggle -->
                <div class="flex bg-gray-100 rounded-xl p-1 shadow-inner">
                    <a href="{{ route('owner.financial.property', $property) }}?period=day"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $period === 'day' ? 'bg-white shadow-md text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Day
                    </a>
                    <a href="{{ route('owner.financial.property', $property) }}?period=week"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $period === 'week' ? 'bg-white shadow-md text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Week
                    </a>
                    <a href="{{ route('owner.financial.property', $property) }}?period=month"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $period === 'month' ? 'bg-white shadow-md text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Month
                    </a>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <p class="text-green-100 text-xs sm:text-sm font-medium mb-1">Total Income</p>
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($metrics['total_income'], 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <p class="text-red-100 text-xs sm:text-sm font-medium mb-1">Total Expenses</p>
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($metrics['total_expenses'], 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <p class="text-blue-100 text-xs sm:text-sm font-medium mb-1">Net Profit</p>
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($metrics['net_profit'], 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <p class="text-purple-100 text-xs sm:text-sm font-medium mb-1">Profit Margin</p>
                <p class="text-2xl sm:text-3xl font-bold">
                    {{ $metrics['total_income'] > 0 ? round(($metrics['net_profit'] / $metrics['total_income']) * 100, 1) : 0 }}%
                </p>
            </div>
        </div>

        <!-- Accommodation Breakdown -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-4 sm:p-6 mb-8">
            <div class="flex items-center gap-2 mb-6">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                </div>
                <h3
                    class="text-lg sm:text-xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Accommodation Performance</h3>
            </div>

            @if(count($accommodationData['accommodations'] ?? []) > 0)
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full">
                        <thead class="hidden sm:table-header-group">
                            <tr class="border-b-2 border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Accommodation</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Income
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Expenses</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Net
                                    Contribution</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Share
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($accommodationData['accommodations'] as $acc)
                                <tr class="hover:bg-purple-50/50 transition-colors">
                                    <!-- Mobile View -->
                                    <td colspan="5" class="sm:hidden p-4">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                    </path>
                                                </svg>
                                            </div>
                                            <p class="font-bold text-gray-900">{{ $acc['name'] }}</p>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2 text-center">
                                            <div class="bg-green-50 rounded-lg p-2">
                                                <p class="text-xs text-green-600 font-medium">Income</p>
                                                <p class="text-sm font-bold text-green-700">₹{{ number_format($acc['income'], 0) }}
                                                </p>
                                            </div>
                                            <div class="bg-red-50 rounded-lg p-2">
                                                <p class="text-xs text-red-600 font-medium">Expenses</p>
                                                <p class="text-sm font-bold text-red-700">₹{{ number_format($acc['expenses'], 0) }}
                                                </p>
                                            </div>
                                            <div class="bg-blue-50 rounded-lg p-2">
                                                <p class="text-xs text-blue-600 font-medium">Net</p>
                                                <p class="text-sm font-bold text-blue-700">
                                                    ₹{{ number_format($acc['net_contribution'], 0) }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Desktop View -->
                                    <td class="hidden sm:table-cell px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                    </path>
                                                </svg>
                                            </div>
                                            <p class="font-bold text-gray-900">{{ $acc['name'] }}</p>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-4 py-4 text-right font-bold text-green-600">
                                        ₹{{ number_format($acc['income'], 0) }}</td>
                                    <td class="hidden sm:table-cell px-4 py-4 text-right font-bold text-red-600">
                                        ₹{{ number_format($acc['expenses'], 0) }}</td>
                                    <td
                                        class="hidden sm:table-cell px-4 py-4 text-right font-bold {{ $acc['net_contribution'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                        ₹{{ number_format($acc['net_contribution'], 0) }}</td>
                                    <td class="hidden sm:table-cell px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                            {{ $acc['contribution_pct'] ?? 0 }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>No accommodation data for this period</p>
                </div>
            @endif
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Income by Type -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    </svg>
                    Income by Type
                </h3>
                <canvas id="incomePieChart" height="200"></canvas>
            </div>

            <!-- Accommodation Comparison -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Accommodation Comparison
                </h3>
                <canvas id="accommodationBarChart" height="200"></canvas>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-20 lg:mb-8">
            <!-- Recent Income -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Recent Income
                </h3>
                @if($recentIncome->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentIncome as $income)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ ucfirst($income->income_type) }}</p>
                                    <p class="text-xs text-gray-500">{{ $income->transaction_date->format('M d, Y') }}</p>
                                </div>
                                <p class="font-bold text-green-600">₹{{ number_format($income->amount, 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No income records for this period</p>
                @endif
            </div>

            <!-- Recent Expenses -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Recent Expenses
                </h3>
                @if($recentExpenses->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentExpenses as $expense)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $expense->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $expense->transaction_date->format('M d, Y') }} •
                                        {{ $expense->category->name ?? 'Uncategorized' }}</p>
                                </div>
                                <p class="font-bold text-red-600">₹{{ number_format($expense->amount, 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No expense records for this period</p>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div
            class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-4 sm:p-6 mb-20 lg:mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('owner.income.create') }}?property_id={{ $property->id }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold shadow-md text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Income
                </a>
                <a href="{{ route('owner.expense.create') }}?property_id={{ $property->id }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl font-semibold shadow-md text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Expense
                </a>
                <a href="{{ route('owner.income.index') }}?property_id={{ $property->id }}"
                    class="inline-flex items-center px-4 py-2 border-2 border-green-500 text-green-700 rounded-xl font-semibold text-sm">
                    View All Income
                </a>
                <a href="{{ route('owner.expense.index') }}?property_id={{ $property->id }}"
                    class="inline-flex items-center px-4 py-2 border-2 border-red-500 text-red-700 rounded-xl font-semibold text-sm">
                    View All Expenses
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Income Pie Chart
                const incomePieCtx = document.getElementById('incomePieChart').getContext('2d');
                new Chart(incomePieCtx, {
                    type: 'doughnut',
                    data: @json($incomePie ?? ['labels' => [], 'datasets' => []]),
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { position: 'right' } }
                    }
                });

                // Accommodation Bar Chart
                const accommodationBarCtx = document.getElementById('accommodationBarChart').getContext('2d');
                new Chart(accommodationBarCtx, {
                    type: 'bar',
                    data: @json($accommodationBar ?? ['labels' => [], 'datasets' => []]),
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { position: 'top' } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: function (value) { return '₹' + value.toLocaleString(); } }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection