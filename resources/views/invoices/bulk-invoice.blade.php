<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Invoice Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            color: #000;
            background: #fff;
            font-size: 12px;
        }
        
        .invoice-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            background: #fff;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }
        
        .company-info h1 {
            color: #000;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .company-info p {
            color: #000;
            font-size: 11px;
            margin: 1px 0;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info h2 {
            color: #000;
            font-size: 18px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .invoice-info p {
            color: #000;
            font-size: 11px;
            margin: 1px 0;
        }
        
        .summary-section {
            background: #fff;
            padding: 15px;
            border: 1px solid #000;
            margin-bottom: 20px;
        }
        
        .summary-section h3 {
            color: #000;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
            border: 1px solid #000;
        }
        
        .summary-item .label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
        }
        
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .bookings-table th,
        .bookings-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        .bookings-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .bookings-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #000;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fff;
            color: #000;
        }
        
        .total-section {
            background: #000;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .total-section h3 {
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #000;
            text-align: center;
            color: #000;
            font-size: 10px;
        }
        
        .footer p {
            margin: 3px 0;
        }
        
        @media print {
            body {
                font-size: 11px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .invoice-container {
                width: 100%;
                min-height: 100%;
                padding: 10mm;
                margin: 0;
            }
            
            .header {
                page-break-inside: avoid;
            }
            
            .summary-section {
                page-break-inside: avoid;
            }
            
            .bookings-table {
                page-break-inside: auto;
            }
            
            .bookings-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .total-section {
                page-break-inside: avoid;
            }
            
            .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ $property_owner->name ?? 'Hospitality Management' }}</h1>
                <p>Bulk Invoice Report</p>
                <p>Generated on: {{ $generated_date }}</p>
            </div>
            <div class="invoice-info">
                <h2>BULK INVOICE</h2>
                <p><strong>Report No:</strong> {{ $invoice_number }}</p>
                <p><strong>Total Bookings:</strong> {{ $total_bookings }}</p>
                <p><strong>Date Range:</strong> All Time</p>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <h3>Summary</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="label">Total Bookings</div>
                    <div class="value">{{ $total_bookings }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Total Amount</div>
                    <div class="value">₹{{ number_format($total_amount, 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Advance Paid</div>
                    <div class="value">₹{{ number_format($total_advance_paid, 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Balance Pending</div>
                    <div class="value">₹{{ number_format($total_balance_pending, 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Pending Bookings</div>
                    <div class="value">{{ $status_counts['pending'] }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Confirmed Bookings</div>
                    <div class="value">{{ $status_counts['confirmed'] }}</div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <table class="bookings-table">
            <thead>
                <tr>
                    <th>Confirmation</th>
                    <th>Guest Name</th>
                    <th>Property</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Nights</th>
                    <th>Amount</th>
                    <th>Advance</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->confirmation_number }}</td>
                    <td>{{ $booking->guest->name }}</td>
                    <td>{{ $booking->accommodation->property->name }}</td>
                    <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                    <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                    <td>{{ $booking->check_in_date->diffInDays($booking->check_out_date) }}</td>
                    <td>₹{{ number_format($booking->total_amount, 2) }}</td>
                    <td>₹{{ number_format($booking->advance_paid, 2) }}</td>
                    <td>₹{{ number_format($booking->balance_pending, 2) }}</td>
                    <td>
                        <span class="status-badge">{{ ucfirst($booking->status) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <h3>Grand Total</h3>
            <div class="total-amount">₹{{ number_format($total_amount, 2) }}</div>
            <div style="font-size: 12px; opacity: 0.9;">
                Advance: ₹{{ number_format($total_advance_paid, 2) }} | 
                Balance: ₹{{ number_format($total_balance_pending, 2) }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Bulk Invoice Report - {{ $property_owner->name ?? 'Hospitality Management' }}</strong></p>
            <p>This report contains {{ $total_bookings }} booking(s) with a total value of ₹{{ number_format($total_amount, 2) }}</p>
            <p>Generated on {{ $generated_date }} | Report ID: {{ $invoice_number }}</p>
            <p>This is a computer-generated report and does not require a signature.</p>
        </div>
    </div>
</body>
</html>
