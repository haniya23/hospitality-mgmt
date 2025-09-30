<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice Report</title>

    {{-- 
        NOTE FOR DOMPDF: For the 'Poppins' font to work, you must install the TTF font files 
        and configure them in your dompdf settings.
    --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'DejaVu Sans', sans-serif;
            background-color: #ffffff;
            font-size: 9px;
            color: #555;
        }

        .invoice-container {
            width: 100%;
            position: relative;
            background: #fff;
        }
        
        /* --- Shared Header Style --- */
        .header {
            color: #ffffff;
            padding: 40px 50px;
            position: relative;
            height: 180px;
            page-break-after: avoid;
        }

        .header-background {
            position: absolute;
            top: -100px;
            left: -100px;
            width: 650px;
            height: 300px;
            background-color:rgb(143, 183, 181);
            border-bottom-right-radius: 200px;
            z-index: -1;
        }

        .header h1 {
            font-size: 36px;
            margin: 0;
            font-weight: 700;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 11px;
        }

        .company-info {
            position: absolute;
            top: 40px;
            right: 50px;
            text-align: right;
        }

        .company-info h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            color: #333;
        }

        .company-info p {
            font-size: 10px;
            color: #888;
            margin: 0;
        }

        /* --- Summary Section --- */
        .summary-section {
            padding: 20px 50px 10px;
            page-break-after: avoid;
        }
        .summary-section h3 {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .summary-table {
            width: 100%;
            border-spacing: 10px 0;
            border-collapse: separate;
        }
        .summary-item {
            width: 33.33%;
            background-color: #F0FDFB;
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
        }
        .summary-item .label {
            font-size: 9px;
            font-weight: 500;
            color: #00A99D;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .summary-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        /* --- Bookings Table Section --- */
        .bookings-section {
            padding: 20px 50px;
            page-break-before: auto;
        }
        .bookings-table-header {
            background-color: #00A99D;
            color: #ffffff;
            padding: 10px 15px;
            font-weight: 600;
            font-size: 10px;
            border-radius: 8px;
        }
        .bookings-table-header table,
        .booking-row-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .booking-row {
            background-color: #F8F8F8;
            border-radius: 8px;
            padding: 8px 15px;
            margin-top: 8px;
        }
        .booking-row:nth-child(even) {
            background-color: #ffffff;
            border: 1px solid #f0f0f0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-confirmed { background-color: #D1FAE5; color: #065F46; }
        .status-pending { background-color: #FEF3C7; color: #92400E; }
        .status-cancelled { background-color: #FEE2E2; color: #991B1B; }

        /* --- Footer --- */
        .footer {
            padding: 30px 50px;
            position: relative;
            width: 100%;
            page-break-before: auto;
            page-break-inside: avoid;
        }
        
        .footer-background {
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background-color: #00A99D;
            border-top-left-radius: 100px;
            z-index: 0;
        }
        .footer-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }
         .footer-content p {
            font-size: 9px;
            color: #888;
            margin: 2px 0;
        }
    </style>
</head>
<body>

    <div class="invoice-container">
        
        <div class="header">
            <div class="header-background"></div>
            <h1>BULK INVOICE REPORT</h1>
            <p>Report No: {{ $invoice_number }}</p>
            <p>Generated on: {{ $generated_date }}</p>

            <div class="company-info">
                <h2>{{ $property_owner->name ?? 'Hospitality Management' }}</h2>
                <p>Your Trusted Hospitality Partner</p>
            </div>
        </div>

        <div class="summary-section">
            <h3>Report Summary</h3>
            <table class="summary-table">
                <tr>
                    <td class="summary-item">
                        <div class="label">Total Bookings</div>
                        <div class="value">{{ $total_bookings }}</div>
                    </td>
                    <td class="summary-item">
                        <div class="label">Total Amount</div>
                        <div class="value">Rs. {{ number_format($total_amount, 2) }}</div>
                    </td>
                     <td class="summary-item">
                        <div class="label">Balance Pending</div>
                        <div class="value">Rs. {{ number_format($total_balance_pending, 2) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="bookings-section">
            <div class="bookings-table-header">
                <table>
                    <tr>
                        <th style="width: 12%;">Confirmation</th>
                        <th style="width: 15%;">Guest Name</th>
                        <th style="width: 15%;">Property</th>
                        <th style="width: 10%;">Check-in</th>
                        <th style="width: 12%;">Amount</th>
                        <th style="width: 12%;">Balance</th>
                        <th style="width: 10%; text-align:center;">Status</th>
                    </tr>
                </table>
            </div>

            @foreach($bookings as $booking)
            <div class="booking-row">
                <table class="booking-row-table">
                    <tr>
                        <td style="width: 12%;">{{ $booking->confirmation_number }}</td>
                        <td style="width: 15%;">{{ $booking->guest->name }}</td>
                        <td style="width: 15%;">{{ $booking->accommodation->property->name }}</td>
                        <td style="width: 10%;">{{ $booking->check_in_date->format('d/m/Y') }}</td>
                        <td style="width: 12%;">&#8377;{{ number_format($booking->total_amount, 2) }}</td>
                        <td style="width: 12%;">&#8377;{{ number_format($booking->balance_pending, 2) }}</td>
                        <td style="width: 10%; text-align:center;">
                            <span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            @endforeach
        </div>

        <div class="footer">
            <div class="footer-background"></div>
            <div class="footer-content">
                <p><strong>Bulk Invoice Report - {{ $property_owner->name ?? 'Hospitality Management' }}</strong></p>
                <p>This report contains {{ $total_bookings }} booking(s) with a total value of &#8377;{{ number_format($total_amount, 2) }}</p>
                <p>This is a computer-generated report and does not require a signature.</p>
            </div>
        </div>

    </div>

</body>
</html>