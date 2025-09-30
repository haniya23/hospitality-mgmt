@extends('layouts.app')

@section('title', 'Invoice Preview - ' . $booking->confirmation_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Invoice Preview</h1>
                    <p class="text-gray-600 mt-1">Confirmation: {{ $booking->confirmation_number }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('bookings.invoice.download', $booking) }}" 
                       class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition flex items-center space-x-2">
                        <i class="fas fa-download"></i>
                        <span>Download PDF</span>
                    </a>
                    <a href="{{ route('bookings.index') }}" 
                       class="bg-gray-500 text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-600 transition flex items-center space-x-2">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Bookings</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Invoice Preview -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Invoice Preview</h2>
                <p class="text-sm text-gray-600">This is how your invoice will appear when downloaded</p>
            </div>
            
            <!-- Invoice Content -->
            <div class="p-6">
                <div class="invoice-preview-container" style="transform: scale(0.8); transform-origin: top left; width: 125%;">
                    @include('invoices.booking-invoice-content')
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('bookings.invoice.download', $booking) }}" 
               class="bg-green-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-green-700 transition flex items-center space-x-2">
                <i class="fas fa-download"></i>
                <span>Download Invoice PDF</span>
            </a>
            <button onclick="window.print()" 
                    class="bg-purple-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-purple-700 transition flex items-center space-x-2">
                <i class="fas fa-print"></i>
                <span>Print Invoice</span>
            </button>
        </div>

        <!-- Information Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Invoice Information</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Invoice will be downloaded as a PDF file</li>
                            <li>PDF is optimized for A4 paper size</li>
                            <li>All amounts are displayed in Indian Rupees (₹)</li>
                            <li>Invoice contains all booking and payment details</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .invoice-preview-container {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    @media print {
        .invoice-preview-container {
            transform: scale(1) !important;
            width: 100% !important;
        }
        
        .bg-gray-50,
        .bg-white,
        .bg-blue-50 {
            background: white !important;
        }
        
        .text-gray-900,
        .text-gray-600,
        .text-blue-800,
        .text-blue-700 {
            color: black !important;
        }
        
        .border-gray-200,
        .border-blue-200 {
            border-color: #ccc !important;
        }
    }
</style>
@endpush
@endsection
