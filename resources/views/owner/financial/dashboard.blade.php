@extends('layouts.app')

@section('title', 'Financial Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ period: '{{ $period }}' }">
        <!-- Header with Period Toggle -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1
                        class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                        Financial Dashboard</h1>
                    <p class="text-gray-600">{{ $dashboardData['period'] }}</p>
                </div>
            </div>

            <!-- Period Toggle -->
            <div class="flex bg-gray-100 rounded-xl p-1 shadow-inner">
                <a href="{{ route('owner.financial.dashboard', ['period' => 'day']) }}"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $period === 'day' ? 'bg-white shadow-md text-green-600' : 'text-gray-600 hover:text-gray-900' }}">
                    Day
                </a>
                <a href="{{ route('owner.financial.dashboard', ['period' => 'week']) }}"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $period === 'week' ? 'bg-white shadow-md text-green-600' : 'text-gray-600 hover:text-gray-900' }}">
                    Week
                </a>
                <a href="{{ route('owner.financial.dashboard', ['period' => 'month']) }}"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $period === 'month' ? 'bg-white shadow-md text-green-600' : 'text-gray-600 hover:text-gray-900' }}">
                    Month
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-green-100 text-xs sm:text-sm font-medium">Total Revenue</p>
                    <div class="bg-white/20 rounded-full p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($dashboardData['totals']['income'], 0) }}</p>
                <p class="text-green-100 text-xs mt-2">{{ $dashboardData['totals']['booking_count'] }} bookings</p>
            </div>

            <!-- Total Expenses -->
            <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-red-100 text-xs sm:text-sm font-medium">Total Expenses</p>
                    <div class="bg-white/20 rounded-full p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($dashboardData['totals']['expenses'], 0) }}</p>
            </div>

            <!-- Net Profit -->
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-blue-100 text-xs sm:text-sm font-medium">Net Profit</p>
                    <div class="bg-white/20 rounded-full p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($dashboardData['totals']['profit'], 0) }}</p>
                <p class="text-blue-100 text-xs mt-2">{{ $dashboardData['totals']['margin'] }}% margin</p>
            </div>

            <!-- Outstanding -->
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
                <p class="text-2xl sm:text-3xl font-bold">₹{{ number_format($dashboardData['totals']['outstanding'], 0) }}
                </p>
            </div>
        </div>

        <!-- Property Performance Grid -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-4 sm:p-6 mb-8">
            <div class="flex items-center gap-2 mb-6">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <h3
                    class="text-lg sm:text-xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Property Performance</h3>
            </div>

            @if(count($dashboardData['properties']) > 0)
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full">
                        <thead class="hidden sm:table-header-group">
                            <tr class="border-b-2 border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Property</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Revenue</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Expenses</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Profit
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Margin</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($dashboardData['properties'] as $property)
                                <tr class="hover:bg-purple-50/50 transition-colors group">
                                    <!-- Mobile Card View -->
                                    <td colspan="6" class="sm:hidden p-4">
                                        <a href="{{ route('owner.financial.property', $property['uuid']) }}?period={{ $period }}"
                                            class="block">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-gray-900">{{ $property['name'] }}</p>
                                                        <p class="text-xs text-gray-500">{{ $property['accommodation_count'] }}
                                                            accommodations</p>
                                                    </div>
                                                </div>
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2 text-center">
                                                <div class="bg-green-50 rounded-lg p-2">
                                                    <p class="text-xs text-green-600 font-medium">Revenue</p>
                                                    <p class="text-sm font-bold text-green-700">
                                                        ₹{{ number_format($property['income'], 0) }}</p>
                                                </div>
                                                <div class="bg-red-50 rounded-lg p-2">
                                                    <p class="text-xs text-red-600 font-medium">Expenses</p>
                                                    <p class="text-sm font-bold text-red-700">
                                                        ₹{{ number_format($property['expenses'], 0) }}</p>
                                                </div>
                                                <div class="bg-blue-50 rounded-lg p-2">
                                                    <p class="text-xs text-blue-600 font-medium">Profit</p>
                                                    <p class="text-sm font-bold text-blue-700">
                                                        ₹{{ number_format($property['profit'], 0) }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </td>

                                    <!-- Desktop Table View -->
                                    <td class="hidden sm:table-cell px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $property['name'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $property['accommodation_count'] }}
                                                    accommodations • {{ $property['booking_count'] }} bookings</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-4 py-4 text-right">
                                        <p class="font-bold text-green-600">₹{{ number_format($property['income'], 0) }}</p>
                                        @if($property['income_change'] != 0)
                                            <p class="text-xs {{ $property['income_change'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $property['income_change'] > 0 ? '↑' : '↓' }} {{ abs($property['income_change']) }}%
                                            </p>
                                        @endif
                                    </td>
                                    <td class="hidden sm:table-cell px-4 py-4 text-right">
                                        <p class="font-bold text-red-600">₹{{ number_format($property['expenses'], 0) }}</p>
                                    </td>
                                    <td class="hidden sm:table-cell px-4 py-4 text-right">
                                        <p class="font-bold {{ $property['profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                            ₹{{ number_format($property['profit'], 0) }}</p>
                                        @if($property['profit_change'] != 0)
                                            <p class="text-xs {{ $property['profit_change'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $property['profit_change'] > 0 ? '↑' : '↓' }} {{ abs($property['profit_change']) }}%
                                            </p>
                                        @endif
                                    </td>
                                    <td class="hidden sm:table-cell px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $property['margin'] >= 50 ? 'bg-green-100 text-green-800' : ($property['margin'] >= 25 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $property['margin'] }}%
                                        </span>
                                    </td>
                                    <td class="hidden sm:table-cell px-4 py-4 text-center">
                                        <a href="{{ route('owner.financial.property', $property['uuid']) }}?period={{ $period }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-xs font-semibold rounded-lg hover:from-purple-600 hover:to-pink-700 transition-all shadow-sm">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">No Financial Data</h4>
                    <p class="text-gray-600 mb-4">Start by adding income or expense records for your properties.</p>
                    <div class="flex justify-center gap-3">
                        <a href="{{ route('owner.income.create') }}"
                            class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg font-semibold text-sm">Add
                            Income</a>
                        <a href="{{ route('owner.expense.create') }}"
                            class="px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg font-semibold text-sm">Add
                            Expense</a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Profit Trend -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Revenue Trend (6 Months)
                </h3>
                <canvas id="profitTrendChart" height="200"></canvas>
            </div>

            <!-- Income Breakdown -->
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
        </div>

        <!-- Quick Actions -->
        <div
            class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 p-4 sm:p-6 mb-20 lg:mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
                    </path>
                </svg>
                Quick Actions
            </h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('owner.income.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 font-semibold shadow-md text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Income
                </a>
                <a href="{{ route('owner.expense.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl hover:from-red-600 hover:to-rose-700 font-semibold shadow-md text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Expense
                </a>
                <a href="{{ route('owner.income.index') }}"
                    class="inline-flex items-center px-4 py-2 border-2 border-green-500 text-green-700 rounded-xl hover:bg-green-50 font-semibold text-sm">
                    View All Income
                </a>
                <a href="{{ route('owner.expense.index') }}"
                    class="inline-flex items-center px-4 py-2 border-2 border-red-500 text-red-700 rounded-xl hover:bg-red-50 font-semibold text-sm">
                    View All Expenses
                </a>
                <a href="{{ route('owner.reports.weekly') }}"
                    class="inline-flex items-center px-4 py-2 border-2 border-blue-500 text-blue-700 rounded-xl hover:bg-blue-50 font-semibold text-sm">
                    Reports
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Profit Trend Chart
                const profitTrendCtx = document.getElementById('profitTrendChart').getContext('2d');
                new Chart(profitTrendCtx, {
                    type: 'line',
                    data: @json($profitTrend),
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

                // Income Pie Chart
                const incomePieCtx = document.getElementById('incomePieChart').getContext('2d');
                new Chart(incomePieCtx, {
                    type: 'doughnut',
                    data: @json($incomePie),
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            });
        </script>
    @endpush
@endsection