@extends('layouts.app')

@section('title', 'Monthly Reports')

@section('content')
    <div class="max-w-7xl mx-auto py-6">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Monthly Financial Reports</h1>
                <p class="text-gray-600">View and manage monthly financial summaries</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('owner.reports.weekly') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Weekly Reports
                </a>
                <a href="{{ route('owner.financial.dashboard') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Dashboard
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4">
                <select name="property_id" class="rounded-lg border-gray-300">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="rounded-lg border-gray-300">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Locked</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            </form>
        </div>

        <!-- Reports List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Report</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Income</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expenses</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $report->report_number }}</div>
                                    <div class="text-sm text-gray-500">{{ $report->period?->period_label ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $report->property?->name ?? 'All Properties' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                    ₹{{ number_format($report->total_income, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                    ₹{{ number_format($report->total_expenses, 2) }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $report->net_profit >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    ₹{{ number_format($report->net_profit, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($report->status === 'locked')
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Locked</span>
                                    @elseif($report->status === 'approved')
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Approved</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <a href="{{ route('owner.reports.view', $report) }}"
                                            class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="{{ route('owner.reports.pdf', $report) }}"
                                            class="text-gray-600 hover:text-gray-900">PDF</a>
                                        <a href="{{ route('owner.reports.excel', $report) }}"
                                            class="text-green-600 hover:text-green-900">Excel</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-lg font-medium">No monthly reports yet</p>
                                    <p class="mt-1">Monthly reports are automatically generated on the 1st of each month.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $reports->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection